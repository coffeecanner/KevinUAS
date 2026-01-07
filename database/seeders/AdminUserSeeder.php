<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        // Create an admin user with known credentials for testing
        $email = 'admin@imigrasi.local';
        if (!User::where('email', $email)->exists()) {
            User::create([
                'name' => 'Admin Imigrasi',
                'email' => $email,
                'password' => Hash::make('secret123'), // change as needed
            ]);
        }
    }
}
