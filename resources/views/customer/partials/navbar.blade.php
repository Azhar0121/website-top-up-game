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

                <li class="nav-item ms-lg-3">
                    <a class="btn app-btn-outline" href="{{ route('login') }}">
                        Masuk
                    </a>
                </li>
            </ul>

        </div>

    </div>
</nav>