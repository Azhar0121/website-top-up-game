<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Admin - TopUp Kilat</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin-custom.css') }}">
</head>
<body class="admin-body">

    <div class="admin-login-page">
        <div class="admin-login-card">
            <div class="text-center mb-4">
                <div class="admin-login-brand">
                    <i class="bi bi-lightning-charge-fill text-warning"></i>
                    TopUp Kilat <span class="badge-enterprise">ADMIN</span>
                </div>
                <p class="text-muted small mb-0">Masuk untuk mengelola katalog, order, dan operasional.</p>
            </div>

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

            <form action="{{ route('admin.login.submit') }}" method="POST" novalidate>
                @csrf

                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold small">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}"
                           class="form-control @error('email') is-invalid @enderror"
                           placeholder="admin@topupgame.test" required autofocus>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label fw-semibold small">Password</label>
                    <input type="password" name="password" id="password"
                           class="form-control @error('password') is-invalid @enderror"
                           placeholder="••••••••" required>
                </div>

                <div class="form-check mb-3">
                    <input type="checkbox" name="remember" id="remember" class="form-check-input">
                    <label for="remember" class="form-check-label small">Ingat saya di perangkat ini</label>
                </div>

                <button type="submit" class="btn btn-admin-primary w-100 py-2">
                    <i class="bi bi-box-arrow-in-right me-1"></i> Masuk Dashboard
                </button>
            </form>

            <p class="text-center text-muted small mt-4 mb-0">
                Halaman ini khusus staff (Owner, Admin, Finance, CS, Marketing, Developer).
            </p>
        </div>
    </div>

</body>
</html>
