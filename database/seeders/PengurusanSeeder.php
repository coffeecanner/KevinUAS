<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pengurusan;
use App\Models\DaftarUlang;

class PengurusanSeeder extends Seeder
{
    public function run()
    {
        $dus = DaftarUlang::whereNotNull('no_antrian')->get();

        foreach ($dus as $du) {
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

            Pengurusan::create([
                'no_antrian' => $du->no_antrian,
                'no_daftar' => $du->no_daftar,
                'nama_pemohon' => $du->nama_pemohon,
                'berkas' => $berkas,
                'status' => $status,
                'keterangan' => $keterangan,
                'pembayaran' => $pembayaran,
            ]);
        }
    }
}
