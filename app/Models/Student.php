<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nis',
        'name',
        'class',
        'photo',
        'rfid_token',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke Wallet (1 siswa = 1 wallet)
    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }

    // Relasi ke Transactions
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    // Helper: Ambil saldo siswa
    public function getBalanceAttribute()
    {
        return $this->wallet ? $this->wallet->balance : 0;
    }
}