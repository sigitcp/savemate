<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use App\Models\WalletMutation;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class SiswaController extends Controller
{
    public function dashboard()
    {
        $student = auth()->user()->student;
        $wallet = $student->wallet;
        $total_transaksi = $student->transactions()->where('status', 'success')->count();

        // 1. DATA CHART: Kategori Jajanan (Makanan, Minuman, Snack)
        $kategoriData = DB::table('transaction_items')
            ->join('menus', 'transaction_items.menu_id', '=', 'menus.id')
            ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
            ->where('transactions.student_id', $student->id)
            ->where('transactions.status', 'success')
            ->select('menus.category', DB::raw('count(*) as total'))
            ->groupBy('menus.category')
            ->get();

        $kategoriLabels = $kategoriData->pluck('category')->toArray();
        $kategoriValues = $kategoriData->pluck('total')->toArray();

        // 2. DATA CHART: Kantin Favorit
        $kantinData = DB::table('transactions')
            ->join('canteens', 'transactions.canteen_id', '=', 'canteens.id')
            ->where('transactions.student_id', $student->id)
            ->where('transactions.status', 'success')
            ->select('canteens.name', DB::raw('count(*) as total'))
            ->groupBy('canteens.name')
            ->get();

        $kantinLabels = $kantinData->pluck('name')->toArray();
        $kantinValues = $kantinData->pluck('total')->toArray();

        // 3. DATA CHART: Aktivitas Mingguan (Senin - Minggu)
        // DAYOFWEEK MySQL: 1=Minggu, 2=Senin, ..., 7=Sabtu
        $hariData = DB::table('transactions')
            ->where('student_id', $student->id)
            ->where('status', 'success')
            ->select(DB::raw('DAYOFWEEK(transaction_at) as day_num'), DB::raw('count(*) as total'))
            ->groupBy('day_num')
            ->get();

        // Mapping hari agar urut dari Senin ke Minggu
        $urutanHari = [2 => 'Senin', 3 => 'Selasa', 4 => 'Rabu', 5 => 'Kamis', 6 => 'Jumat', 7 => 'Sabtu', 1 => 'Minggu'];
        $hariLabels = [];
        $hariValues = [];

        foreach ($urutanHari as $num => $namaHari) {
            $hariLabels[] = $namaHari;
            $match = $hariData->firstWhere('day_num', $num);
            $hariValues[] = $match ? $match->total : 0; // Isi 0 jika tidak ada transaksi di hari itu
        }

        return view('siswa.dashboard', compact(
            'student', 'wallet', 'total_transaksi',
            'kategoriLabels', 'kategoriValues',
            'kantinLabels', 'kantinValues',
            'hariLabels', 'hariValues'
        ));
    }

    public function riwayat(Request $request)
    {
        $student = auth()->user()->student;

        // 1. Ambil filter tanggal (default: 30 hari terakhir)
        $startDate = $request->input('start_date', now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));
        $endDateTime = $endDate . ' 23:59:59';

        // 2. Ambil Data Pembelian (Jajan)
        $purchases = Transaction::where('student_id', $student->id)
            ->where('status', 'success')
            ->whereBetween('transaction_at', [$startDate, $endDateTime])
            ->with('canteen', 'items.menu')
            ->get()
            ->map(function ($item) {
                $item->activity_type = 'purchase';
                return $item;
            });

        // 3. Ambil Data Topup
        $topups = WalletMutation::whereHas('wallet', function ($q) use ($student) {
                $q->where('student_id', $student->id);
            })
            ->where('type', 'topup')
            ->whereBetween('created_at', [$startDate, $endDateTime])
            ->get()
            ->map(function ($item) {
                $item->activity_type = 'topup';
                return $item;
            });

        // 4. Gabungkan dan Urutkan berdasarkan waktu (Terbaru di atas)
        $activities = $purchases->concat($topups)->sortByDesc(function ($item) {
            return $item->activity_type === 'purchase' ? $item->transaction_at : $item->created_at;
        })->values(); // Reset index array

        return view('siswa.riwayat', compact('activities', 'startDate', 'endDate', 'student'));
    }

    // public function downloadPdf(Request $request)
    // {
    //     $student = auth()->user()->student;
    //     $startDate = $request->input('start_date', now()->subDays(30)->format('Y-m-d'));
    //     $endDate = $request->input('end_date', now()->format('Y-m-d'));
    //     $endDateTime = $endDate . ' 23:59:59';

    //     $purchases = Transaction::where('student_id', $student->id)
    //         ->where('status', 'success')
    //         ->whereBetween('transaction_at', [$startDate, $endDateTime])
    //         ->with('canteen', 'items.menu')
    //         ->get()
    //         ->map(function ($item) { $item->activity_type = 'purchase'; return $item; });

    //     $topups = WalletMutation::whereHas('wallet', function ($q) use ($student) {
    //             $q->where('student_id', $student->id);
    //         })
    //         ->where('type', 'topup')
    //         ->whereBetween('created_at', [$startDate, $endDateTime])
    //         ->get()
    //         ->map(function ($item) { $item->activity_type = 'topup'; return $item; });

    //     $activities = $purchases->concat($topups)->sortByDesc(function ($item) {
    //         return $item->activity_type === 'purchase' ? $item->transaction_at : $item->created_at;
    //     })->values();

    //     $pdf = Pdf::loadView('siswa.riwayat-pdf', compact('activities', 'startDate', 'endDate', 'student'));
    //     return $pdf->download('Riwayat_Transaksi_' . $student->nis . '.pdf');
    // }
}