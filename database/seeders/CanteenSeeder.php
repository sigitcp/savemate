<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Canteen;

class CanteenSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil user kantin yang baru dibuat
        $user = User::where('email', 'kantin@savemate.com')->first();

        if ($user) {
            Canteen::create([
                'user_id' => $user->id,
                'name' => 'Kantin Sehat',
                'owner' => 'Bu Sari',
                'location' => 'Gedung A - Lantai 1',
                'is_active' => true,
            ]);
        }
    }
}