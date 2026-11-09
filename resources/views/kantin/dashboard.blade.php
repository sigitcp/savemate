@extends('layouts.app')
@section('title', 'Dashboard Kantin')

@section('sidebar-menu')
    <a href="{{ route('kantin.dashboard') }}" class="active"><i class="bi bi-speedometer2"></i> Dashboard</a>
    <a href="{{ route('kantin.transaksi') }}"><i class="bi bi-cart-check"></i> Transaksi</a>
    <a href="{{ route('kantin.riwayat') }}"><i class="bi bi-clock-history"></i> Riwayat</a>
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-0 fw-bold">Dashboard</h3>
            <small class="text-muted">{{ $canteen->name }}</small>
        </div>
    </div>

    {{-- STATISTIK (2 Kolom di HP, 4 Kolom di Desktop) --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card card-stat p-3 bg-success text-white h-100">
                <h6 class="mb-1 small opacity-75">Pendapatan Hari Ini</h6>
                <h4 class="mb-0 fw-bold">Rp {{ number_format($data['pendapatan_hari_ini'], 0, ',', '.') }}</h4>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card card-stat p-3 bg-primary text-white h-100">
                <h6 class="mb-1 small opacity-75">Transaksi Hari Ini</h6>
                <h4 class="mb-0 fw-bold">{{ $data['transaksi_hari_ini'] }}</h4>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card card-stat p-3 bg-info text-white h-100">
                <h6 class="mb-1 small opacity-75">Total Pendapatan</h6>
                <h4 class="mb-0 fw-bold">Rp {{ number_format($data['total_pendapatan'], 0, ',', '.') }}</h4>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card card-stat p-3 bg-warning text-white h-100">
                <h6 class="mb-1 small opacity-75">Total Transaksi</h6>
                <h4 class="mb-0 fw-bold">{{ $data['total_transaksi'] }}</h4>
            </div>
        </div>
    </div>

    {{-- TOMBOL AKSI UTAMA --}}
    <a href="{{ route('kantin.transaksi') }}" class="btn btn-primary btn-lg w-100 shadow-sm d-flex align-items-center justify-content-center gap-2">
        <i class="bi bi-cart-plus fs-5"></i> 
        <span>Mulai Transaksi Baru</span>
    </a>
@endsection