@extends('layouts.app')
@section('title', 'Laporan Transaksi')

@section('sidebar-menu')
    <a href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2"></i> Dashboard</a>
    <a href="{{ route('admin.siswa.index') }}"><i class="bi bi-people"></i> Data Siswa</a>
    <a href="{{ route('admin.kantin.index') }}"><i class="bi bi-shop"></i> Data Kantin</a>
    <a href="{{ route('admin.menu.index') }}"><i class="bi bi-list-ul"></i> Menu Kantin</a>
    <a href="{{ route('admin.laporan') }}" class="active"><i class="bi bi-graph-up"></i> Laporan</a>
@endsection

@section('content')
    <h3 class="mb-4">Laporan Transaksi</h3>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Tanggal</th>
                        <th>Siswa</th>
                        <th>Kantin</th>
                        <th>Menu</th>
                        <th>Total</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $t)
                    <tr>
                        <td>{{ $t->transaction_at->format('d/m/Y H:i') }}</td>
                        <td>{{ $t->student->name }}</td>
                        <td>{{ $t->canteen->name }}</td>
                        <td>
                            @foreach($t->items as $item)
                                {{ $item->menu->name }} ({{ $item->qty }}x)<br>
                            @endforeach
                        </td>
                        <td>Rp {{ number_format($t->total, 0, ',', '.') }}</td>
                        <td><span class="badge bg-{{ $t->status == 'success' ? 'success' : 'danger' }}">{{ ucfirst($t->status) }}</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">Belum ada transaksi</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3">{{ $transactions->links() }}</div>
    </div>
@endsection