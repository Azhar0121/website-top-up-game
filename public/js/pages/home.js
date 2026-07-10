(function () {
    'use strict';

    const API_BASE = window.APP_CONFIG.apiBase;
    const grid = document.getElementById('game-grid');
    const heading = document.getElementById('catalog-heading');
    const searchForm = document.getElementById('hero-search-form');
    const searchInput = document.getElementById('hero-search-input');
    const filterChips = document.querySelectorAll('.filter-chip');

    let state = {
        search: '',
        filter: 'all',
    };

    let debounceTimer = null;

    function buildQuery() {
        const params = new URLSearchParams();
        if (state.search) params.set('search', state.search);
        if (state.filter === 'popular') params.set('popular', '1');
        if (state.filter === 'favorite') params.set('favorite', '1');
        return params.toString();
    }

    function updateHeading() {
        if (state.search) {
            heading.textContent = `Hasil pencarian "${state.search}"`;
        } else if (state.filter === 'popular') {
            heading.textContent = '🔥 Lagi Populer';
        } else if (state.filter === 'favorite') {
            heading.textContent = '⭐ Game Favorit';
        } else {
            heading.textContent = 'Semua Game';
        }
    }

    function renderSkeleton() {
        let html = '';
        for (let i = 0; i < 12; i++) {
            html += `
                <div class="col-6 col-md-4 col-lg-2">
                    <div class="skeleton-card">
                        <div class="skeleton-thumb"></div>
                        <div class="skeleton-line" style="width: 80%;"></div>
                        <div class="skeleton-line mb-3" style="width: 50%;"></div>
                    </div>
                </div>`;
        }
        grid.innerHTML = html;
    }

    function renderEmpty() {
        grid.innerHTML = `
            <div class="col-12">
                <div class="catalog-state">
                    <div class="catalog-state-emoji">🔍</div>
                    <p class="mb-1 fw-bold">Game tidak ditemukan</p>
                    <p class="mb-0 small">Coba kata kunci lain, atau lihat semua game yang tersedia.</p>
                </div>
            </div>`;
    }

    function renderError() {
        grid.innerHTML = `
            <div class="col-12">
                <div class="catalog-state">
                    <div class="catalog-state-emoji">⚠️</div>
                    <p class="mb-1 fw-bold">Gagal memuat daftar game</p>
                    <p class="mb-0 small">Periksa koneksi internet kamu, atau coba muat ulang halaman.</p>
                </div>
            </div>`;
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function renderGames(games) {
        if (!games.length) {
            renderEmpty();
            return;
        }

        const html = games.map((game, index) => {
            const badge = game.is_popular
                ? '<span class="game-card-badge">Populer</span>'
                : (game.is_favorite ? '<span class="game-card-badge">Favorit</span>' : '');

            const initial = game.name.charAt(0).toUpperCase();
            const fallbackClass = `fallback-${index % 3}`;

            const thumb = game.logo_image
                ? `<img src="${escapeHtml(game.logo_image)}" alt="${escapeHtml(game.name)}" loading="lazy">`
                : initial;

            return `
                <div class="col-6 col-md-4 col-lg-2">
                    <a href="/game/${encodeURIComponent(game.slug)}" class="game-card">
                        <div class="game-card-thumb ${game.logo_image ? '' : fallbackClass}">
                            ${badge}
                            ${thumb}
                        </div>
                        <div class="game-card-body">
                            <div class="game-card-title">${escapeHtml(game.name)}</div>
                            <div class="game-card-cta">Top up sekarang →</div>
                        </div>
                    </a>
                </div>`;
        }).join('');

        grid.innerHTML = html;
    }

    async function loadGames() {
        renderSkeleton();
        updateHeading();

        try {
            const query = buildQuery();
            const response = await fetch(`${API_BASE}/games${query ? '?' + query : ''}`, {
                headers: { Accept: 'application/json' },
            });

            if (!response.ok) throw new Error('Request gagal: ' + response.status);

            const result = await response.json();
            renderGames(result.data || []);
        } catch (err) {
            console.error('Gagal memuat katalog game:', err);
            renderError();
        }
    }

    searchForm.addEventListener('submit', function (e) {
        e.preventDefault();
        state.search = searchInput.value.trim();
        loadGames();
    });

    searchInput.addEventListener('input', function () {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(function () {
            state.search = searchInput.value.trim();
            loadGames();
        }, 400);
    });

    filterChips.forEach((chip) => {
        chip.addEventListener('click', function () {
            filterChips.forEach((c) => c.classList.remove('active'));
            chip.classList.add('active');
            state.filter = chip.dataset.filter;
            loadGames();
        });
    });

    loadGames();
})();
