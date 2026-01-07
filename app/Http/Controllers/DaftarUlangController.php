<?php

namespace App\Http\Controllers;

use App\Models\DaftarUlang;
use App\Models\Pendaftaran;
use Illuminate\Http\Request;

class DaftarUlangController extends Controller
{
    public function index()
    {
        $list = DaftarUlang::orderBy('id')->get();
        return response()->json($list);
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

        $isMatchingSchedule = (
            $p->tanggal_hadir->format('Y-m-d') ?? $p->tanggal_hadir
        );

        // Compare provided tanggal_harus_datang and hari_harus_datang with scheduled
        $matches = ($data['tanggal_harus_datang'] == $p->tanggal_hadir->format('Y-m-d') || $data['tanggal_harus_datang'] == $p->tanggal_hadir);
        $matches = $matches && ($data['hari_harus_datang'] == $p->hari);

        $keterangan = 'TIDAK';
        $no_antrian = null;

        if ($matches && $hasAnyBerkas) {
            $keterangan = 'OK';
            // generate no_antrian otomatis
            $max = DaftarUlang::whereNotNull('no_antrian')->max('no_antrian');
            $no_antrian = ($max ? $max + 1 : 1);
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
        $matches = ($du->tanggal_harus_datang == $p->tanggal_hadir) && ($du->hari_harus_datang == $p->hari);

        if ($matches && $hasAnyBerkas) {
            if ($du->keterangan !== 'OK') {
                $max = DaftarUlang::whereNotNull('no_antrian')->max('no_antrian');
                $du->no_antrian = ($max ? $max + 1 : 1);
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
