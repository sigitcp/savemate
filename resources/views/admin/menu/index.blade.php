@extends('layouts.app')
@section('title', 'Menu Kantin')

@section('sidebar-menu')
    <a href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2"></i> Dashboard</a>
    <a href="{{ route('admin.siswa.index') }}"><i class="bi bi-people"></i> Data Siswa</a>
    <a href="{{ route('admin.kantin.index') }}"><i class="bi bi-shop"></i> Data Kantin</a>
    <a href="{{ route('admin.menu.index') }}" class="active"><i class="bi bi-list-ul"></i> Menu Kantin</a>
    <a href="{{ route('admin.laporan') }}"><i class="bi bi-graph-up"></i> Laporan</a>
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">Menu Kantin</h3>
        <a href="{{ route('admin.menu.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Tambah Menu
        </a>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Nama Menu</th>
                        <th>Kantin</th>
                        <th>Kategori</th>
                        <th>Harga</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($menus as $m)
                    <tr>
                        <td>{{ $m->name }}</td>
                        <td>{{ $m->canteen->name }}</td>
                        <td><span class="badge bg-secondary">{{ $m->category }}</span></td>
                        <td>Rp {{ number_format($m->price, 0, ',', '.') }}</td>
                        <td>
                            <form action="{{ route('admin.menu.destroy', $m) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">Belum ada menu</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection