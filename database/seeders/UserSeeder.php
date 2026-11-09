<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun Admin
        User::create([
            'name' => 'Administrator Sekolah',
            'email' => 'admin@savemate.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'phone' => '081234567890',
            'is_active' => true,
        ]);

        // 2. Akun Kantin
        User::create([
            'name' => 'Bu Sari - Kantin Sehat',
            'email' => 'kantin@savemate.com',
            'password' => Hash::make('password'),
            'role' => 'kantin',
            'phone' => '081234567891',
            'is_active' => true,
        ]);

        // 3. Akun Siswa
        User::create([
            'name' => 'Andi Pratama',
            'email' => 'siswa@savemate.com',
            'password' => Hash::make('password'),
            'role' => 'siswa',
            'phone' => '081234567892',
            'is_active' => true,
        ]);
    }
}