<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Canteen;
use App\Models\Menu;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil kantin yang baru dibuat
        $canteen = Canteen::where('name', 'Kantin Sehat')->first();

        if ($canteen) {
            $menus = [
                ['name' => 'Nasi Goreng Spesial', 'price' => 15000, 'category' => 'Makanan'],
                ['name' => 'Mie Ayam Bakso', 'price' => 12000, 'category' => 'Makanan'],
                ['name' => 'Ayam Geprek', 'price' => 13000, 'category' => 'Makanan'],
                ['name' => 'Roti Bakar Coklat Keju', 'price' => 8000, 'category' => 'Snack'],
                ['name' => 'Es Teh Manis', 'price' => 3000, 'category' => 'Minuman'],
                ['name' => 'Es Jeruk Peras', 'price' => 5000, 'category' => 'Minuman'],
                ['name' => 'Air Mineral', 'price' => 4000, 'category' => 'Minuman'],
            ];

            foreach ($menus as $menu) {
                Menu::create([
                    'canteen_id' => $canteen->id,
                    'name' => $menu['name'],
                    'price' => $menu['price'],
                    'category' => $menu['category'],
                    'is_active' => true,
                ]);
            }
        }
    }
}