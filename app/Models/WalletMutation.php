<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WalletMutation extends Model
{
    use HasFactory;

    protected $fillable = [
        'wallet_id',
        'type',
        'amount',
        'description',
        'transaction_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    // Relasi ke Wallet
    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

    // Relasi ke Transaction (opsional, untuk topup tidak ada transaction_id)
    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}