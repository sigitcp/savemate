@extends('layouts.app')
@section('title', 'Riwayat Transaksi')

@section('sidebar-menu')
    <a href="{{ route('siswa.dashboard') }}"><i class="bi bi-house"></i> Dashboard</a>
    <a href="{{ route('siswa.riwayat') }}" class="active"><i class="bi bi-clock-history"></i> Riwayat Transaksi</a>
@endsection

@section('content')
    {{-- CSS KHUSUS UNTUK CETAK / PRINT --}}
    <style>
        @media print {
            /* Sembunyikan semua elemen di halaman */
            body * {
                visibility: hidden;
            }
            /* Tampilkan hanya area laporan ini */
            #area-laporan, #area-laporan * {
                visibility: visible;
            }
            #area-laporan {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                padding: 20px;
                background: white !important;
                color: black !important;
            }
            /* Paksa tampilan tabel rapi saat print */
            .table { width: 100% !important; border-collapse: collapse !important; }
            .table th, .table td { border: 1px solid #000 !important; padding: 8px !important; }
            .no-print { display: none !important; }
            .badge { border: 1px solid #000 !important; color: #000 !important; background: none !important; }
        }
    </style>

    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2 no-print">
        <h3 class="mb-0 fw-bold">Riwayat Transaksi</h3>
        <a href="{{ route('siswa.dashboard') }}" class="btn btn-sm btn-outline-primary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    {{-- AREA YANG AKAN DICETAK --}}
    <div id="area-laporan" class="card shadow-sm border-0 mb-4 p-4">
        
        {{-- Header Laporan (Hanya muncul rapi saat di-print) --}}
        <div class="text-center mb-4 d-none d-print-block">
            <h4 class="fw-bold mb-1">SAVEMATE - SMART CANTEEN</h4>
            <p class="text-muted mb-0">Laporan Riwayat Transaksi Siswa</p>
            <hr class="border-dark">
        </div>

        <div class="row mb-3 no-print">
            <div class="col-md-6">
                <strong>Nama:</strong> {{ $student->name }}<br>
                <strong>NIS:</strong> {{ $student->nis }} | <strong>Kelas:</strong> {{ $student->class }}
            </div>
            <div class="col-md-6 text-md-end">
                <strong>Periode:</strong> {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
            </div>
        </div>

        {{-- FORM FILTER (Tidak akan tercetak) --}}
        <form method="GET" action="{{ route('siswa.riwayat') }}" class="row g-2 align-items-end mb-4 no-print">
            <div class="col-6 col-md-3">
                <label class="form-label small fw-bold text-muted">Dari Tanggal</label>
                <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
            </div>
            <div class="col-6 col-md-3">
                <label class="form-label small fw-bold text-muted">Sampai Tanggal</label>
                <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
            </div>
            <div class="col-12 col-md-6 d-flex gap-2 mt-3 mt-md-0">
                <button type="submit" class="btn btn-primary flex-grow-1"><i class="bi bi-funnel"></i> Filter</button>
                <button type="button" onclick="cetakLaporan()" class="btn btn-danger flex-grow-1">
                    <i class="bi bi-printer"></i> Cetak / Simpan PDF
                </button>
            </div>
        </form>

        {{-- TABEL RIWAYAT (Akan tercetak rapi) --}}
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Tanggal</th>
                        <th>Jenis</th>
                        <th>Keterangan</th>
                        <th class="text-end">Nominal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($activities as $act)
                    <tr>
                        <td>
                            {{ $act->activity_type == 'purchase' ? $act->transaction_at->format('d/m/Y H:i') : $act->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td>
                            @if($act->activity_type == 'topup')
                                <span class="badge bg-success bg-opacity-10 text-success border border-success">Topup</span>
                            @else
                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger">Beli</span>
                            @endif
                        </td>
                        <td>
                            @if($act->activity_type == 'topup')
                                Pengisian saldo oleh Admin
                            @else
                                <strong>{{ $act->canteen->name }}</strong><br>
                                <small class="text-muted">
                                    @foreach($act->items as $item)
                                        {{ $item->menu->name }} (x{{ $item->qty }}){{ !$loop->last ? ',' : '' }}
                                    @endforeach
                                </small>
                            @endif
                        </td>
                        <td class="text-end fw-bold">
                            @if($act->activity_type == 'topup')
                                <span class="text-success">+ Rp {{ number_format($act->amount, 0, ',', '.') }}</span>
                            @else
                                <span class="text-danger">- Rp {{ number_format($act->total, 0, ',', '.') }}</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">
                            Tidak ada transaksi pada periode ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Footer Laporan (Hanya muncul saat di-print) --}}
        <div class="mt-4 pt-3 border-top d-none d-print-block text-end text-muted small">
            Dicetak pada: {{ now()->format('d/m/Y H:i:s') }} WIB<br>
            Sistem SaveMate - Smart Canteen
        </div>
    </div>
@endsection

@push('scripts')
<script>
function cetakLaporan() {
    // Panggil dialog print bawaan browser/HP
    window.print();
}
</script>
@endpush