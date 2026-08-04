@extends('layouts.customer')

@section('title', 'Masuk')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/auth-theme.css') }}">
@endpush

@section('content')

    <div class="auth-shell">

        <div class="auth-brand-panel">
            <div class="auth-brand-panel-content">
                <div class="auth-brand-logo">
                    <span class="app-brand-icon"><i class="bi bi-lightning-charge-fill"></i></span>
                    TopUp<span class="app-brand-accent">Kilat</span>
                </div>

                <h2 class="auth-brand-title">Balik lagi buat top up? Masuk dulu, yuk.</h2>
                <p class="auth-brand-subtitle">Kelola riwayat transaksi dan pesan ulang produk favoritmu cuma dengan sekali klik.</p>

                <ul class="auth-trust-list">
                    <li>
                        <span class="auth-trust-icon"><i class="bi bi-clock-history"></i></span>
                        <div><strong>Riwayat Tersimpan</strong><span>Semua transaksi kamu tercatat rapi di satu tempat.</span></div>
                    </li>
                    <li>
                        <span class="auth-trust-icon"><i class="bi bi-arrow-repeat"></i></span>
                        <div><strong>Pesan Lagi Sekali Klik</strong><span>Top up produk yang sama tanpa isi ulang form dari awal.</span></div>
                    </li>
                    <li>
                        <span class="auth-trust-icon"><i class="bi bi-shield-check"></i></span>
                        <div><strong>Akun Terlindungi</strong><span>Login diamankan dengan verifikasi reCAPTCHA.</span></div>
                    </li>
                </ul>
            </div>
        </div>

        <div class="auth-form-panel">
            <div class="auth-form-card">

                <div class="auth-form-mobile-brand">
                    <span class="app-brand-icon"><i class="bi bi-lightning-charge-fill"></i></span>
                </div>

                <h1 class="auth-form-title">Masuk ke Akun Kamu</h1>
                <p class="auth-form-subtitle">Belum punya akun? <a href="{{ route('register') }}" class="fw-semibold">Daftar di sini</a></p>

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

                <div class="auth-divider">atau pakai email</div>

                <form action="{{ route('login.submit') }}" method="POST" novalidate id="login-form">
                    @csrf
                    <input type="hidden" name="recaptcha_token" id="recaptcha_token">

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

                    <button type="submit" class="btn app-btn-cta w-100" id="login-submit-btn">
                        <i class="bi bi-box-arrow-in-right me-1"></i> Masuk
                    </button>
                </form>

                <p class="auth-form-footer">
                    Login staff/admin pakai form yang sama - kamu akan diarahkan otomatis ke dashboard.
                </p>
            </div>
        </div>

    </div>

@endsection

@push('scripts')
<script>
    (function () {
        const form = document.getElementById('login-form');
        const submitBtn = document.getElementById('login-submit-btn');
        const tokenInput = document.getElementById('recaptcha_token');
        const siteKey = window.APP_CONFIG?.recaptchaSiteKey;

        form.addEventListener('submit', function (event) {
            if (!siteKey || typeof grecaptcha === 'undefined') {
                return;
            }

            event.preventDefault();
            submitBtn.disabled = true;

            grecaptcha.ready(function () {
                grecaptcha.execute(siteKey, { action: 'login' }).then(function (token) {
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
