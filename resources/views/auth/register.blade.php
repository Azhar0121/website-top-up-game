@extends('layouts.customer')

@section('title', 'Daftar Akun')

@section('content')

    <div class="container py-5" style="max-width: 460px;">

        <h1 class="section-heading mb-4 text-center" style="font-size:1.6rem;">Buat Akun Baru</h1>

        <div class="checkout-panel">

            @if ($errors->any())
                <div class="alert alert-danger py-2 small">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('register.submit') }}" method="POST" novalidate>
                @csrf

                <div class="mb-3">
                    <label for="name" class="form-label fw-semibold" style="font-size:.9rem;">Nama</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}"
                           class="form-control app-input @error('name') is-invalid @enderror"
                           placeholder="Nama kamu" required autofocus>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold" style="font-size:.9rem;">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}"
                           class="form-control app-input @error('email') is-invalid @enderror"
                           placeholder="nama@email.com" required>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label fw-semibold" style="font-size:.9rem;">Password</label>
                    <input type="password" name="password" id="password"
                           class="form-control app-input @error('password') is-invalid @enderror"
                           placeholder="Minimal 8 karakter" required>
                </div>

                <div class="mb-3">
                    <label for="password_confirmation" class="form-label fw-semibold" style="font-size:.9rem;">Ulangi Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation"
                           class="form-control app-input" placeholder="Ulangi password" required>
                </div>

                <button type="submit" class="btn app-btn-cta w-100">Daftar</button>
            </form>

            <p class="text-center small mt-4 mb-0" style="color: var(--color-text-muted);">
                Sudah punya akun? <a href="{{ route('login') }}" class="fw-semibold">Masuk di sini</a>
            </p>
        </div>
    </div>

@endsection
