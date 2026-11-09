<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\Student;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\Canteen;
use Illuminate\Support\Facades\DB;

class KantinController extends Controller
{
    public function dashboard()
    {
        $canteen = auth()->user()->canteen;
        
        $data = [
            // PERBAIKAN: Tambahkan () setelah transactions
            'pendapatan_hari_ini' => $canteen->transactions()
                ->where('status', 'success')
                ->whereDate('transaction_at', today())
                ->sum('total'),
                
            'transaksi_hari_ini' => $canteen->transactions()
                ->where('status', 'success')
                ->whereDate('transaction_at', today())
                ->count(),
                
            'total_pendapatan' => $canteen->total_revenue,
            
            'total_transaksi' => $canteen->transactions()
                ->where('status', 'success')
                ->count(),
        ];

        return view('kantin.dashboard', compact('data', 'canteen'));
    }

    // ===== TRANSAKSI =====
    public function transaksi()
    {
        $canteen = auth()->user()->canteen;
        $menus = Menu::where('canteen_id', $canteen->id)
            ->where('is_active', true)
            ->get();
        return view('kantin.transaksi', compact('menus', 'canteen'));
    }

    // ===== CEK TOKEN RFID =====
    public function cekToken(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'total' => 'required|numeric|min:0',
            'items' => 'required|array|min:1',
        ]);

        // 1. Cari siswa berdasarkan token
        $student = Student::where('rfid_token', $request->token)
            ->where('is_active', true)
            ->with('wallet')
            ->first();

        // 2. Jika token tidak dikenali, tolak LANGSUNG (tanpa data siswa)
        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Kartu tidak terdaftar atau sudah tidak aktif',
            ]);
        }

        // 3. Jika saldo tidak cukup, tolak LANGSUNG (tanpa data siswa)
        if ($student->wallet->balance < $request->total) {
            return response()->json([
                'success' => false,
                'message' => 'Saldo tidak cukup! Silakan isi ulang saldo terlebih dahulu.',
            ]);
        }

        // 4. Jika semua valid, kirim data siswa + URL foto lengkap
        return response()->json([
            'success' => true,
            'student' => [
                'id' => $student->id,
                'name' => $student->name,
                'class' => $student->class,
                // Kirim URL lengkap agar frontend bisa langsung render gambar
                'photo' => $student->photo ? asset($student->photo) : null, 
                'balance' => $student->wallet->balance,
            ],
        ]);
    }

    // ===== PROSES TRANSAKSI =====
    public function prosesTransaksi(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'total' => 'required|numeric|min:0',
            'items' => 'required|array|min:1', // Sekarang ini akan berhasil dibaca sebagai array
        ]);

        $canteen = auth()->user()->canteen;
        $student = Student::with('wallet')->findOrFail($request->student_id);

        if ($student->wallet->balance < $request->total) {
            return redirect()->route('kantin.transaksi')
                ->with('error', 'Saldo siswa tidak mencukupi.');
        }

        DB::transaction(function () use ($canteen, $student, $request) {
            // 1. Buat Transaksi
            $transaction = Transaction::create([
                'student_id' => $student->id,
                'canteen_id' => $canteen->id,
                'total' => $request->total,
                'status' => 'success',
                'transaction_at' => now(),
            ]);

            // 2. Simpan Item Transaksi
            foreach ($request->items as $item) {
                $menu = Menu::findOrFail($item['menu_id']);
                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'menu_id' => $menu->id,
                    'qty' => $item['qty'],
                    'price' => $menu->price,
                    'subtotal' => $menu->price * $item['qty'],
                ]);
            }

            // 3. Potong Saldo
            $student->wallet->reduceBalance(
                $request->total, 
                'Pembelian di ' . $canteen->name, 
                $transaction->id
            );
        });

        return redirect()->route('kantin.transaksi')
            ->with('success', "Transaksi berhasil! Saldo {$student->name} telah dipotong sebesar Rp " . number_format($request->total, 0, ',', '.'));
    }

    // ===== RIWAYAT =====
    public function riwayat()
    {
        $canteen = auth()->user()->canteen;
        $transactions = $canteen->transactions()
            ->with('student', 'items.menu')
            ->latest('transaction_at')
            ->paginate(15);
        return view('kantin.riwayat', compact('transactions', 'canteen'));
    }
}