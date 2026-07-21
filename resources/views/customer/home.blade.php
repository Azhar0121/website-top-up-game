@extends('layouts.customer')

@section('title', 'Beranda')
@section('meta_description', 'Top up Diamond Mobile Legends, Roblox, Minecraft, dan game favoritmu lainnya. Proses otomatis 1-3 menit, aman, dan banyak pilihan pembayaran.')

@section('content')

    <section class="hero">
        <div class="hero-glow-orb orb-pink"></div>
        <div class="hero-glow-orb orb-mint"></div>
        <svg class="hero-doodle d1" viewBox="0 0 24 24" fill="none"><path d="M12 2l2.4 7.2H22l-6 4.6 2.3 7.2L12 16.4l-6.3 4.6 2.3-7.2-6-4.6h7.6L12 2z" fill="#FFC93C"/></svg>
        <svg class="hero-doodle d2" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="9" stroke="#FF5D8F" stroke-width="2.5" stroke-dasharray="4 4"/></svg>
        <svg class="hero-doodle d3" viewBox="0 0 24 24" fill="none"><path d="M4 12c2-6 14-6 16 0" stroke="#34E4B8" stroke-width="2.5" stroke-linecap="round"/></svg>
        <svg class="hero-doodle d4" viewBox="0 0 24 24" fill="none"><path d="M12 2l2.4 7.2H22l-6 4.6 2.3 7.2L12 16.4l-6.3 4.6 2.3-7.2-6-4.6h7.6L12 2z" fill="#FF5D8F"/></svg>

        <div class="container position-relative">
            <div class="row justify-content-center text-center">
                <div class="col-lg-9">
                    <span class="hero-eyebrow">⚡ Proses Otomatis 1-3 Menit</span>
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
                        <button type="submit">Cari</button>
                    </form>

                    <div class="hero-trust-strip">
                        <div class="hero-trust-item"><i class="bi bi-lightning-charge-fill"></i> Proses Otomatis</div>
                        <div class="hero-trust-item"><i class="bi bi-shield-lock-fill"></i> Pembayaran Aman</div>
                        <div class="hero-trust-item"><i class="bi bi-controller"></i> 100+ Game Populer</div>
                        <div class="hero-trust-item"><i class="bi bi-headset"></i> CS Siap Bantu</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="container filter-chips">
        <div class="d-flex gap-2 flex-wrap justify-content-center">
            <button type="button" class="filter-chip active" data-filter="all">Semua Game</button>
            <button type="button" class="filter-chip" data-filter="popular">🔥 Lagi Populer</button>
            <button type="button" class="filter-chip" data-filter="favorite">⭐ Favorit</button>
        </div>
    </div>

    <section class="catalog-section">
        <div class="container">
            <div class="section-eyebrow">🎮 Katalog Game</div>
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
