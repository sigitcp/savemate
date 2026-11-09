@extends('layouts.app')
@section('title', 'Tambah Kantin')

@section('sidebar-menu')
    <a href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2"></i> Dashboard</a>
    <a href="{{ route('admin.siswa.index') }}"><i class="bi bi-people"></i> Data Siswa</a>
    <a href="{{ route('admin.kantin.index') }}" class="active"><i class="bi bi-shop"></i> Data Kantin</a>
    <a href="{{ route('admin.menu.index') }}"><i class="bi bi-list-ul"></i> Menu Kantin</a>
    <a href="{{ route('admin.laporan') }}"><i class="bi bi-graph-up"></i> Laporan</a>
@endsection

@section('content')
    <h3 class="mb-4">Tambah Kantin Baru</h3>

    <div class="card p-4">
        <form action="{{ route('admin.kantin.store') }}" method="POST">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nama Kantin</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nama Pemilik</label>
                    <input type="text" name="owner" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Lokasi</label>
                    <input type="text" name="location" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">No. HP Pemilik</label>
                    <input type="text" name="phone" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email Login</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Simpan</button>
                <a href="{{ route('admin.kantin.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
@endsection