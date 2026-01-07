<?php

namespace App\Http\Controllers;

use App\Models\Pendaftaran;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PendaftaranController extends Controller
{
    public function index()
    {
        // list semua pendaftaran - return with formatted dates for UI (Indonesian day name, dd-MMM-yyyy)
        $list = Pendaftaran::orderBy('no_daftar')->get()->map(function($p){
            // ensure Carbon instances
            $tanggal_daftar = $p->tanggal_daftar ? \Carbon\Carbon::parse($p->tanggal_daftar) : null;
            $tanggal_hadir = $p->tanggal_hadir ? \Carbon\Carbon::parse($p->tanggal_hadir) : null;

            return [
                'no_daftar' => $p->no_daftar,
                'nama_pemohon' => $p->nama_pemohon,
                'tanggal_daftar' => $tanggal_daftar ? $tanggal_daftar->locale('id')->translatedFormat('d-M-Y') : null,
                'tanggal_daftar_raw' => $p->tanggal_daftar,
                'hari' => $tanggal_hadir ? $tanggal_hadir->locale('id')->translatedFormat('l') : ($p->hari ?? null),
                'tanggal_hadir' => $tanggal_hadir ? $tanggal_hadir->locale('id')->translatedFormat('d-M-Y') : null,
                'tanggal_hadir_raw' => $p->tanggal_hadir,
                'jam_hadir' => $p->jam_hadir ? \Carbon\Carbon::parse($p->jam_hadir)->format('H:i') : null,
            ];
        });

        return response()->json($list);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_pemohon' => 'required|string',
            'tanggal_daftar' => 'required|date',
        ]);

        // jadwalkan tanggal_hadir dengan kuota 5 per hari
        $start = Carbon::parse($data['tanggal_daftar']);
        $candidate = $start->copy();

        while (true) {
            $count = Pendaftaran::whereDate('tanggal_hadir', $candidate->toDateString())->count();
            if ($count < 5) {
                break;
            }
            $candidate->addDay();
        }

        $p = Pendaftaran::create([
            'nama_pemohon' => $data['nama_pemohon'],
            'tanggal_daftar' => $start->toDateString(),
            'hari' => $candidate->format('l'),
            'tanggal_hadir' => $candidate->toDateString(),
            'jam_hadir' => '09:00:00',
        ]);
        // return formatted response (Indonesian)
        $td = \Carbon\Carbon::parse($p->tanggal_daftar);
        $th = \Carbon\Carbon::parse($p->tanggal_hadir);
        return response()->json([
            'no_daftar' => $p->no_daftar,
            'nama_pemohon' => $p->nama_pemohon,
            'tanggal_daftar' => $td->locale('id')->translatedFormat('d-M-Y'),
            'tanggal_daftar_raw' => $p->tanggal_daftar,
            'hari' => $th->locale('id')->translatedFormat('l'),
            'tanggal_hadir' => $th->locale('id')->translatedFormat('d-M-Y'),
            'tanggal_hadir_raw' => $p->tanggal_hadir,
            'jam_hadir' => $p->jam_hadir ? \Carbon\Carbon::parse($p->jam_hadir)->format('H:i') : null,
        ], 201);
    }

    public function show($no_daftar)
    {
        $p = Pendaftaran::findOrFail($no_daftar);
        $td = $p->tanggal_daftar ? \Carbon\Carbon::parse($p->tanggal_daftar) : null;
        $th = $p->tanggal_hadir ? \Carbon\Carbon::parse($p->tanggal_hadir) : null;
        return response()->json([
            'no_daftar' => $p->no_daftar,
            'nama_pemohon' => $p->nama_pemohon,
            'tanggal_daftar' => $td ? $td->locale('id')->translatedFormat('d-M-Y') : null,
            'hari' => $th ? $th->locale('id')->translatedFormat('l') : ($p->hari ?? null),
            'tanggal_hadir' => $th ? $th->locale('id')->translatedFormat('d-M-Y') : null,
            'jam_hadir' => $p->jam_hadir ? \Carbon\Carbon::parse($p->jam_hadir)->format('H:i') : null,
        ]);
    }

    public function update(Request $request, $no_daftar)
    {
        $p = Pendaftaran::findOrFail($no_daftar);
        $data = $request->validate([
            'nama_pemohon' => 'sometimes|required|string',
            'tanggal_daftar' => 'sometimes|required|date',
        ]);

        // If tanggal_daftar changed, recompute schedule
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
            $p->hari = $candidate->format('l');
            $p->tanggal_hadir = $candidate->toDateString();
            $p->jam_hadir = '09:00:00';
        }

        if (isset($data['nama_pemohon'])) $p->nama_pemohon = $data['nama_pemohon'];

        $p->save();
        $td = $p->tanggal_daftar ? \Carbon\Carbon::parse($p->tanggal_daftar) : null;
        $th = $p->tanggal_hadir ? \Carbon\Carbon::parse($p->tanggal_hadir) : null;
        return response()->json([
            'no_daftar' => $p->no_daftar,
            'nama_pemohon' => $p->nama_pemohon,
            'tanggal_daftar' => $td ? $td->locale('id')->translatedFormat('d-M-Y') : null,
            'hari' => $th ? $th->locale('id')->translatedFormat('l') : ($p->hari ?? null),
            'tanggal_hadir' => $th ? $th->locale('id')->translatedFormat('d-M-Y') : null,
            'jam_hadir' => $p->jam_hadir ? \Carbon\Carbon::parse($p->jam_hadir)->format('H:i') : null,
        ]);
    }

    /**
     * Live search endpoint for pendaftar by name
     */
    public function search(Request $request)
    {
        $q = trim($request->get('q', ''));

        // If the query looks like a number, try exact match on no_daftar first
        $exact = collect();
        if (ctype_digit($q)) {
            $exact = Pendaftaran::where('no_daftar', $q)->get();
        }

        $fuzzy = Pendaftaran::when($q, function($qb) use ($q) {
            $qb->where(function($s) use ($q) {
                $s->where('nama_pemohon', 'like', "%{$q}%")
                  ->orWhere('no_daftar', 'like', "%{$q}%")
                  ->orWhere('hari', 'like', "%{$q}%")
                  ->orWhere('tanggal_daftar', 'like', "%{$q}%")
                  ->orWhere('tanggal_hadir', 'like', "%{$q}%")
                  ->orWhere('jam_hadir', 'like', "%{$q}%");
            });
        })->orderBy('no_daftar')->limit(50)->get();

        // Merge exact first (if any), then fuzzy results without duplicating exacts
        if ($exact->isNotEmpty()) {
            $exactIds = $exact->pluck('no_daftar')->all();
            $fuzzy = $fuzzy->reject(function($item) use ($exactIds){ return in_array($item->no_daftar, $exactIds); })->values();
            $merged = $exact->concat($fuzzy);
        } else {
            $merged = $fuzzy;
        }

        $list = $merged->map(function($p){
            $td = $p->tanggal_daftar ? \Carbon\Carbon::parse($p->tanggal_daftar) : null;
            $th = $p->tanggal_hadir ? \Carbon\Carbon::parse($p->tanggal_hadir) : null;
            return [
                'no_daftar' => $p->no_daftar,
                'nama_pemohon' => $p->nama_pemohon,
                'tanggal_daftar' => $td ? $td->locale('id')->translatedFormat('d-M-Y') : null,
                'hari' => $th ? $th->locale('id')->translatedFormat('l') : ($p->hari ?? null),
                'tanggal_hadir' => $th ? $th->locale('id')->translatedFormat('d-M-Y') : null,
                'jam_hadir' => $p->jam_hadir ? \Carbon\Carbon::parse($p->jam_hadir)->format('H:i') : null,
            ];
        });

        return response()->json($list);
    }

    public function destroy($no_daftar)
    {
        $p = Pendaftaran::findOrFail($no_daftar);
        $p->delete();
        return response()->json(['deleted' => true]);
    }
}
