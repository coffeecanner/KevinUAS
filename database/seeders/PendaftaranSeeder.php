<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pendaftaran;
use Carbon\Carbon;

class PendaftaranSeeder extends Seeder
{
    public function run()
    {
        $today = Carbon::today();

        // Create 8 sample pendaftar on same tanggal_daftar to test scheduling
        for ($i = 1; $i <= 8; $i++) {
            Pendaftaran::create([
                'nama_pemohon' => 'Pemohon ' . $i,
                'tanggal_daftar' => $today->toDateString(),
                // scheduling happens in controller normally; here we emulate by assigning
                'hari' => $today->copy()->addDays(intval(($i - 1) / 5))->format('l'),
                'tanggal_hadir' => $today->copy()->addDays(intval(($i - 1) / 5))->toDateString(),
                'jam_hadir' => '09:00:00',
            ]);
        }
    }
}
