<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;

class SiswaController extends Controller
{
    public function dashboard()
    {
        $student = auth()->user()->student;
        $wallet = $student->wallet;
        
        $total_transaksi = $student->transactions()->where('status', 'success')->count();
        
        return view('siswa.dashboard', compact('student', 'wallet', 'total_transaksi'));
    }

    public function riwayat()
    {
        $student = auth()->user()->student;
        $transactions = $student->transactions()
            ->with('canteen', 'items.menu')
            ->latest('transaction_at')
            ->paginate(10);
        return view('siswa.riwayat', compact('transactions', 'student'));
    }
}