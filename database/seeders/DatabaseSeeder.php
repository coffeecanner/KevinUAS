<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // App specific seeders
        $this->call([\Database\Seeders\PendaftaranSeeder::class]);
        $this->call([\Database\Seeders\DaftarUlangSeeder::class]);
        // Note: some daftar_ulang entries created above may not have no_antrian; in real flow
        // no_antrian is created by the controller. For demo, we optionally assign antrian here.

        // Auto-generate no_antrian for those marked OK
        \DB::table('daftar_ulang')->where('keterangan', 'OK')->update(['no_antrian' => null]);

        // Assign incremental antrian to OK entries
        $rows = \DB::table('daftar_ulang')->where('keterangan', 'OK')->orderBy('id')->get();
        $n = 1;
        foreach ($rows as $r) {
            \DB::table('daftar_ulang')->where('id', $r->id)->update(['no_antrian' => $n]);
            $n++;
        }

        // Create pengurusan records for entries that have no_antrian
        $this->call([\Database\Seeders\PengurusanSeeder::class]);
    }
}
