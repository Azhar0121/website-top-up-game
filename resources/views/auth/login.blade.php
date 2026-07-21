@extends('layouts.customer')

@section('title', 'Masuk')

@section('content')

    <div class="container py-5" style="max-width: 460px;">

        <h1 class="section-heading mb-4 text-center" style="font-size:1.6rem;">Masuk ke Akun Kamu</h1>

        <div class="checkout-panel">

            @if (session('status'))
                <div class="alert alert-success py-2 small">{{ session('status') }}</div>
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
