@extends('layouts.auth')

@section('title', 'Daftar Akun')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/auth-theme.css') }}">
@endpush

@section('content')

    <div class="auth-shell">

        <div class="auth-brand-panel">
            <div class="auth-brand-panel-content">
                <div class="auth-brand-logo">
                    TopUp<span class="app-brand-accent">Kilat</span>
                </div>

                <h2 class="auth-brand-title">Daftar sekali, top up seumur hidup lebih cepat.</h2>
                <p class="auth-brand-subtitle">Bikin akun gratis buat simpan riwayat transaksi dan checkout lebih cepat tiap kali balik lagi.</p>

                <ul class="auth-trust-list">
                    <li>
                        <span class="auth-trust-icon"><i class="bi bi-lightning-charge"></i></span>
                        <div><strong>Checkout Lebih Cepat</strong><span>Data ID game & kontak otomatis tersimpan untuk order berikutnya.</span></div>
                    </li>
                    <li>
                        <span class="auth-trust-icon"><i class="bi bi-receipt-cutoff"></i></span>
                        <div><strong>Pantau Semua Transaksi</strong><span>Lihat status pesanan lama kapan saja dari Akun Saya.</span></div>
                    </li>
                    <li>
                        <span class="auth-trust-icon"><i class="bi bi-gift"></i></span>
                        <div><strong>Info Promo Duluan</strong><span>Dapat kabar flash sale & voucher terbaru lebih awal.</span></div>
                    </li>
                </ul>
            </div>
        </div>

        <div class="auth-form-panel">
            <div class="auth-form-card">

                <h1 class="auth-form-title">Buat Akun Baru</h1>
                <p class="auth-form-subtitle">Sudah punya akun? <a href="{{ route('login') }}" class="fw-semibold">Masuk di sini</a></p>

                @if ($errors->any())
                    <div class="alert alert-danger py-2 small">
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('register.submit') }}" method="POST" novalidate id="register-form">
                    @csrf
                    <input type="hidden" name="recaptcha_token" id="recaptcha_token">

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

                    <button type="submit" class="btn app-btn-cta w-100" id="register-submit-btn">
                        <i class="bi bi-person-plus me-1"></i> Daftar
                    </button>
                </form>

                <div class="auth-divider">atau</div>

                <a href="{{ route('auth.google') }}" class="btn app-btn-outline w-100 d-flex align-items-center justify-content-center gap-2">
                    <svg width="18" height="18" viewBox="0 0 18 18"><path fill="#4285F4" d="M17.64 9.2c0-.64-.06-1.25-.16-1.84H9v3.48h4.84a4.14 4.14 0 0 1-1.8 2.72v2.26h2.92c1.7-1.57 2.68-3.88 2.68-6.62z"/><path fill="#34A853" d="M9 18c2.43 0 4.47-.8 5.96-2.18l-2.92-2.26c-.81.54-1.85.86-3.04.86-2.34 0-4.32-1.58-5.03-3.7H.98v2.33A9 9 0 0 0 9 18z"/><path fill="#FBBC05" d="M3.97 10.72A5.4 5.4 0 0 1 3.68 9c0-.6.1-1.18.29-1.72V4.95H.98A9 9 0 0 0 0 9c0 1.45.35 2.83.98 4.05l2.99-2.33z"/><path fill="#EA4335" d="M9 3.58c1.32 0 2.51.45 3.44 1.35l2.58-2.58C13.46.89 11.43 0 9 0A9 9 0 0 0 .98 4.95l2.99 2.33C4.68 5.16 6.66 3.58 9 3.58z"/></svg>
                    Daftar dengan Google
                </a>

                <p class="auth-form-footer">
                    Dengan mendaftar, kamu setuju dengan
                    <a href="{{ route('terms') }}" class="fw-semibold">Syarat &amp; Ketentuan</a> kami.
                </p>

                <p class="recaptcha-disclosure">
                    Situs ini dilindungi reCAPTCHA dan berlaku
                    <a href="https://policies.google.com/privacy" target="_blank" rel="noopener">Kebijakan Privasi</a> serta
                    <a href="https://policies.google.com/terms" target="_blank" rel="noopener">Persyaratan Layanan</a> Google.
                </p>
            </div>
        </div>

    </div>

@endsection

@push('scripts')
<script>
    (function () {
        const form = document.getElementById('register-form');
        const submitBtn = document.getElementById('register-submit-btn');
        const tokenInput = document.getElementById('recaptcha_token');
        const siteKey = window.APP_CONFIG?.recaptchaSiteKey;

        form.addEventListener('submit', function (event) {
            if (!siteKey || typeof grecaptcha === 'undefined') {
                return;
            }

            event.preventDefault();
            submitBtn.disabled = true;

            grecaptcha.ready(function () {
                grecaptcha.execute(siteKey, { action: 'register' }).then(function (token) {
                    tokenInput.value = token;
                    form.submit();
                }).catch(function () {
                    submitBtn.disabled = false;
                    form.submit();
                });
            });
        });
    })();
</script>
@endpush
