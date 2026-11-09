<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Login - SaveMate')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    body {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
        display: flex;
        align-items: center;
    }

    .login-card {
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
    }
    </style>
</head>

<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card login-card p-4">
                    <div class="text-center mb-4">
                        <img src="{{ asset('logo.png') }}" alt="SaveMate Logo" class="mb-3"
                            style="max-width: 120px; height: auto;">
                        <h2 class="fw-bold">SaveMate</h2>
                        <p class="text-muted">Sistem Pembayaran Kantin Digital</p>
                    </div>
                    @yield('content')
                </div>
            </div>
        </div>
    </div>
</body>

</html>