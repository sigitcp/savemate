@extends('layouts.app')
@section('title', 'Tambah Siswa')

@section('sidebar-menu')
    <a href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2"></i> Dashboard</a>
    <a href="{{ route('admin.siswa.index') }}" class="active"><i class="bi bi-people"></i> Data Siswa</a>
    <a href="{{ route('admin.kantin.index') }}"><i class="bi bi-shop"></i> Data Kantin</a>
    <a href="{{ route('admin.menu.index') }}"><i class="bi bi-list-ul"></i> Menu Kantin</a>
    <a href="{{ route('admin.laporan') }}"><i class="bi bi-graph-up"></i> Laporan</a>
@endsection

@section('content')
    <h3 class="mb-4">Tambah Siswa Baru</h3>

    {{-- PENTING: Tampilkan semua error validasi di sini --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <h5><i class="bi bi-exclamation-triangle"></i> Gagal Menyimpan Data:</h5>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card p-4">
        <form action="{{ route('admin.siswa.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">NIS</label>
                    <input type="text" name="nis" class="form-control" value="{{ old('nis') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Kelas</label>
                    <input type="text" name="class" class="form-control" value="{{ old('class') }}" placeholder="Contoh: 7A" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">No. HP (Opsional)</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                    <small class="text-muted">Minimal 6 karakter</small>
                </div>
                
                <div class="col-12">
                    <label class="form-label">Foto Siswa (Opsional)</label>
                    <input type="file" name="photo" class="form-control" accept="image/jpeg,image/png,image/jpg">
                    <small class="text-muted">Format: JPG, PNG (Maksimal 2MB). Kosongkan jika tidak ada foto.</small>
                </div>
            </div>

            <div class="alert alert-info mt-3">
                <i class="bi bi-info-circle"></i> Token RFID akan digenerate otomatis oleh sistem.
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Simpan & Generate Token
                </button>
                <a href="{{ route('admin.siswa.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
@endsection