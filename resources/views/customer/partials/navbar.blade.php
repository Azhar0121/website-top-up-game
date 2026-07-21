<nav class="navbar navbar-expand-lg app-navbar sticky-top">
    <div class="container">

        <a class="navbar-brand app-brand" href="{{ url('/') }}">
            <span class="app-brand-mark">⚡</span>
            TopUp<span class="app-brand-accent">Kilat</span>
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
                    <a class="nav-link" href="{{ url('/') }}">
                        Semua Game
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/cek-transaksi') }}">
                        Cek Transaksi
                    </a>
                </li>

                @auth
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('account.index') }}">
                            Akun Saya
                        </a>
                    </li>
                    <li class="nav-item ms-lg-3">
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn app-btn-outline">Keluar</button>
                        </form>
                    </li>
                @else
                    <li class="nav-item ms-lg-2">
                        <a class="nav-link" href="{{ route('register') }}">
                            Daftar
                        </a>
                    </li>
                    <li class="nav-item ms-lg-2">
                        <a class="btn app-btn-outline" href="{{ route('login') }}">
                            Masuk
                        </a>
                    </li>
                @endauth
            </ul>

        </div>

    </div>
</nav>