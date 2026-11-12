@extends('layouts.app')
@section('title', 'Dashboard Siswa')

@section('sidebar-menu')
    <a href="{{ route('siswa.dashboard') }}" class="active"><i class="bi bi-house"></i> Dashboard</a>
    <a href="{{ route('siswa.riwayat') }}"><i class="bi bi-clock-history"></i> Riwayat Transaksi</a>
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-0 fw-bold">Halo, {{ explode(' ', $student->name)[0] }}! 👋</h3>
            <small class="text-muted">Selamat datang di SaveMate</small>
        </div>
    </div>

    <!-- KARTU STATISTIK UTAMA -->
    <div class="row g-3 mb-4">
        <!-- Kartu Saldo -->
        <div class="col-12 col-md-4">
            <div class="card card-stat p-4 bg-success text-white h-100 border-0 shadow-sm">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h6 class="mb-1 opacity-75">Saldo Saya</h6>
                        <h2 class="mb-0 fw-bold">Rp {{ number_format($wallet->balance, 0, ',', '.') }}</h2>
                    </div>
                    <i class="bi bi-wallet2 fs-1 opacity-50"></i>
                </div>
            </div>
        </div>

        <!-- Kartu Total Transaksi -->
        <div class="col-6 col-md-4">
            <div class="card card-stat p-3 bg-primary text-white h-100 border-0 shadow-sm text-center">
                <i class="bi bi-cart-check fs-2 mb-2 opacity-75"></i>
                <h6 class="mb-1 small opacity-75">Total Transaksi</h6>
                <h3 class="mb-0 fw-bold">{{ $total_transaksi }}</h3>
                <small class="opacity-75">Kali belanja</small>
            </div>
        </div>

        <!-- Kartu Informasi -->
        <div class="col-6 col-md-4">
            <div class="card card-stat p-3 bg-info text-white h-100 border-0 shadow-sm text-center">
                <i class="bi bi-person-badge fs-2 mb-2 opacity-75"></i>
                <h6 class="mb-1 small opacity-75">Informasi Siswa</h6>
                <p class="mb-0 fw-bold">{{ $student->nis }}</p>
                <small class="opacity-75">Kelas {{ $student->class }}</small>
            </div>
        </div>
    </div>

    <!-- BAGIAN CHART / GRAFIK -->
    <div class="row g-3 mb-4">
        <!-- Chart 1: Kategori Jajanan (Pie Chart) -->
        <div class="col-12 col-md-6">
            <div class="card shadow-sm border-0 p-3 h-100">
                <h6 class="fw-bold mb-3 text-center text-primary">🍔 Kategori Jajanan Favorit</h6>
                <div style="position: relative; height: 250px;">
                    <canvas id="kategoriChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Chart 2: Kantin Favorit (Doughnut Chart) -->
        <div class="col-12 col-md-6">
            <div class="card shadow-sm border-0 p-3 h-100">
                <h6 class="fw-bold mb-3 text-center text-success">🏪 Kantin Favorit</h6>
                <div style="position: relative; height: 250px;">
                    <canvas id="kantinChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <!-- Chart 3: Aktivitas Mingguan (Line Chart) -->
        <div class="col-12">
            <div class="card shadow-sm border-0 p-3">
                <h6 class="fw-bold mb-3 text-center text-info">📈 Aktivitas Transaksi Mingguan</h6>
                <div style="position: relative; height: 300px;">
                    <canvas id="weeklyChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Tombol Aksi -->
    <div class="d-grid gap-2 d-md-flex justify-content-md-start mb-4">
        <a href="{{ route('siswa.riwayat') }}" class="btn btn-primary btn-lg w-100 w-md-auto shadow-sm d-flex align-items-center justify-content-center gap-2">
            <i class="bi bi-clock-history fs-5"></i> 
            <span>Lihat Riwayat Transaksi</span>
        </a>
    </div>
@endsection

@push('scripts')
<!-- Load Chart.js dari CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const kategoriLabels = @json($kategoriLabels);
    const kategoriValues = @json($kategoriValues);
    
    const kantinLabels = @json($kantinLabels);
    const kantinValues = @json($kantinValues);
    
    const hariLabels = @json($hariLabels);
    const hariValues = @json($hariValues);

    // Fallback jika data masih kosong (agar chart tidak error)
    const kLabels = kategoriLabels.length ? kategoriLabels : ['Belum ada data'];
    const kValues = kategoriValues.length ? kategoriValues : [1];
    
    const kanLabels = kantinLabels.length ? kantinLabels : ['Belum ada data'];
    const kanValues = kantinValues.length ? kantinValues : [1];

    // 1. Render Pie Chart (Kategori)
    new Chart(document.getElementById('kategoriChart'), {
        type: 'pie',
        data: {
            labels: kLabels,
            datasets: [{
                data: kValues,
                backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56'], // Merah, Biru, Kuning
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom' } }
        }
    });

    // 2. Render Doughnut Chart (Kantin)
    new Chart(document.getElementById('kantinChart'), {
        type: 'doughnut',
        data: {
            labels: kanLabels,
            datasets: [{
                data: kanValues,
                backgroundColor: ['#4BC0C0', '#9966FF', '#FF9F40'], // Hijau Tosca, Ungu, Orange
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom' } }
        }
    });

    // 3. Render Line Chart (Mingguan)
    new Chart(document.getElementById('weeklyChart'), {
        type: 'line',
        data: {
            labels: hariLabels,
            datasets: [{
                label: 'Jumlah Transaksi',
                data: hariValues,
                borderColor: '#667eea',
                backgroundColor: 'rgba(102, 126, 234, 0.2)',
                borderWidth: 3,
                fill: true,
                tension: 0.4 // Membuat garis melengkung halus
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { 
                    beginAtZero: true, 
                    ticks: { stepSize: 1 } // Hanya angka bulat
                }
            },
            plugins: { legend: { display: false } }
        }
    });
</script>
@endpush