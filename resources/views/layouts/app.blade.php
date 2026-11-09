<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SaveMate')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .sidebar a {
            color: rgba(255, 255, 255, 0.9);
            text-decoration: none;
            padding: 12px 20px;
            display: block;
            border-radius: 8px;
            margin: 4px 10px;
            transition: all 0.3s;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: rgba(255, 255, 255, 0.2);
            color: white;
        }

        .card-stat {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            transition: transform 0.2s;
        }

        .card-stat:hover {
            transform: translateY(-3px);
        }

        .menu-card {
            cursor: pointer;
            transition: all 0.2s;
            border: 2px solid transparent;
        }

        .menu-card:hover {
            border-color: #667eea;
        }

        .menu-card.selected {
            border-color: #28a745;
            background: #f0fff4;
        }

        /* Tambahan: Memastikan bottom nav tidak menutupi konten di HP */
        @media (max-width: 767.98px) {
            main {
                padding-bottom: 80px !important;
            }
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar (Hanya muncul di Desktop/Tablet) -->
            <nav class="d-none d-md-block col-md-3 col-lg-2 sidebar p-3">
                <h4 class="mb-4">
                    <i class="bi bi-shop"></i> SaveMate
                </h4>
                <hr style="border-color: rgba(255,255,255,0.3);">
                <p class="small text-white-50 mb-1">Login sebagai:</p>
                <p class="fw-bold mb-3">{{ auth()->user()->name }}</p>

                @yield('sidebar-menu')

                <hr style="border-color: rgba(255,255,255,0.3);">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-outline-light btn-sm w-100">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </button>
                </form>
            </nav>

            <!-- Content Area (Ditambahkan pb-5 untuk ruang di HP) -->
            <main class="col-md-9 col-lg-10 p-3 p-md-4 pb-5 pb-md-4">
                
                {{-- ===== PESAN ERROR & SUKSES ===== --}}
                @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <h6 class="alert-heading"><i class="bi bi-exclamation-triangle-fill"></i> Gagal Memproses:</h6>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-x-circle-fill"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif
                {{-- ===== AKHIR PESAN ===== --}}

                @yield('content')
            </main>
        </div>

        {{-- ===== BOTTOM NAVIGATION (Hanya muncul di HP, khusus role Kantin) ===== --}}
        @if(auth()->check() && auth()->user()->role === 'kantin')
        <nav class="d-md-none fixed-bottom bg-white border-top shadow-lg p-2 d-flex justify-content-around" style="z-index: 1050;">
            <a href="{{ route('kantin.dashboard') }}" class="text-center text-decoration-none {{ request()->routeIs('kantin.dashboard') ? 'text-primary fw-bold' : 'text-muted' }}">
                <i class="bi bi-speedometer2 d-block fs-4"></i>
                <small style="font-size: 10px;">Dashboard</small>
            </a>
            <a href="{{ route('kantin.transaksi') }}" class="text-center text-decoration-none {{ request()->routeIs('kantin.transaksi') ? 'text-primary fw-bold' : 'text-muted' }}">
                <i class="bi bi-cart-check d-block fs-4"></i>
                <small style="font-size: 10px;">Transaksi</small>
            </a>
            <a href="{{ route('kantin.riwayat') }}" class="text-center text-decoration-none {{ request()->routeIs('kantin.riwayat') ? 'text-primary fw-bold' : 'text-muted' }}">
                <i class="bi bi-clock-history d-block fs-4"></i>
                <small style="font-size: 10px;">Riwayat</small>
            </a>
        </nav>
        @endif
        {{-- ===== AKHIR BOTTOM NAVIGATION ===== --}}

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>

</html>