<?php

namespace App\Http\Controllers;

use App\Models\DaftarUlang;
use App\Models\Pendaftaran;
use App\Models\Pengurusan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DaftarUlangController extends Controller
{
    public function index()
    {
        $list = DaftarUlang::orderBy('id')->get()->map(function($du){
            $processed = Pengurusan::where('no_daftar', $du->no_daftar)->orWhere('no_antrian', $du->no_antrian)->exists();
            return array_merge($du->toArray(), ['processed' => $processed]);
        });
        return response()->json($list);
    }

    /**
     * Search daftar ulang by name or no_daftar
     */
    public function search(Request $request)
    {
        $q = trim($request->query('q', ''));
        if ($q === '') return response()->json([]);

        // If numeric, prefer exact matches on no_daftar or no_antrian first
        $exact = collect();
        if (ctype_digit($q)) {
            $exact = DaftarUlang::where('no_daftar', $q)->orWhere('no_antrian', $q)->get();
        }

        $fuzzy = DaftarUlang::where(function($qb) use ($q) {
            $qb->where('nama_pemohon', 'like', "%{$q}%")
               ->orWhere('no_daftar', 'like', "%{$q}%")
               ->orWhere('hari_harus_datang', 'like', "%{$q}%")
               ->orWhere('tanggal_harus_datang', 'like', "%{$q}%")
               ->orWhere('keterangan', 'like', "%{$q}%")
               ->orWhere('no_antrian', 'like', "%{$q}%");
        })->orderBy('id')->get();

        if ($exact->isNotEmpty()) {
            $exactIds = $exact->pluck('id')->all();
            $fuzzy = $fuzzy->reject(function($item) use ($exactIds){ return in_array($item->id, $exactIds); })->values();
            $merged = $exact->concat($fuzzy);
        } else {
            $merged = $fuzzy;
        }

        return response()->json($merged);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'no_daftar' => 'required|integer|exists:pendaftaran,no_daftar',
            'nama_pemohon' => 'required|string',
            'hari_harus_datang' => 'required|string',
            'tanggal_harus_datang' => 'required|date',
            'ktp' => 'sometimes|boolean',
            'kk' => 'sometimes|boolean',
            'ijazah_akta' => 'sometimes|boolean',
        ]);

        $p = Pendaftaran::findOrFail($data['no_daftar']);

        $hasAnyBerkas = ($request->boolean('ktp') || $request->boolean('kk') || $request->boolean('ijazah_akta'));

        // Matching rule: only compare tanggal_harus_datang (date) with scheduled tanggal_hadir
        $matches = false;
        if ($p->tanggal_hadir) {
            $matches = ($data['tanggal_harus_datang'] == $p->tanggal_hadir->format('Y-m-d'));
        }

        $keterangan = 'TIDAK';
        $no_antrian = null;

        if ($matches && $hasAnyBerkas) {
            $keterangan = 'OK';
            // generate no_antrian automatically in a DB transaction with lock to avoid race conditions
            DB::beginTransaction();
            try {
                $max = DB::table('daftar_ulang')->whereNotNull('no_antrian')->lockForUpdate()->max('no_antrian');
                $no_antrian = ($max ? $max + 1 : 1);
                DB::commit();
            } catch (\Throwable $e) {
                DB::rollBack();
                // fallback without lock
                $max = DaftarUlang::whereNotNull('no_antrian')->max('no_antrian');
                $no_antrian = ($max ? $max + 1 : 1);
            }
        }

        $du = DaftarUlang::create([
            'no_daftar' => $data['no_daftar'],
            'nama_pemohon' => $data['nama_pemohon'],
            'hari_harus_datang' => $data['hari_harus_datang'],
            'tanggal_harus_datang' => $data['tanggal_harus_datang'],
            'ktp' => $request->boolean('ktp'),
            'kk' => $request->boolean('kk'),
            'ijazah_akta' => $request->boolean('ijazah_akta'),
            'keterangan' => $keterangan,
            'no_antrian' => $no_antrian,
        ]);

        return response()->json($du, 201);
    }

    public function show($id)
    {
        $du = DaftarUlang::findOrFail($id);
        return response()->json($du);
    }

    public function update(Request $request, $id)
    {
        $du = DaftarUlang::findOrFail($id);
        $data = $request->only(['nama_pemohon','hari_harus_datang','tanggal_harus_datang','ktp','kk','ijazah_akta']);
        if (isset($data['ktp'])) $du->ktp = (bool)$data['ktp'];
        if (isset($data['kk'])) $du->kk = (bool)$data['kk'];
        if (isset($data['ijazah_akta'])) $du->ijazah_akta = (bool)$data['ijazah_akta'];
        if (isset($data['nama_pemohon'])) $du->nama_pemohon = $data['nama_pemohon'];
        if (isset($data['hari_harus_datang'])) $du->hari_harus_datang = $data['hari_harus_datang'];
        if (isset($data['tanggal_harus_datang'])) $du->tanggal_harus_datang = $data['tanggal_harus_datang'];

        // Re-evaluate keterangan and antrian
        $p = Pendaftaran::find($du->no_daftar);
        $hasAnyBerkas = ($du->ktp || $du->kk || $du->ijazah_akta);
        // match only by tanggal_harus_datang (date)
        $matches = false;
        if ($p && $p->tanggal_hadir) {
            $matches = ($du->tanggal_harus_datang == \Carbon\Carbon::parse($p->tanggal_hadir)->format('Y-m-d'));
        }

        if ($matches && $hasAnyBerkas) {
            if ($du->keterangan !== 'OK') {
                // assign no_antrian safely inside a transaction
                DB::beginTransaction();
                try {
                    $max = DB::table('daftar_ulang')->whereNotNull('no_antrian')->lockForUpdate()->max('no_antrian');
                    $du->no_antrian = ($max ? $max + 1 : 1);
                    DB::commit();
                } catch (\Throwable $e) {
                    DB::rollBack();
                    $max = DaftarUlang::whereNotNull('no_antrian')->max('no_antrian');
                    $du->no_antrian = ($max ? $max + 1 : 1);
                }
            }
            $du->keterangan = 'OK';
        } else {
            $du->keterangan = 'TIDAK';
            $du->no_antrian = null;
        }

        $du->save();
        return response()->json($du);
    }

    public function destroy($id)
    {
        $du = DaftarUlang::findOrFail($id);
        $du->delete();
        return response()->json(['deleted' => true]);
    }
}
