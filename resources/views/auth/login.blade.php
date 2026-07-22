@extends('layouts.customer')

@section('title', 'Masuk')

@section('content')

    <div class="container py-5" style="max-width: 460px;">

        <h1 class="section-heading mb-4 text-center" style="font-size:1.6rem;">Masuk ke Akun Kamu</h1>

        <div class="checkout-panel">

            @if (session('status'))
                <div class="alert alert-success py-2 small">{{ session('status') }}</div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger py-2 small">{{ session('error') }}</div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger py-2 small">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <a href="{{ route('auth.google') }}" class="btn app-btn-outline w-100 d-flex align-items-center justify-content-center gap-2 mb-3">
                <svg width="18" height="18" viewBox="0 0 18 18"><path fill="#4285F4" d="M17.64 9.2c0-.64-.06-1.25-.16-1.84H9v3.48h4.84a4.14 4.14 0 0 1-1.8 2.72v2.26h2.92c1.7-1.57 2.68-3.88 2.68-6.62z"/><path fill="#34A853" d="M9 18c2.43 0 4.47-.8 5.96-2.18l-2.92-2.26c-.81.54-1.85.86-3.04.86-2.34 0-4.32-1.58-5.03-3.7H.98v2.33A9 9 0 0 0 9 18z"/><path fill="#FBBC05" d="M3.97 10.72A5.4 5.4 0 0 1 3.68 9c0-.6.1-1.18.29-1.72V4.95H.98A9 9 0 0 0 0 9c0 1.45.35 2.83.98 4.05l2.99-2.33z"/><path fill="#EA4335" d="M9 3.58c1.32 0 2.51.45 3.44 1.35l2.58-2.58C13.46.89 11.43 0 9 0A9 9 0 0 0 .98 4.95l2.99 2.33C4.68 5.16 6.66 3.58 9 3.58z"/></svg>
                Masuk dengan Google
            </a>

            <div class="d-flex align-items-center gap-2 mb-3">
                <hr class="flex-grow-1 my-0"><span class="small" style="color: var(--color-text-muted);">atau</span><hr class="flex-grow-1 my-0">
            </div>

            <form action="{{ route('login.submit') }}" method="POST" novalidate>
                @csrf

                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold" style="font-size:.9rem;">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}"
                           class="form-control app-input @error('email') is-invalid @enderror"
                           placeholder="nama@email.com" required autofocus>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label fw-semibold" style="font-size:.9rem;">Password</label>
                    <input type="password" name="password" id="password"
                           class="form-control app-input @error('password') is-invalid @enderror"
                           placeholder="••••••••" required>
                </div>

                <div class="form-check mb-3">
                    <input type="checkbox" name="remember" id="remember" class="form-check-input">
                    <label for="remember" class="form-check-label small">Ingat saya di perangkat ini</label>
                </div>

                <button type="submit" class="btn app-btn-cta w-100">Masuk</button>
            </form>

            <p class="text-center small mt-4 mb-0" style="color: var(--color-text-muted);">
                Belum punya akun? <a href="{{ route('register') }}" class="fw-semibold">Daftar di sini</a>
            </p>
        </div>
    </div>

@endsection
