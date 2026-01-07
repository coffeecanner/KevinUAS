<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pendaftaran;
use App\Models\DaftarUlang;
use App\Models\Pengurusan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class VariedDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = \Faker\Factory::create('id_ID');

        // Safety: only run destructive truncation in local environment
        if (! app()->environment('local')) {
            if ($this->command) {
                $this->command->warn('VariedDataSeeder: destructive truncation skipped because APP_ENV != local');
            }
            return;
        }

        // Disable FK constraints during truncate to avoid MySQL errors
        Schema::disableForeignKeyConstraints();
        // Truncate child tables first to respect FK relationships
        Pengurusan::truncate();
        DaftarUlang::truncate();
        Pendaftaran::truncate();
        Schema::enableForeignKeyConstraints();

        $assignedPerDate = []; // date (Y-m-d) => count
        $antrianCounter = 1;

        $pendaftaranRows = [];

        // Create 50 pendaftar with varied tanggal and realistic names
        for ($i = 0; $i < 50; $i++) {
            $nama = $faker->name();
            // tanggal_daftar: random within past 30 days to next 7 days
            $offset = rand(-30, 7);
            $tanggal_daftar = Carbon::today()->copy()->addDays($offset);

            // schedule tanggal_hadir with quota 5 per day
            $candidate = $tanggal_daftar->copy();
            while (true) {
                $key = $candidate->toDateString();
                $count = $assignedPerDate[$key] ?? 0;
                if ($count < 5) break;
                $candidate->addDay();
            }
            // assign
            $key2 = $candidate->toDateString();
            $assignedPerDate[$key2] = ($assignedPerDate[$key2] ?? 0) + 1;

            $p = Pendaftaran::create([
                'nama_pemohon' => $nama,
                'tanggal_daftar' => $tanggal_daftar->toDateString(),
                'hari' => $candidate->format('l'),
                'tanggal_hadir' => $candidate->toDateString(),
                'jam_hadir' => sprintf('%02d:00:00', 9 + ($assignedPerDate[$key2] - 1) % 6),
            ]);

            $pendaftaranRows[] = $p;
        }

        // Create daftar ulang for ~70% of pendaftar, with varied berkas and possible mismatches
        foreach ($pendaftaranRows as $p) {
            if (rand(1, 100) > 70) continue; // ~30% skip

            $matchSchedule = rand(1, 100) <= 75; // 75% will match scheduled hari/tanggal
            $hari = $p->hari;
            $tanggal = $p->tanggal_hadir;
            if (!$matchSchedule) {
                // choose a nearby date (+/- 1-3 days)
                $delta = rand(1, 3);
                $choose = (rand(0,1) ? Carbon::parse($tanggal)->addDays($delta) : Carbon::parse($tanggal)->subDays($delta));
                $hari = $choose->format('l');
                $tanggal = $choose->toDateString();
            }

            // berkas presence probabilities
            $ktp = rand(1,100) <= 90; // 90%
            $kk = rand(1,100) <= 85;  // 85%
            $ijazah = rand(1,100) <= 60; // 60%

            $hasAny = ($ktp || $kk || $ijazah);
            $keterangan = 'TIDAK';
            $no_antrian = null;
            if ($matchSchedule && $hasAny) {
                $keterangan = 'OK';
                $no_antrian = $antrianCounter++;
            }

            DaftarUlang::create([
                'no_daftar' => $p->no_daftar,
                'nama_pemohon' => $p->nama_pemohon,
                'hari_harus_datang' => $hari,
                'tanggal_harus_datang' => $tanggal,
                'ktp' => $ktp,
                'kk' => $kk,
                'ijazah_akta' => $ijazah,
                'keterangan' => $keterangan,
                'no_antrian' => $no_antrian,
            ]);
        }

        // Create pengurusan records for some of the OK antrian entries (~60% of OK)
        $duRows = DaftarUlang::whereNotNull('no_antrian')->orderBy('no_antrian')->get();
        foreach ($duRows as $du) {
            if (rand(1,100) > 60) continue;

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
