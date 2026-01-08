<?php

namespace App\Http\Controllers;

use App\Models\Pengurusan;
use App\Models\DaftarUlang;
use Illuminate\Http\Request;

class PengurusanController extends Controller
{
    /* ================= LIST ================= */
    public function index()
    {
        return response()->json([
            'data' => Pengurusan::orderBy('no_antrian')->get(),
            'total_pendapatan' => Pengurusan::where('status', 'Diterima')->sum('pembayaran')
        ]);
    }

    /* ================= SEARCH ================= */
    public function search(Request $request)
    {
        $q = trim($request->q);

        $list = Pengurusan::where(function ($qb) use ($q) {
            $qb->where('nama_pemohon', 'like', "%$q%")
               ->orWhere('no_antrian', 'like', "%$q%")
               ->orWhere('status', 'like', "%$q%");
        })->orderBy('no_antrian')->get();

        return response()->json([
            'data' => $list,
            'total_pendapatan' => $list->where('status','Diterima')->sum('pembayaran')
        ]);
    }

    /* ================= MASUK ANTRIAN ================= */
    public function store(Request $request)
    {
        $du = DaftarUlang::findOrFail($request->daftar_ulang_id);

        if ($du->keterangan !== 'OK' || !$du->no_antrian) {
            return response()->json(['error' => 'Daftar ulang belum valid'], 422);
        }

        if (Pengurusan::where('no_antrian', $du->no_antrian)->exists()) {
            return response()->json(['error' => 'Sudah masuk pengurusan'], 409);
        }

        return response()->json(
            Pengurusan::create([
                'no_antrian'   => $du->no_antrian,
                'no_daftar'    => $du->no_daftar,
                'nama_pemohon' => $du->nama_pemohon,
                'berkas'       => 'Lengkap',
                'status'       => 'Menunggu',
                'keterangan'   => 'Antri',
                'pembayaran'   => 0,
            ]),
            201
        );
    }

    /* ================= CURRENT ================= */
    public function current()
    {
        $current = Pengurusan::where('status', 'Diproses')->first();
        if ($current) return response()->json($current);

        $next = Pengurusan::where('status', 'Menunggu')
            ->orderBy('no_antrian')
            ->first();

        if ($next) {
            $next->update(['status' => 'Diproses']);
            return response()->json($next);
        }

        return response()->json(null);
    }

    /* ================= SELESAI + NEXT ================= */
    public function selesaiDanNext($id)
    {
        $current = Pengurusan::findOrFail($id);

        if ($current->status !== 'Diproses') {
            return response()->json(['error'=>'Status tidak valid'], 422);
        }

        $current->update([
            'status'     => 'Diterima',
            'keterangan' => 'OK',
            'pembayaran' => 355000
        ]);

        $next = Pengurusan::where('status','Menunggu')
            ->orderBy('no_antrian')
            ->first();

        if ($next) $next->update(['status'=>'Diproses']);

        return response()->json([
            'selesai' => $current,
            'next' => $next
        ]);
    }

    /* ================= UPDATE ================= */
    public function update(Request $request, $id)
    {
        $p = Pengurusan::findOrFail($id);
        $p->update($request->only(['status','keterangan','pembayaran','berkas']));
        return response()->json($p);
    }

    /* ================= DELETE ================= */
    public function destroy($id)
    {
        Pengurusan::findOrFail($id)->delete();
        return response()->json(['deleted'=>true]);
    }
}
