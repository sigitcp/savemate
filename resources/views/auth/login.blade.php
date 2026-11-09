@extends('layouts.guest')

@section('content')
    <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control form-control-lg" value="{{ old('email') }}" required autofocus>
            @error('email') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control form-control-lg" required>
        </div>
        <button type="submit" class="btn btn-primary btn-lg w-100">
            <i class="bi bi-box-arrow-in-right"></i> Login
        </button>
        
        <hr class="my-4">
        <div class="small text-muted">
            <strong>Demo Accounts:</strong><br>
            Admin: admin@savemate.com<br>
            Kantin: kantin@savemate.com<br>
            Siswa: siswa@savemate.com<br>
            Password: <code>password</code>
        </div>
    </form>
@endsection