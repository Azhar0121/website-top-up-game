@extends('layouts.customer')

@section('title', 'Beranda')
@section('meta_description', 'Top up Diamond Mobile Legends, Roblox, Minecraft, dan game favoritmu lainnya. Proses otomatis 1-3 menit, aman, dan banyak pilihan pembayaran.')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/home-theme.css') }}">
@endpush

@section('content')

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

    <section class="curated-section" id="curated-popular-section">
        <div class="container">
            <div class="section-header-row">
                <div>
                    <div class="section-eyebrow-label"><i class="bi bi-fire"></i> Trending</div>
                    <h2 class="section-heading mb-0">Sedang Populer Minggu Ini</h2>
                </div>
            </div>
            <div id="curated-popular-scroll" class="curated-scroll"></div>
        </div>
    </section>

    <div class="container filter-chips">
        <div class="d-flex gap-2 flex-wrap justify-content-center">
            <button type="button" class="filter-chip active" data-filter="all">Semua Game</button>
            <button type="button" class="filter-chip" data-filter="popular"><i class="bi bi-fire"></i>  Populer</button>
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
