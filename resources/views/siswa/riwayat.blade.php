@extends('layouts.app')
@section('title', 'Riwayat Transaksi')

@section('sidebar-menu')
    <a href="{{ route('siswa.dashboard') }}"><i class="bi bi-house"></i> Dashboard</a>
    <a href="{{ route('siswa.riwayat') }}" class="active"><i class="bi bi-clock-history"></i> Riwayat Transaksi</a>
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <h3 class="mb-0 fw-bold">Riwayat Transaksi</h3>
        <a href="{{ route('siswa.dashboard') }}" class="btn btn-sm btn-outline-primary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    {{-- FORM FILTER & DOWNLOAD --}}
    <div class="card shadow-sm border-0 mb-4 p-3">
        <form method="GET" action="{{ route('siswa.riwayat') }}" class="row g-2 align-items-end">
            <div class="col-6 col-md-3">
                <label class="form-label small fw-bold text-muted">Dari Tanggal</label>
                <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
            </div>
            <div class="col-6 col-md-3">
                <label class="form-label small fw-bold text-muted">Sampai Tanggal</label>
                <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
            </div>
            <div class="col-6 col-md-2">
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-funnel"></i> Filter</button>
            </div>
            <div class="col-6 col-md-4">
                <a href="{{ route('siswa.riwayat.pdf', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-danger w-100">
                    <i class="bi bi-file-earmark-pdf"></i> Download PDF
                </a>
            </div>
        </form>
    </div>

    {{-- ===== TAMPILAN DESKTOP (TABEL) ===== --}}
    <div class="card shadow-sm border-0 d-none d-md-block">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Tanggal & Waktu</th>
                        <th>Jenis</th>
                        <th>Keterangan</th>
                        <th class="text-end">Nominal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($activities as $act)
                    <tr>
                        <td>
                            <div class="fw-bold">{{ $act->activity_type == 'purchase' ? $act->transaction_at->format('d/m/Y') : $act->created_at->format('d/m/Y') }}</div>
                            <small class="text-muted">{{ $act->activity_type == 'purchase' ? $act->transaction_at->format('H:i') : $act->created_at->format('H:i') }} WIB</small>
                        </td>
                        <td>
                            @if($act->activity_type == 'topup')
                                <span class="badge bg-success bg-opacity-10 text-success border border-success"><i class="bi bi-arrow-down-circle"></i> Topup Saldo</span>
                            @else
                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger"><i class="bi bi-bag"></i> Pembelian</span>
                            @endif
                        </td>
                        <td>
                            @if($act->activity_type == 'topup')
                                <span class="text-muted">Pengisian saldo oleh Admin</span>
                            @else
                                <span class="fw-semibold"><i class="bi bi-shop text-primary"></i> {{ $act->canteen->name }}</span>
                                <div class="mt-1">
                                    @foreach($act->items as $item)
                                        <span class="badge bg-light text-dark border me-1">{{ $item->menu->name }} (x{{ $item->qty }})</span>
                                    @endforeach
                                </div>
                            @endif
                        </td>
                        <td class="text-end">
                            @if($act->activity_type == 'topup')
                                <span class="badge bg-success fs-6">+ Rp {{ number_format($act->amount, 0, ',', '.') }}</span>
                            @else
                                <span class="badge bg-danger fs-6">- Rp {{ number_format($act->total, 0, ',', '.') }}</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-5">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            Tidak ada transaksi pada periode ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ===== TAMPILAN HP (CARD LIST) ===== --}}
    <div class="d-md-none">
        @forelse($activities as $act)
        <div class="card mb-3 shadow-sm border-0">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        @if($act->activity_type == 'topup')
                            <h6 class="mb-0 fw-bold text-success"><i class="bi bi-arrow-down-circle"></i> Topup Saldo</h6>
                            <small class="text-muted">Pengisian saldo oleh Admin</small>
                        @else
                            <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-shop text-primary"></i> {{ $act->canteen->name }}</h6>
                            <small class="text-muted">
                                <i class="bi bi-calendar3"></i> {{ $act->transaction_at->format('d/m/Y') }} 
                                <span class="mx-1">•</span> 
                                <i class="bi bi-clock"></i> {{ $act->transaction_at->format('H:i') }}
                            </small>
                        @endif
                    </div>
                    @if($act->activity_type == 'topup')
                        <span class="badge bg-success fs-6 px-2 py-1">+ Rp {{ number_format($act->amount, 0, ',', '.') }}</span>
                    @else
                        <span class="badge bg-danger fs-6 px-2 py-1">- Rp {{ number_format($act->total, 0, ',', '.') }}</span>
                    @endif
                </div>
                
                @if($act->activity_type == 'purchase')
                    <hr class="my-2 opacity-25">
                    <div class="ps-1">
                        @foreach($act->items as $item)
                            <div class="d-flex justify-content-between small mb-1">
                                <span class="text-dark">{{ $item->menu->name }}</span>
                                <span class="text-muted fw-semibold">x{{ $item->qty }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
        @empty
        <div class="text-center text-muted py-5">
            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
            <p class="mb-0">Tidak ada transaksi pada periode ini.</p>
        </div>
        @endforelse
    </div>
@endsection