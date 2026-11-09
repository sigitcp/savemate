<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'canteen_id',
        'total',
        'status',
        'transaction_at',
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'transaction_at' => 'datetime',
    ];

    // Relasi ke Student
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    // Relasi ke Canteen
    public function canteen()
    {
        return $this->belongsTo(Canteen::class);
    }

    // Relasi ke Transaction Items
    public function items()
    {
        return $this->hasMany(TransactionItem::class);
    }

    // Relasi ke Wallet Mutation
    public function walletMutation()
    {
        return $this->hasOne(WalletMutation::class);
    }

    // Scope: Hanya transaksi sukses
    public function scopeSuccess($query)
    {
        return $query->where('status', 'success');
    }

    // Scope: Transaksi hari ini
    public function scopeToday($query)
    {
        return $query->whereDate('transaction_at', today());
    }
}