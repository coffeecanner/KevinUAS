<?php

namespace App\Http\Controllers;

use App\Models\Pengurusan;
use App\Models\DaftarUlang;
use Illuminate\Http\Request;

class PengurusanController extends Controller
{
    public function index()
    {
        $list = Pengurusan::orderBy('no_antrian')->get();
        $total = Pengurusan::where('status', 'Diterima')->sum('pembayaran');
        return response()->json(['data' => $list, 'total_pendapatan' => $total]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'daftar_ulang_id' => 'required|integer|exists:daftar_ulang,id',
        ]);

        $du = DaftarUlang::findOrFail($data['daftar_ulang_id']);

        if (!$du->no_antrian) {
            return response()->json(['error' => 'Entry tidak punya no_antrian'], 422);
        }

        $allBerkas = ($du->ktp && $du->kk && $du->ijazah_akta);
        if ($allBerkas) {
            $berkas = 'Lengkap';
            $status = 'Diterima';
            $keterangan = 'OK';
            $pembayaran = 355000;
        } else {
            $berkas = 'Tidak Lengkap';
            $status = 'Ditolak';
            $keterangan = 'Tidak Lengkap';
            $pembayaran = 0;
        }

        $p = Pengurusan::create([
            'no_antrian' => $du->no_antrian,
            'no_daftar' => $du->no_daftar,
            'nama_pemohon' => $du->nama_pemohon,
            'berkas' => $berkas,
            'status' => $status,
            'keterangan' => $keterangan,
            'pembayaran' => $pembayaran,
        ]);

        return response()->json($p, 201);
    }

    public function show($id)
    {
        $p = Pengurusan::findOrFail($id);
        return response()->json($p);
    }

    public function destroy($id)
    {
        $p = Pengurusan::findOrFail($id);
        $p->delete();
        return response()->json(['deleted' => true]);
    }
}
