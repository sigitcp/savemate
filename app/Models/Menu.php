<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    protected $fillable = [
        'canteen_id',
        'name',
        'price',
        'photo',
        'category',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Relasi ke Canteen
    public function canteen()
    {
        return $this->belongsTo(Canteen::class);
    }

    // Relasi ke Transaction Items
    public function transactionItems()
    {
        return $this->hasMany(TransactionItem::class);
    }

    // Scope: Hanya menu aktif
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}