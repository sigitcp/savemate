@extends('layouts.app')
@section('title', 'Tambah Menu')

@section('sidebar-menu')
    <a href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2"></i> Dashboard</a>
    <a href="{{ route('admin.siswa.index') }}"><i class="bi bi-people"></i> Data Siswa</a>
    <a href="{{ route('admin.kantin.index') }}"><i class="bi bi-shop"></i> Data Kantin</a>
    <a href="{{ route('admin.menu.index') }}" class="active"><i class="bi bi-list-ul"></i> Menu Kantin</a>
    <a href="{{ route('admin.laporan') }}"><i class="bi bi-graph-up"></i> Laporan</a>
@endsection

@section('content')
    <h3 class="mb-4">Tambah Menu Baru</h3>

    <div class="card p-4">
        <form action="{{ route('admin.menu.store') }}" method="POST">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Kantin</label>
                    <select name="canteen_id" class="form-select" required>
                        <option value="">-- Pilih Kantin --</option>
                        @foreach($canteens as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nama Menu</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Kategori</label>
                    <select name="category" class="form-select" required>
                        <option value="Makanan">Makanan</option>
                        <option value="Minuman">Minuman</option>
                        <option value="Snack">Snack</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Harga (Rp)</label>
                    <input type="number" name="price" class="form-control" required min="0">
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Simpan</button>
                <a href="{{ route('admin.menu.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
@endsection