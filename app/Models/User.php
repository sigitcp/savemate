<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'photo',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    // Relasi ke Student (jika role = siswa)
    public function student()
    {
        return $this->hasOne(Student::class);
    }

    // Relasi ke Canteen (jika role = kantin)
    public function canteen()
    {
        return $this->hasOne(Canteen::class);
    }

    // Helper: Cek role
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isKantin()
    {
        return $this->role === 'kantin';
    }

    public function isSiswa()
    {
        return $this->role === 'siswa';
    }
}