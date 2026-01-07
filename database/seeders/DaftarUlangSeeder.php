<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DaftarUlang;
use App\Models\Pendaftaran;

class DaftarUlangSeeder extends Seeder
{
    public function run()
    {
        $pList = Pendaftaran::orderBy('no_daftar')->get();

        foreach ($pList as $idx => $p) {
            // make some entries OK and some not
            $ktp = ($idx % 2 == 0);
            $kk = true;
            $ijazah = ($idx % 3 != 0);

            $matches = true; // we set tanggal_harus_datang equal to schedule for some

            DaftarUlang::create([
                'no_daftar' => $p->no_daftar,
                'nama_pemohon' => $p->nama_pemohon,
                'hari_harus_datang' => $p->hari,
                'tanggal_harus_datang' => $p->tanggal_hadir,
                'ktp' => $ktp,
                'kk' => $kk,
                'ijazah_akta' => $ijazah,
                'keterangan' => ($matches && ($ktp || $kk || $ijazah)) ? 'OK' : 'TIDAK',
                'no_antrian' => null, // controller would assign; leave null here
            ]);
        }
    }
}
