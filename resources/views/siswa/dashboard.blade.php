@extends('layouts.app')
@section('title', 'Dashboard Siswa')

@section('sidebar-menu')
    <a href="{{ route('siswa.dashboard') }}" class="active"><i class="bi bi-house"></i> Dashboard</a>
    <a href="{{ route('siswa.riwayat') }}"><i class="bi bi-clock-history"></i> Riwayat Transaksi</a>
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-0 fw-bold">Halo, {{ explode(' ', $student->name)[0] }}! 👋</h3>
            <small class="text-muted">Selamat datang di SaveMate</small>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <!-- Kartu Saldo (Paling Menonjol) -->
        <div class="col-12 col-md-4">
            <div class="card card-stat p-4 bg-success text-white h-100 border-0 shadow-sm">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h6 class="mb-1 opacity-75">Saldo Saya</h6>
                        <h2 class="mb-0 fw-bold">Rp {{ number_format($wallet->balance, 0, ',', '.') }}</h2>
                    </div>
                    <i class="bi bi-wallet2 fs-1 opacity-50"></i>
                </div>
                <!-- <div class="mt-2 pt-2 border-top border-white border-opacity-25">
                    <small class="d-block opacity-75">Token RFID Anda:</small>
                    <code class="bg-white bg-opacity-25 text-white px-2 py-1 rounded mt-1 d-inline-block">{{ $student->rfid_token }}</code>
                </div> -->
            </div>
        </div>

        <!-- Kartu Total Transaksi -->
        <div class="col-6 col-md-4">
            <div class="card card-stat p-3 bg-primary text-white h-100 border-0 shadow-sm text-center">
                <i class="bi bi-cart-check fs-2 mb-2 opacity-75"></i>
                <h6 class="mb-1 small opacity-75">Total Transaksi</h6>
                <h3 class="mb-0 fw-bold">{{ $total_transaksi }}</h3>
                <small class="opacity-75">Kali belanja</small>
            </div>
        </div>

        <!-- Kartu Informasi -->
        <div class="col-6 col-md-4">
            <div class="card card-stat p-3 bg-info text-white h-100 border-0 shadow-sm text-center">
                <i class="bi bi-person-badge fs-2 mb-2 opacity-75"></i>
                <h6 class="mb-1 small opacity-75">Informasi Siswa</h6>
                <p class="mb-0 fw-bold">{{ $student->nis }}</p>
                <small class="opacity-75">Kelas {{ $student->class }}</small>
            </div>
        </div>
    </div>

    <!-- Tombol Aksi -->
    <div class="d-grid gap-2 d-md-flex justify-content-md-start">
        <a href="{{ route('siswa.riwayat') }}" class="btn btn-primary btn-lg w-100 w-md-auto shadow-sm d-flex align-items-center justify-content-center gap-2">
            <i class="bi bi-clock-history fs-5"></i> 
            <span>Lihat Riwayat Transaksi</span>
        </a>
    </div>
@endsection