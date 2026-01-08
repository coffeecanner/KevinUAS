<?php

namespace App\Http\Controllers;

use App\Models\Pendaftaran;
use App\Models\DaftarUlang;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PendaftaranController extends Controller
{
    /**
     * List pendaftaran
     * ❗ KHUSUS untuk dropdown Daftar Ulang:
     * hanya yang BELUM ada di tabel daftar_ulang
     */
    public function index()
    {
        // ambil semua no_daftar yang sudah daftar ulang
        $usedNoDaftar = DaftarUlang::pluck('no_daftar')->toArray();

        $list = Pendaftaran::whereNotIn('no_daftar', $usedNoDaftar)
            ->orderBy('no_daftar')
            ->get()
            ->map(function ($p) {
                $tanggal_daftar = $p->tanggal_daftar
                    ? Carbon::parse($p->tanggal_daftar)
                    : null;

                $tanggal_hadir = $p->tanggal_hadir
                    ? Carbon::parse($p->tanggal_hadir)
                    : null;

                return [
                    'no_daftar' => $p->no_daftar,
                    'nama_pemohon' => $p->nama_pemohon,

                    // UI display
                    'tanggal_daftar' => $tanggal_daftar
                        ? $tanggal_daftar->locale('id')->translatedFormat('d-M-Y')
                        : null,

                    'hari' => $tanggal_hadir
                        ? $tanggal_hadir->locale('id')->translatedFormat('l')
                        : ($p->hari ?? null),

                    // 🔥 ISO untuk input date
                    'tanggal_hadir' => $tanggal_hadir
                        ? $tanggal_hadir->format('Y-m-d')
                        : null,

                    'jam_hadir' => $p->jam_hadir
                        ? Carbon::parse($p->jam_hadir)->format('H:i')
                        : null,
                ];
            });

        return response()->json($list);
    }

    /**
     * Simpan pendaftaran baru
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_pemohon'   => 'required|string',
            'tanggal_daftar' => 'required|date',
        ]);

        // atur jadwal hadir (maks 5 per hari)
        $start = Carbon::parse($data['tanggal_daftar']);
        $candidate = $start->copy();

        while (true) {
            $count = Pendaftaran::whereDate('tanggal_hadir', $candidate->toDateString())->count();
            if ($count < 5) break;
            $candidate->addDay();
        }

        $p = Pendaftaran::create([
            'nama_pemohon'   => $data['nama_pemohon'],
            'tanggal_daftar' => $start->toDateString(),
            'hari'           => $candidate->format('l'),
            'tanggal_hadir'  => $candidate->toDateString(),
            'jam_hadir'      => '09:00:00',
        ]);

        $td = Carbon::parse($p->tanggal_daftar);
        $th = Carbon::parse($p->tanggal_hadir);

        return response()->json([
            'no_daftar'      => $p->no_daftar,
            'nama_pemohon'   => $p->nama_pemohon,
            'tanggal_daftar' => $td->locale('id')->translatedFormat('d-M-Y'),
            'hari'           => $th->locale('id')->translatedFormat('l'),
            'tanggal_hadir'  => $th->format('Y-m-d'),
            'jam_hadir'      => $p->jam_hadir,
        ], 201);
    }

    /**
     * Detail pendaftaran
     */
    public function show($no_daftar)
    {
        $p = Pendaftaran::findOrFail($no_daftar);

        $td = $p->tanggal_daftar ? Carbon::parse($p->tanggal_daftar) : null;
        $th = $p->tanggal_hadir ? Carbon::parse($p->tanggal_hadir) : null;

        return response()->json([
            'no_daftar'      => $p->no_daftar,
            'nama_pemohon'   => $p->nama_pemohon,
            'tanggal_daftar' => $td ? $td->locale('id')->translatedFormat('d-M-Y') : null,
            'hari'           => $th ? $th->locale('id')->translatedFormat('l') : null,
            'tanggal_hadir'  => $th ? $th->format('Y-m-d') : null,
            'jam_hadir'      => $p->jam_hadir,
        ]);
    }

    /**
     * Update pendaftaran
     */
    public function update(Request $request, $no_daftar)
    {
        $p = Pendaftaran::findOrFail($no_daftar);

        $data = $request->validate([
            'nama_pemohon'   => 'sometimes|required|string',
            'tanggal_daftar' => 'sometimes|required|date',
        ]);

        if (isset($data['tanggal_daftar'])) {
            $start = Carbon::parse($data['tanggal_daftar']);
            $candidate = $start->copy();

            while (true) {
                $count = Pendaftaran::whereDate('tanggal_hadir', $candidate->toDateString())
                    ->where('no_daftar', '!=', $p->no_daftar)
                    ->count();
                if ($count < 5) break;
                $candidate->addDay();
            }

            $p->tanggal_daftar = $start->toDateString();
            $p->hari           = $candidate->format('l');
            $p->tanggal_hadir  = $candidate->toDateString();
            $p->jam_hadir      = '09:00:00';
        }

        if (isset($data['nama_pemohon'])) {
            $p->nama_pemohon = $data['nama_pemohon'];
        }

        $p->save();

        return response()->json(['updated' => true]);
    }

    /**
     * Live search pendaftaran (TIDAK difilter)
     * dipakai di halaman Pendaftaran
     */
    public function search(Request $request)
    {
        $q = trim($request->get('q', ''));

        $list = Pendaftaran::when($q, function ($qb) use ($q) {
            $qb->where(function ($s) use ($q) {
                $s->where('nama_pemohon', 'like', "%{$q}%")
                  ->orWhere('no_daftar', 'like', "%{$q}%")
                  ->orWhere('hari', 'like', "%{$q}%")
                  ->orWhere('tanggal_daftar', 'like', "%{$q}%")
                  ->orWhere('tanggal_hadir', 'like', "%{$q}%")
                  ->orWhere('jam_hadir', 'like', "%{$q}%");
            });
        })
        ->orderBy('no_daftar')
        ->limit(50)
        ->get()
        ->map(function ($p) {
            $td = $p->tanggal_daftar ? Carbon::parse($p->tanggal_daftar) : null;
            $th = $p->tanggal_hadir ? Carbon::parse($p->tanggal_hadir) : null;

            return [
                'no_daftar'      => $p->no_daftar,
                'nama_pemohon'   => $p->nama_pemohon,
                'tanggal_daftar' => $td ? $td->locale('id')->translatedFormat('d-M-Y') : null,
                'hari'           => $th ? $th->locale('id')->translatedFormat('l') : null,
                'tanggal_hadir'  => $th ? $th->format('Y-m-d') : null,
                'jam_hadir'      => $p->jam_hadir,
            ];
        });

        return response()->json($list);
    }

    /**
     * Hapus pendaftaran
     */
    public function destroy($no_daftar)
    {
        $p = Pendaftaran::findOrFail($no_daftar);
        $p->delete();
        return response()->json(['deleted' => true]);
    }
}
