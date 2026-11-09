@extends('layouts.app')
@section('title', 'Data Kantin')

@section('sidebar-menu')
    <a href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2"></i> Dashboard</a>
    <a href="{{ route('admin.siswa.index') }}"><i class="bi bi-people"></i> Data Siswa</a>
    <a href="{{ route('admin.kantin.index') }}" class="active"><i class="bi bi-shop"></i> Data Kantin</a>
    <a href="{{ route('admin.menu.index') }}"><i class="bi bi-list-ul"></i> Menu Kantin</a>
    <a href="{{ route('admin.laporan') }}"><i class="bi bi-graph-up"></i> Laporan</a>
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">Data Kantin</h3>
        <a href="{{ route('admin.kantin.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Tambah Kantin
        </a>
    </div>

    <div class="row g-3">
        @forelse($canteens as $c)
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="flex-grow-1 pe-2">
                            <h5 class="card-title mb-1 fw-bold">{{ $c->name }}</h5>
                            <p class="mb-1 text-muted small"><i class="bi bi-person"></i> {{ $c->owner }}</p>
                            <p class="mb-0 text-muted small"><i class="bi bi-geo-alt"></i> {{ $c->location ?? 'Lokasi tidak diatur' }}</p>
                        </div>
                        
                        <!-- Tombol Aksi (Edit & Hapus) -->
                        <div class="d-flex gap-2">
                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editModal{{ $c->id }}" title="Edit Data Kantin">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <form action="{{ route('admin.kantin.destroy', $c) }}" method="POST" onsubmit="return confirm('Yakin hapus kantin ini? Semua data menu dan riwayat akan ikut terhapus.')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" title="Hapus Kantin">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>

                    <hr class="my-3">

                    <!-- STATISTIK HARI INI -->
                    <div class="row text-center mb-3">
                        <div class="col-6 border-end">
                            <small class="text-muted d-block">Transaksi Hari Ini</small>
                            <h4 class="fw-bold text-primary mb-0">{{ $c->today_transaction_count }}</h4>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block">Pendapatan Hari Ini</small>
                            <h5 class="fw-bold text-success mb-0">Rp {{ number_format($c->today_revenue, 0, ',', '.') }}</h5>
                        </div>
                    </div>

                    <!-- TOMBOL BUKA MODAL REKAPITULASI -->
                    <button class="btn btn-outline-primary w-100" data-bs-toggle="modal" data-bs-target="#recapModal{{ $c->id }}">
                        <i class="bi bi-bar-chart-line"></i> Lihat Rekapitulasi Harian
                    </button>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- MODAL EDIT DATA KANTIN -->
            <!-- ========================================== -->
            <div class="modal fade" id="editModal{{ $c->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <form action="{{ route('admin.kantin.update', $c) }}" method="POST" class="modal-content">
                        @csrf
                        @method('PUT')
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title"><i class="bi bi-pencil-square"></i> Edit Data: {{ $c->name }}</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Nama Kantin</label>
                                    <input type="text" name="name" class="form-control" value="{{ $c->name }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Nama Pemilik</label>
                                    <input type="text" name="owner" class="form-control" value="{{ $c->owner }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Lokasi</label>
                                    <input type="text" name="location" class="form-control" value="{{ $c->location }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">No. HP Pemilik</label>
                                    <input type="text" name="phone" class="form-control" value="{{ $c->user->phone }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email Login</label>
                                    <input type="email" name="email" class="form-control" value="{{ $c->user->email }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Password Baru <span class="text-muted small">(Kosongkan jika tidak ingin diubah)</span></label>
                                    <input type="password" name="password" class="form-control" placeholder="******">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
            <!-- ========================================== -->
            <!-- AKHIR MODAL EDIT -->
            <!-- ========================================== -->

            <!-- ===== MODAL REKAPITULASI PER HARI (Tetap sama) ===== -->
            <div class="modal fade" id="recapModal{{ $c->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title">
                                <i class="bi bi-calendar3"></i> Rekapitulasi Transaksi: {{ $c->name }}
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            @if($c->daily_recap->isEmpty())
                                <div class="text-center py-4 text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    Belum ada riwayat transaksi.
                                </div>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-hover table-striped mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Tanggal</th>
                                                <th class="text-center">Jumlah Transaksi</th>
                                                <th class="text-end">Total Pendapatan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($c->daily_recap as $recap)
                                            <tr>
                                                <td>
                                                    {{ \Carbon\Carbon::parse($recap->date)->isoFormat('dddd, D MMMM Y') }}
                                                    @if(\Carbon\Carbon::parse($recap->date)->isToday())
                                                        <span class="badge bg-success ms-1">Hari Ini</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-primary rounded-pill">{{ $recap->total_trx }}</span>
                                                </td>
                                                <td class="text-end fw-bold text-success">
                                                    Rp {{ number_format($recap->total_revenue, 0, ',', '.') }}
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot class="table-light">
                                            <tr>
                                                <th colspan="2" class="text-end">Total Keseluruhan:</th>
                                                <th class="text-end text-primary">
                                                    Rp {{ number_format($c->total_revenue, 0, ',', '.') }}
                                                </th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            @endif
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- ===== AKHIR MODAL REKAPITULASI ===== -->

        </div>
        @empty
        <div class="col-12">
            <div class="card p-5 text-center text-muted">
                <i class="bi bi-shop fs-1 mb-3"></i>
                <p class="mb-0">Belum ada data kantin. Silakan tambahkan kantin baru.</p>
            </div>
        </div>
        @endforelse
    </div>
@endsection