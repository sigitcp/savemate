<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'balance',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
    ];

    // Relasi ke Student
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    // Relasi ke Wallet Mutations
    public function mutations()
    {
        return $this->hasMany(WalletMutation::class);
    }

    // Helper: Tambah saldo
    public function addBalance($amount, $description = null, $transactionId = null)
    {
        $this->balance += $amount;
        $this->save();

        $this->mutations()->create([
            'type' => 'topup',
            'amount' => $amount,
            'description' => $description ?? 'Pengisian saldo',
            'transaction_id' => $transactionId,
        ]);

        return $this;
    }

    // Helper: Kurangi saldo
    public function reduceBalance($amount, $description = null, $transactionId = null)
    {
        if ($this->balance < $amount) {
            throw new \Exception('Saldo tidak mencukupi');
        }

        $this->balance -= $amount;
        $this->save();

        $this->mutations()->create([
            'type' => 'purchase',
            'amount' => $amount,
            'description' => $description ?? 'Pembelian di kantin',
            'transaction_id' => $transactionId,
        ]);

        return $this;
    }
}