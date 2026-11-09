<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\KantinController;
use App\Http\Controllers\SiswaController;

// ===== GUEST =====
Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ===== ADMIN =====
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // Siswa
    Route::get('/siswa', [AdminController::class, 'siswaIndex'])->name('siswa.index');
    Route::get('/siswa/create', [AdminController::class, 'siswaCreate'])->name('siswa.create');
    Route::post('/siswa', [AdminController::class, 'siswaStore'])->name('siswa.store');
    Route::delete('/siswa/{student}', [AdminController::class, 'siswaDestroy'])->name('siswa.destroy');
    Route::post('/siswa/{student}/topup', [AdminController::class, 'saldoTopup'])->name('siswa.topup');
    Route::put('/siswa/{student}', [AdminController::class, 'siswaUpdate'])->name('siswa.update'); 
    
    // Kantin
    Route::get('/kantin', [AdminController::class, 'kantinIndex'])->name('kantin.index');
    Route::get('/kantin/create', [AdminController::class, 'kantinCreate'])->name('kantin.create');
    Route::post('/kantin', [AdminController::class, 'kantinStore'])->name('kantin.store');
    Route::delete('/kantin/{canteen}', [AdminController::class, 'kantinDestroy'])->name('kantin.destroy');
    Route::put('/kantin/{canteen}', [AdminController::class, 'kantinUpdate'])->name('kantin.update'); 
    
    // Menu
    Route::get('/menu', [AdminController::class, 'menuIndex'])->name('menu.index');
    Route::get('/menu/create', [AdminController::class, 'menuCreate'])->name('menu.create');
    Route::post('/menu', [AdminController::class, 'menuStore'])->name('menu.store');
    Route::delete('/menu/{menu}', [AdminController::class, 'menuDestroy'])->name('menu.destroy');
    
    // Laporan
    Route::get('/laporan', [AdminController::class, 'laporan'])->name('laporan');
});

// ===== KANTIN =====
Route::middleware(['auth', 'role:kantin'])->prefix('kantin')->name('kantin.')->group(function () {
    Route::get('/dashboard', [KantinController::class, 'dashboard'])->name('dashboard');
    Route::get('/transaksi', [KantinController::class, 'transaksi'])->name('transaksi');
    Route::post('/cek-token', [KantinController::class, 'cekToken'])->name('cekToken');
    Route::post('/proses-transaksi', [KantinController::class, 'prosesTransaksi'])->name('prosesTransaksi');
    Route::get('/riwayat', [KantinController::class, 'riwayat'])->name('riwayat');
});

// ===== SISWA =====
Route::middleware(['auth', 'role:siswa'])->prefix('siswa')->name('siswa.')->group(function () {
    Route::get('/dashboard', [SiswaController::class, 'dashboard'])->name('dashboard');
    Route::get('/riwayat', [SiswaController::class, 'riwayat'])->name('riwayat');
    Route::get('/riwayat/pdf', [SiswaController::class, 'downloadPdf'])->name('riwayat.pdf');
});