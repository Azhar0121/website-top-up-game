@extends('layouts.customer')

@section('title', 'Beranda')
@section('meta_description', 'Top up Diamond Mobile Legends, Roblox, Minecraft, dan game favoritmu lainnya. Proses otomatis 1-3 menit, aman, dan banyak pilihan pembayaran.')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/home-theme.css') }}">
@endpush

@section('content')

    <section class="hero">
        <div class="hero-glow-orb orb-pink"></div>
        <div class="hero-glow-orb orb-mint"></div>

        <div class="container position-relative">
            <div class="row justify-content-center text-center">
                <div class="col-lg-9">
                    <h1 class="hero-title mb-3">
                        Top Up Diamond & Item Game,
                        <span class="hero-underline">
                            Kelar dalam Hitungan Menit
                            <svg viewBox="0 0 300 14" preserveAspectRatio="none"><path d="M2 8c40-10 80 10 120 0s80-10 120 0 40 4 56 -2" stroke="#FFC93C" stroke-width="4" fill="none" stroke-linecap="round"/></svg>
                        </span>
                    </h1>
                    <p class="hero-subtitle mx-auto mb-4">
                        Ratusan game dan voucher siap diisi otomatis. Cari game favoritmu di bawah,
                        pilih nominal, bayar — beres.
                    </p>

                    <form id="hero-search-form" class="hero-search d-flex align-items-center mx-auto" role="search">
                        <input type="text" id="hero-search-input" placeholder="Cari nama game, misal: Mobile Legends..." aria-label="Cari game">
                        <button type="submit"><i class="bi bi-search"></i> Cari</button>
                    </form>

                    <div class="hero-trust-strip">
                        <div class="hero-trust-item"><i class="bi bi-lightning-charge-fill"></i> Proses Otomatis</div>
                        <div class="hero-trust-item"><i class="bi bi-shield-lock-fill"></i> Pembayaran Aman</div>
                        <div class="hero-trust-item"><i class="bi bi-controller"></i> Ratusan Game Populer</div>
                        <div class="hero-trust-item"><i class="bi bi-headset"></i> CS Siap Bantu</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @if ($banners->isNotEmpty())
        <div class="container">
            <div id="bannerCarousel" class="carousel slide banner-carousel" data-bs-ride="carousel" data-bs-interval="4500">
                <div class="carousel-indicators">
                    @foreach ($banners as $i => $banner)
                        <button type="button" data-bs-target="#bannerCarousel" data-bs-slide-to="{{ $i }}" class="{{ $i === 0 ? 'active' : '' }}" aria-current="{{ $i === 0 ? 'true' : 'false' }}" aria-label="Slide {{ $i + 1 }}"></button>
                    @endforeach
                </div>
                <div class="carousel-inner">
                    @foreach ($banners as $i => $banner)
                        <div class="carousel-item {{ $i === 0 ? 'active' : '' }}">
                            @if ($banner->link_url)
                                <a href="{{ $banner->link_url }}">
                                    <img src="{{ $banner->image_url }}" class="banner-slide-img" alt="{{ $banner->title }}">
                                </a>
                            @else
                                <img src="{{ $banner->image_url }}" class="banner-slide-img" alt="{{ $banner->title }}">
                            @endif
                        </div>
                    @endforeach
                </div>
                @if ($banners->count() > 1)
                    <button class="carousel-control-prev" type="button" data-bs-target="#bannerCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#bannerCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    </button>
                @endif
            </div>
        </div>
    @endif

    {{-- Kenapa Pilih TopUp Kilat - trust section ala landing page startup --}}
    <section class="features-section">
        <div class="container">
            <div class="row g-3">
                <div class="col-6 col-lg-3">
                    <div class="feature-card">
                        <span class="feature-card-icon"><i class="bi bi-lightning-charge-fill"></i></span>
                        <div>
                            <div class="feature-card-title">Proses Otomatis</div>
                            <p class="feature-card-text">Rata-rata selesai 1-3 menit setelah pembayaran dikonfirmasi.</p>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="feature-card">
                        <span class="feature-card-icon"><i class="bi bi-shield-check"></i></span>
                        <div>
                            <div class="feature-card-title">Transaksi Aman</div>
                            <p class="feature-card-text">Pembayaran diproses lewat payment gateway resmi dan terenkripsi.</p>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="feature-card">
                        <span class="feature-card-icon"><i class="bi bi-tags-fill"></i></span>
                        <div>
                            <div class="feature-card-title">Harga Bersaing</div>
                            <p class="feature-card-text">Ratusan pilihan nominal dengan harga transparan, tanpa biaya siluman.</p>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="feature-card">
                        <span class="feature-card-icon"><i class="bi bi-headset"></i></span>
                        <div>
                            <div class="feature-card-title">CS Siap Bantu</div>
                            <p class="feature-card-text">Ada kendala? Tim support siap bantu lacak transaksi kamu.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Curated: Lagi Populer minggu ini (scroll horizontal, ala Itemku) --}}
    <section class="curated-section" id="curated-popular-section">
        <div class="container">
            <div class="section-header-row">
                <div>
                    <div class="section-eyebrow-label"><i class="bi bi-fire"></i> Trending</div>
                    <h2 class="section-heading mb-0">Lagi Populer Minggu Ini</h2>
                </div>
            </div>
            <div id="curated-popular-scroll" class="curated-scroll"></div>
        </div>
    </section>

    <div class="container filter-chips">
        <div class="d-flex gap-2 flex-wrap justify-content-center">
            <button type="button" class="filter-chip active" data-filter="all">Semua Game</button>
            <button type="button" class="filter-chip" data-filter="popular"><i class="bi bi-fire"></i> Lagi Populer</button>
            <button type="button" class="filter-chip" data-filter="favorite"><i class="bi bi-star-fill"></i> Favorit</button>
        </div>
    </div>

    <section class="catalog-section">
        <div class="container">
            <h2 class="section-heading" id="catalog-heading">Semua Game</h2>

            <div id="game-grid" class="row g-3" aria-live="polite">
                @for ($i = 0; $i < 12; $i++)
                    <div class="col-6 col-md-4 col-lg-2">
                        <div class="skeleton-card">
                            <div class="skeleton-thumb"></div>
                            <div class="skeleton-line" style="width: 80%;"></div>
                            <div class="skeleton-line mb-3" style="width: 50%;"></div>
                        </div>
                    </div>
                @endfor
            </div>
        </div>
    </section>

@endsection

@push('scripts')
    <script src="{{ asset('js/pages/home.js') }}"></script>
@endpush
