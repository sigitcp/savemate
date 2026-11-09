<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Canteen extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'owner',
        'location',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relasi ke User (pemilik kantin)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke Menu
    public function menus()
    {
        return $this->hasMany(Menu::class);
    }

    // Relasi ke Transactions
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    // Helper: Hitung total pendapatan
    public function getTotalRevenueAttribute()
    {
        return $this->transactions()
            ->where('status', 'success')
            ->sum('total');
    }

    // Helper: Pendapatan hari ini
    public function getTodayRevenueAttribute()
    {
        return $this->transactions()
            ->where('status', 'success')
            ->whereDate('transaction_at', today())
            ->sum('total');
    }


    // BARU: Helper: Hitung jumlah transaksi HARI INI SAJA
    public function getTodayTransactionCountAttribute()
    {
        return $this->transactions()
            ->where('status', 'success')
            ->whereDate('transaction_at', today())
            ->count();
    }

    // BARU: Helper: Ambil rekapitulasi per hari (14 hari terakhir)
    public function getDailyRecapAttribute()
    {
        return $this->transactions()
            ->where('status', 'success')
            ->selectRaw('DATE(transaction_at) as date, COUNT(*) as total_trx, SUM(total) as total_revenue')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->limit(14) // Menampilkan 14 hari terakhir
            ->get();
    }
}