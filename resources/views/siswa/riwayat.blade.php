@extends('layouts.app')
@section('title', 'Riwayat Transaksi')

@section('sidebar-menu')
    <a href="{{ route('siswa.dashboard') }}"><i class="bi bi-house"></i> Dashboard</a>
    <a href="{{ route('siswa.riwayat') }}" class="active"><i class="bi bi-clock-history"></i> Riwayat Transaksi</a>
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0 fw-bold">Riwayat Transaksi</h3>
        <a href="{{ route('siswa.dashboard') }}" class="btn btn-sm btn-outline-primary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    {{-- ===== TAMPILAN DESKTOP / TABLET (TABEL) ===== --}}
    <div class="card shadow-sm border-0 d-none d-md-block">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Tanggal & Waktu</th>
                        <th>Nama Kantin</th>
                        <th>Rincian Menu</th>
                        <th class="text-end">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $t)
                    <tr>
                        <td>
                            <div class="fw-bold">{{ $t->transaction_at->format('d/m/Y') }}</div>
                            <small class="text-muted">{{ $t->transaction_at->format('H:i') }} WIB</small>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border px-2 py-1">
                                <i class="bi bi-shop"></i> {{ $t->canteen->name }}
                            </span>
                        </td>
                        <td>
                            @foreach($t->items as $item)
                                <div class="small mb-1">
                                    <span class="fw-semibold">{{ $item->menu->name }}</span> 
                                    <span class="badge bg-light text-dark border">x{{ $item->qty }}</span>
                                </div>
                            @endforeach
                        </td>
                        <td class="text-end">
                            <span class="badge bg-danger fs-6">- Rp {{ number_format($t->total, 0, ',', '.') }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-5">
                            <i class="bi bi-receipt fs-1 d-block mb-2"></i>
                            Belum ada riwayat transaksi
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($transactions->hasPages())
            <div class="p-3 border-top">{{ $transactions->links() }}</div>
        @endif
    </div>

    {{-- ===== TAMPILAN HP / MOBILE (CARD LIST) ===== --}}
    <div class="d-md-none">
        @forelse($transactions as $t)
        <div class="card mb-3 shadow-sm border-0">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <h6 class="mb-0 fw-bold text-dark">
                            <i class="bi bi-shop text-primary"></i> {{ $t->canteen->name }}
                        </h6>
                        <small class="text-muted">
                            <i class="bi bi-calendar3"></i> {{ $t->transaction_at->format('d/m/Y') }} 
                            <span class="mx-1">•</span> 
                            <i class="bi bi-clock"></i> {{ $t->transaction_at->format('H:i') }}
                        </small>
                    </div>
                    <span class="badge bg-danger fs-6 px-2 py-1">- Rp {{ number_format($t->total, 0, ',', '.') }}</span>
                </div>
                
                <hr class="my-2 opacity-25">
                
                <div class="ps-1">
                    @foreach($t->items as $item)
                        <div class="d-flex justify-content-between small mb-1">
                            <span class="text-dark">{{ $item->menu->name }}</span>
                            <span class="text-muted fw-semibold">x{{ $item->qty }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        @empty
        <div class="text-center text-muted py-5">
            <i class="bi bi-receipt fs-1 d-block mb-2"></i>
            <p class="mb-0">Belum ada riwayat transaksi</p>
            <a href="{{ route('siswa.dashboard') }}" class="btn btn-sm btn-outline-primary mt-2">Kembali ke Dashboard</a>
        </div>
        @endforelse
        
        @if($transactions->hasPages())
            <div class="d-flex justify-content-center mt-3">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>
@endsection