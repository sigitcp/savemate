<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Student;
use App\Models\Canteen;
use App\Models\Menu;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\Wallet;
use App\Models\WalletMutation;
use Carbon\Carbon;

class TransactionSeeder extends Seeder
{
    public function run(): void
    {
        // ===== TRANSAKSI DUMMY UNTUK RIWAYAT =====
        $transactions = [
            // Siswa 1 (Andi) - beli di Kantin Sehat, 2 hari lalu
            [
                'student_email' => 'siswa1@smartcanteen.com',
                'canteen_name' => 'Kantin Sehat',
                'menus' => [
                    ['name' => 'Nasi Goreng', 'qty' => 1],
                    ['name' => 'Es Teh Manis', 'qty' => 1]
                ],
                'days_ago' => 2,
            ],
            // Siswa 2 (Bunga) - beli di Kantin Segar, 1 hari lalu
            [
                'student_email' => 'siswa2@smartcanteen.com',
                'canteen_name' => 'Kantin Segar',
                'menus' => [
                    ['name' => 'Ayam Geprek', 'qty' => 1],
                    ['name' => 'Jus Alpukat', 'qty' => 1]
                ],
                'days_ago' => 1,
            ],
            // Siswa 3 (Citra) - beli di Kantin Sehat, hari ini
            [
                'student_email' => 'siswa3@smartcanteen.com',
                'canteen_name' => 'Kantin Sehat',
                'menus' => [
                    ['name' => 'Bakso', 'qty' => 1],
                    ['name' => 'Roti Bakar Coklat', 'qty' => 1]
                ],
                'days_ago' => 0,
            ],
        ];

        foreach ($transactions as $data) {
            $student = Student::whereHas('user', fn($q) => $q->where('email', $data['student_email']))->first();
            $canteen = Canteen::where('name', $data['canteen_name'])->first();

            if (!$student || !$canteen) {
                continue;
            }

            $total = 0;

            // Buat transaksi
            $transaction = Transaction::create([
                'student_id' => $student->id,
                'canteen_id' => $canteen->id,
                'total' => 0, // akan diupdate
                'status' => 'success',
                'transaction_at' => Carbon::now()->subDays($data['days_ago']),
            ]);

            // Simpan item transaksi
            foreach ($data['menus'] as $menuItem) {
                $menu = Menu::where('canteen_id', $canteen->id)
                            ->where('name', $menuItem['name'])
                            ->first();

                if (!$menu) {
                    continue;
                }

                $subtotal = $menu->price * $menuItem['qty'];
                $total += $subtotal;

                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'menu_id' => $menu->id,
                    'qty' => $menuItem['qty'],
                    'price' => $menu->price,
                    'subtotal' => $subtotal,
                ]);
            }

            // Update total transaksi
            $transaction->update(['total' => $total]);

            // Kurangi saldo siswa & catat mutasi
            $wallet = $student->wallet;
            $wallet->balance -= $total;
            $wallet->save();

            WalletMutation::create([
                'wallet_id' => $wallet->id,
                'type' => 'purchase',
                'amount' => $total,
                'description' => 'Pembelian di ' . $canteen->name,
                'transaction_id' => $transaction->id,
            ]);
        }
    }
}