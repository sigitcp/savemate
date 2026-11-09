<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Student;
use App\Models\Wallet;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil user siswa yang baru dibuat
        $user = User::where('email', 'siswa@savemate.com')->first();

        if ($user) {
            // Buat data siswa
            $student = Student::create([
                'user_id' => $user->id,
                'nis' => '2024001',
                'name' => 'Andi Pratama',
                'class' => '7A',
                'rfid_token' => 'SC-2026-001', // Token mudah diingat untuk demo
                'is_active' => true,
            ]);

            // Buat wallet dengan saldo awal bersih
            Wallet::create([
                'student_id' => $student->id,
                'balance' => 100000, // Saldo awal Rp 100.000
            ]);
        }
    }
}