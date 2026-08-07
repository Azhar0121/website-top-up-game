<nav class="navbar navbar-expand-lg app-navbar sticky-top">
    <div class="container">

        <a class="navbar-brand app-brand" href="{{ url('/') }}">
            TopUp<span class="app-brand-accent">Kilat</span>
        </a>

        <a href="{{ url('/cek-transaksi') }}" class="app-nav-track-btn d-lg-none ms-auto me-2">
            <i class="bi bi-receipt"></i>
        </a>

        <button class="navbar-toggler"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#mainNav"
                aria-controls="mainNav"
                aria-expanded="false"
                aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNav">

            <ul class="navbar-nav ms-auto align-items-lg-center">
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('/') ? 'active' : '' }}" href="{{ url('/') }}">
                        <i class="bi bi-controller"></i> Semua Game
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('faq') ? 'active' : '' }}" href="{{ route('faq') }}">
                        <i class="bi bi-question-circle"></i> FAQ
                    </a>
                </li>

                <li class="nav-item d-none d-lg-block ms-lg-1">
                    <a href="{{ url('/cek-transaksi') }}" class="app-nav-track-btn">
                        <i class="bi bi-receipt"></i> Cek Transaksi
                    </a>
                </li>

                @auth
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('account.*') ? 'active' : '' }}" href="{{ route('account.index') }}">
                            <i class="bi bi-person-circle"></i> Akun Saya
                        </a>
                    </li>
                    <li class="nav-item ms-lg-2">
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn app-btn-outline">
                                <i class="bi bi-box-arrow-right"></i> Keluar
                            </button>
                        </form>
                    </li>
                @else
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('register') }}">
                            <i class="bi bi-person-plus"></i> Daftar
                        </a>
                    </li>
                    <li class="nav-item ms-lg-2">
                        <a class="btn app-btn-outline" href="{{ route('login') }}">
                            <i class="bi bi-box-arrow-in-right"></i> Masuk
                        </a>
                    </li>
                @endauth
            </ul>

        </div>

    </div>
</nav>
