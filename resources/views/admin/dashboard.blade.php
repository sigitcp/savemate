@extends('layouts.app')
@section('title', 'Dashboard Admin')

@section('sidebar-menu')
    <a href="{{ route('admin.dashboard') }}" class="active"><i class="bi bi-speedometer2"></i> Dashboard</a>
    <a href="{{ route('admin.siswa.index') }}"><i class="bi bi-people"></i> Data Siswa</a>
    <a href="{{ route('admin.kantin.index') }}"><i class="bi bi-shop"></i> Data Kantin</a>
    <a href="{{ route('admin.menu.index') }}"><i class="bi bi-list-ul"></i> Menu Kantin</a>
    <a href="{{ route('admin.laporan') }}"><i class="bi bi-graph-up"></i> Laporan</a>
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">Dashboard Admin</h3>
        <span class="badge bg-primary fs-6">
            <i class="bi bi-calendar3"></i> {{ now()->format('d M Y') }}
        </span>
    </div>

    {{-- ===== STATISTIK UTAMA ===== --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card card-stat p-3 bg-primary text-white">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="mb-1">Total Siswa</h6>
                        <h2 class="mb-0">{{ $data['totalSiswa'] }}</h2>
                    </div>
                    <i class="bi bi-people-fill fs-1 opacity-50"></i>
                </div>
                <small class="mt-2 opacity-75">Total saldo: Rp {{ number_format($data['totalSaldoSiswa'], 0, ',', '.') }}</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-stat p-3 bg-success text-white">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="mb-1">Total Kantin</h6>
                        <h2 class="mb-0">{{ $data['totalKantin'] }}</h2>
                    </div>
                    <i class="bi bi-shop fs-1 opacity-50"></i>
                </div>
                <small class="mt-2 opacity-75">Kantin aktif terdaftar</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-stat p-3 bg-info text-white">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="mb-1">Total Transaksi</h6>
                        <h2 class="mb-0">{{ $data['totalTransaksi'] }}</h2>
                    </div>
                    <i class="bi bi-cart-check-fill fs-1 opacity-50"></i>
                </div>
                <small class="mt-2 opacity-75">Pendapatan: Rp {{ number_format($data['totalPendapatanKantin'], 0, ',', '.') }}</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-stat p-3 bg-warning text-white">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="mb-1">Total Topup</h6>
                        <h2 class="mb-0">{{ $data['totalTopup'] }}</h2>
                    </div>
                    <i class="bi bi-wallet2 fs-1 opacity-50"></i>
                </div>
                <small class="mt-2 opacity-75">Nominal: Rp {{ number_format($data['totalNominalTopup'], 0, ',', '.') }}</small>
            </div>
        </div>
    </div>

    {{-- ===== STATISTIK HARI INI ===== --}}
    <h5 class="mb-3"><i class="bi bi-calendar-day"></i> Aktivitas Hari Ini</h5>
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card p-3 border-start border-4 border-success">
                <h6 class="text-muted mb-1">Transaksi Kantin</h6>
                <h3 class="mb-0">{{ $data['transaksiHariIni'] }} <small class="text-muted fs-6">transaksi</small></h3>
                <p class="mb-0 mt-2 text-success">
                    <strong>+ Rp {{ number_format($data['pendapatanHariIni'], 0, ',', '.') }}</strong>
                </p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-3 border-start border-4 border-primary">
                <h6 class="text-muted mb-1">Pengisian Saldo (Topup)</h6>
                <h3 class="mb-0">{{ $data['topupHariIni'] }} <small class="text-muted fs-6">topup</small></h3>
                <p class="mb-0 mt-2 text-primary">
                    <strong>+ Rp {{ number_format($data['nominalTopupHariIni'], 0, ',', '.') }}</strong>
                </p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-3 border-start border-4 border-info">
                <h6 class="text-muted mb-1">Total Mutasi Hari Ini</h6>
                <h3 class="mb-0">{{ $data['mutasiHariIni'] }} <small class="text-muted fs-6">aktivitas</small></h3>
                <p class="mb-0 mt-2 text-info">
                    <strong>Topup + Transaksi</strong>
                </p>
            </div>
        </div>
    </div>

    {{-- ===== AKTIVITAS TERBARU ===== --}}
    <div class="card">
        <div class="card-header bg-white">
            <h5 class="mb-0"><i class="bi bi-clock-history"></i> Aktivitas Terbaru</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Waktu</th>
                        <th>Siswa</th>
                        <th>Jenis</th>
                        <th>Keterangan</th>
                        <th>Nominal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data['aktivitasTerbaru'] as $mutasi)
                    <tr>
                        <td>{{ $mutasi->created_at->format('d/m/Y H:i') }}</td>
                        <td>{{ $mutasi->wallet->student->name ?? '-' }}</td>
                        <td>
                            @if($mutasi->type == 'topup')
                                <span class="badge bg-primary"><i class="bi bi-arrow-down-circle"></i> Topup</span>
                            @elseif($mutasi->type == 'purchase')
                                <span class="badge bg-danger"><i class="bi bi-arrow-up-circle"></i> Pembelian</span>
                            @else
                                <span class="badge bg-secondary">{{ ucfirst($mutasi->type) }}</span>
                            @endif
                        </td>
                        <td>
                            {{ $mutasi->description ?? '-' }}
                            @if($mutasi->transaction && $mutasi->transaction->canteen)
                                <br><small class="text-muted">di {{ $mutasi->transaction->canteen->name }}</small>
                            @endif
                        </td>
                        <td>
                            @if($mutasi->type == 'topup')
                                <strong class="text-success">+ Rp {{ number_format($mutasi->amount, 0, ',', '.') }}</strong>
                            @else
                                <strong class="text-danger">- Rp {{ number_format($mutasi->amount, 0, ',', '.') }}</strong>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            Belum ada aktivitas
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection