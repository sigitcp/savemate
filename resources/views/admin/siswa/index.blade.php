@extends('layouts.app')
@section('title', 'Data Siswa')

@section('sidebar-menu')
    <a href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2"></i> Dashboard</a>
    <a href="{{ route('admin.siswa.index') }}" class="active"><i class="bi bi-people"></i> Data Siswa</a>
    <a href="{{ route('admin.kantin.index') }}"><i class="bi bi-shop"></i> Data Kantin</a>
    <a href="{{ route('admin.menu.index') }}"><i class="bi bi-list-ul"></i> Menu Kantin</a>
    <a href="{{ route('admin.laporan') }}"><i class="bi bi-graph-up"></i> Laporan</a>
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">Data Siswa</h3>
        <a href="{{ route('admin.siswa.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Tambah Siswa
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Foto</th>
                        <th>NIS</th>
                        <th>Nama</th>
                        <th>Kelas</th>
                        <th>Token RFID</th>
                        <th>Saldo</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $s)
                    <tr>
                        <td>
                            @if($s->photo)
                                <img src="{{ asset($s->photo) }}" alt="Foto {{ $s->name }}" 
                                     class="rounded-circle border" width="45" height="45" style="object-fit: cover;">
                            @else
                                <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center" 
                                     style="width:45px; height:45px; font-size: 1.2rem; font-weight: bold;">
                                    {{ strtoupper(substr($s->name, 0, 1)) }}
                                </div>
                            @endif
                        </td>
                        <td class="fw-semibold">{{ $s->nis }}</td>
                        <td>{{ $s->name }}</td>
                        <td><span class="badge bg-light text-dark border">{{ $s->class }}</span></td>
                        
                        <!-- KOLOM TOKEN DENGAN TOMBOL COPY -->
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <code class="bg-warning bg-opacity-10 text-warning-emphasis p-1 rounded mb-0" id="token-{{ $s->id }}">
                                    {{ $s->rfid_token }}
                                </code>
                                <button class="btn btn-sm btn-outline-secondary" onclick="copyToken('{{ $s->rfid_token }}', '{{ $s->name }}')" title="Salin Token">
                                    <i class="bi bi-clipboard"></i>
                                </button>
                            </div>
                        </td>
                        <!-- AKHIR KOLOM TOKEN -->

                        <td class="fw-bold text-success">Rp {{ number_format($s->wallet->balance ?? 0, 0, ',', '.') }}</td>
                        <td class="text-center">
                            <div class="btn-group" role="group">
                                <!-- Tombol Edit -->
                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editModal{{ $s->id }}" title="Edit Data">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                
                                <!-- Tombol Topup -->
                                <button class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#topupModal{{ $s->id }}" title="Topup Saldo">
                                    <i class="bi bi-wallet2"></i>
                                </button>
                                
                                <!-- Tombol Hapus -->
                                <form action="{{ route('admin.siswa.destroy', $s) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus siswa ini? Semua data transaksi dan saldo akan ikut terhapus.')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" title="Hapus Siswa">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>

                            <!-- ========================================== -->
                            <!-- MODAL EDIT DATA SISWA -->
                            <!-- ========================================== -->
                            <div class="modal fade" id="editModal{{ $s->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <form action="{{ route('admin.siswa.update', $s) }}" method="POST" enctype="multipart/form-data" class="modal-content">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-header bg-primary text-white">
                                            <h5 class="modal-title"><i class="bi bi-pencil-square"></i> Edit Data: {{ $s->name }}</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label class="form-label">NIS</label>
                                                    <input type="text" name="nis" class="form-control" value="{{ $s->nis }}" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Nama Lengkap</label>
                                                    <input type="text" name="name" class="form-control" value="{{ $s->name }}" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Kelas</label>
                                                    <input type="text" name="class" class="form-control" value="{{ $s->class }}" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">No. HP</label>
                                                    <input type="text" name="phone" class="form-control" value="{{ $s->user->phone }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Email Login</label>
                                                    <input type="email" name="email" class="form-control" value="{{ $s->user->email }}" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Password Baru <span class="text-muted small">(Kosongkan jika tidak ingin diubah)</span></label>
                                                    <input type="password" name="password" class="form-control" placeholder="******">
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label">Foto Profil</label>
                                                    <input type="file" name="photo" class="form-control" accept="image/jpeg,image/png,image/jpg">
                                                    <small class="text-muted">Format: JPG, PNG (Maksimal 2MB). Biarkan kosong jika tidak ingin mengganti foto.</small>
                                                </div>
                                                
                                                <!-- KOLOM TOKEN RFID (READONLY) -->
                                                <div class="col-12">
                                                    <label class="form-label fw-bold text-warning">Token RFID</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i class="bi bi-wifi"></i></span>
                                                        <input type="text" class="form-control bg-light" value="{{ $s->rfid_token }}" readonly>
                                                        <span class="input-group-text bg-warning text-dark border-warning">
                                                            <i class="bi bi-lock-fill"></i> Tidak dapat diubah
                                                        </span>
                                                    </div>
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

                            <!-- Modal Topup -->
                            <div class="modal fade" id="topupModal{{ $s->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <form action="{{ route('admin.siswa.topup', $s) }}" method="POST" class="modal-content">
                                        @csrf
                                        <div class="modal-header bg-success text-white">
                                            <h5 class="modal-title"><i class="bi bi-wallet2"></i> Topup Saldo - {{ $s->name }}</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p>Saldo saat ini: <strong class="text-success">Rp {{ number_format($s->wallet->balance ?? 0, 0, ',', '.') }}</strong></p>
                                            <div class="mb-3">
                                                <label class="form-label">Nominal Topup</label>
                                                <input type="number" name="amount" class="form-control form-control-lg" required min="1000" placeholder="Contoh: 50000">
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-success">Proses Topup</button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            Belum ada data siswa
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- TOAST NOTIFICATION (Muncul di pojok kanan bawah) -->
    <!-- ========================================== -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1090;">
        <div id="copyToast" class="toast align-items-center text-bg-success border-0 shadow" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body fw-semibold">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    Token <span id="copiedTokenName" class="text-decoration-underline"></span> berhasil disalin!
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>
    <!-- ========================================== -->
    <!-- AKHIR TOAST NOTIFICATION -->
    <!-- ========================================== -->

@endsection

@push('scripts')
<script>
    function copyToken(token, studentName) {
        // Menggunakan Clipboard API modern
        navigator.clipboard.writeText(token).then(() => {
            // Update nama siswa di dalam toast
            document.getElementById('copiedTokenName').textContent = studentName;
            
            // Tampilkan toast notification
            const toastEl = document.getElementById('copyToast');
            const toast = new bootstrap.Toast(toastEl, { delay: 3000 }); // Hilang otomatis setelah 3 detik
            toast.show();
        }).catch(err => {
            console.error('Gagal menyalin: ', err);
            alert('Gagal menyalin token. Silakan coba salin manual.');
        });
    }
</script>
@endpush