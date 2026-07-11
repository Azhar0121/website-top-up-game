(function () {
    'use strict';

    const API_BASE = window.APP_CONFIG.apiBase;
    const app = document.getElementById('game-detail-app');
    const slug = app.dataset.slug;

    const gameHeader = document.getElementById('game-header');
    const tutorialBox = document.getElementById('tutorial-box');
    const tutorialText = document.getElementById('tutorial-text');
    const categoryTabs = document.getElementById('category-tabs');
    const productGrid = document.getElementById('product-grid');
    const serverIdWrapper = document.getElementById('server-id-wrapper');

    const form = document.getElementById('checkout-form');
    const submitBtn = document.getElementById('submit-btn');
    const formAlert = document.getElementById('form-alert');
    const orderSummary = document.getElementById('order-summary');
    const summaryProductName = document.getElementById('summary-product-name');
    const summaryProductPrice = document.getElementById('summary-product-price');

    let selectedProduct = null;
    let allCategories = [];
    let isSubmitting = false;

    function formatRupiah(value) {
        return 'Rp' + Number(value).toLocaleString('id-ID');
    }

    function showAlert(message, type = 'danger') {
        formAlert.className = `alert alert-${type}`;
        formAlert.textContent = message;
        formAlert.classList.remove('d-none');
    }

    function hideAlert() {
        formAlert.classList.add('d-none');
    }

    function renderHeader(game) {
        const initial = game.name.charAt(0).toUpperCase();
        gameHeader.innerHTML = `
            <div class="d-flex align-items-center gap-3">
                <div class="game-header-icon">${initial}</div>
                <div>
                    <h1 class="game-header-title mb-1">${escapeHtml(game.name)}</h1>
                    <p class="game-header-sub mb-0">Top up otomatis &middot; Proses 1-3 menit</p>
                </div>
            </div>`;

        if (game.tutorial_text) {
            tutorialText.textContent = game.tutorial_text;
            tutorialBox.classList.remove('d-none');
        }
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function renderCategoryTabs(categories) {
        if (categories.length <= 1) {
            categoryTabs.innerHTML = '';
            return;
        }

        categoryTabs.innerHTML = categories.map((cat, i) => `
            <button type="button" class="filter-chip ${i === 0 ? 'active' : ''}" data-category-id="${cat.id}">
                ${escapeHtml(cat.name)}
            </button>
        `).join('');

        categoryTabs.querySelectorAll('.filter-chip').forEach((btn) => {
            btn.addEventListener('click', function () {
                categoryTabs.querySelectorAll('.filter-chip').forEach((b) => b.classList.remove('active'));
                btn.classList.add('active');
                const category = categories.find((c) => String(c.id) === btn.dataset.categoryId);
                renderProducts(category ? category.products : []);
            });
        });
    }

    function renderProducts(products) {
        if (!products || !products.length) {
            productGrid.innerHTML = `
                <div class="col-12">
                    <div class="catalog-state py-3">
                        <p class="mb-0 small">Belum ada produk tersedia untuk kategori ini.</p>
                    </div>
                </div>`;
            return;
        }

        productGrid.innerHTML = products.map((product) => `
            <div class="col-6 col-md-4">
                <button type="button" class="product-card" data-product='${JSON.stringify(product)}'>
                    <div class="product-card-name">${escapeHtml(product.name)}</div>
                    <div class="product-card-price">${formatRupiah(product.base_price)}</div>
                </button>
            </div>
        `).join('');

        productGrid.querySelectorAll('.product-card').forEach((card) => {
            card.addEventListener('click', function () {
                productGrid.querySelectorAll('.product-card').forEach((c) => c.classList.remove('selected'));
                card.classList.add('selected');

                selectedProduct = JSON.parse(card.dataset.product);
                summaryProductName.textContent = selectedProduct.name;
                summaryProductPrice.textContent = formatRupiah(selectedProduct.base_price);
                orderSummary.classList.remove('d-none');

                submitBtn.disabled = false;
                submitBtn.textContent = `Bayar ${formatRupiah(selectedProduct.base_price)}`;
            });
        });
    }

    async function loadGameDetail() {
        try {
            const response = await fetch(`${API_BASE}/games/${encodeURIComponent(slug)}`, {
                headers: { Accept: 'application/json' },
            });

            if (!response.ok) throw new Error('Game tidak ditemukan');

            const result = await response.json();
            const game = result.data;

            renderHeader(game);
            allCategories = game.categories || [];
            renderCategoryTabs(allCategories);
            renderProducts(allCategories.length ? allCategories[0].products : []);
        } catch (err) {
            console.error(err);
            gameHeader.innerHTML = `<div class="catalog-state"><div class="catalog-state-emoji">⚠️</div><p class="fw-bold mb-0">Gagal memuat data game</p></div>`;
        }
    }

    async function handleSubmit(e) {
        e.preventDefault();
        hideAlert();

        if (!selectedProduct) {
            showAlert('Pilih nominal top up dulu.');
            return;
        }

        const targetGameId = document.getElementById('target_game_id').value.trim();
        if (!targetGameId) {
            showAlert('ID Game wajib diisi.');
            return;
        }

        if (isSubmitting) return;
        isSubmitting = true;
        submitBtn.disabled = true;
        submitBtn.textContent = 'Memproses...';

        const payload = {
            product_id: selectedProduct.id,
            target_game_id: targetGameId,
            target_server_id: document.getElementById('target_server_id').value.trim() || null,
            customer_email: document.getElementById('customer_email').value.trim() || null,
            customer_whatsapp: document.getElementById('customer_whatsapp').value.trim() || null,
            voucher_code: document.getElementById('voucher_code').value.trim() || null,
        };

        try {
            const checkoutRes = await fetch(`${API_BASE}/checkout`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
                body: JSON.stringify(payload),
            });
            const checkoutResult = await checkoutRes.json();

            if (!checkoutRes.ok || !checkoutResult.success) {
                const message = checkoutResult.message
                    || (checkoutResult.errors ? Object.values(checkoutResult.errors)[0][0] : 'Gagal membuat order.');
                throw new Error(message);
            }

            const invoiceNumber = checkoutResult.data.invoice_number;

            const initiateRes = await fetch(`${API_BASE}/payment/initiate`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
                body: JSON.stringify({ invoice_number: invoiceNumber, gateway_code: 'midtrans' }),
            });
            const initiateResult = await initiateRes.json();

            if (!initiateRes.ok || !initiateResult.success || !initiateResult.data.snap_token) {
                throw new Error('Order berhasil dibuat, tapi gagal membuka pembayaran. Cek transaksi kamu di /order/' + invoiceNumber);
            }

            window.snap.pay(initiateResult.data.snap_token, {
                onSuccess: function () {
                    window.location.href = '/order/' + invoiceNumber;
                },
                onPending: function () {
                    window.location.href = '/order/' + invoiceNumber;
                },
                onError: function () {
                    showAlert('Pembayaran gagal. Silakan coba lagi atau cek status di /order/' + invoiceNumber);
                    resetSubmitButton();
                },
                onClose: function () {
                    showAlert('Kamu menutup jendela pembayaran. Order tetap tersimpan, cek statusnya di /order/' + invoiceNumber, 'warning');
                    resetSubmitButton();
                },
            });
        } catch (err) {
            showAlert(err.message || 'Terjadi kesalahan, silakan coba lagi.');
            resetSubmitButton();
        }
    }

    function resetSubmitButton() {
        isSubmitting = false;
        submitBtn.disabled = false;
        submitBtn.textContent = selectedProduct ? `Bayar ${formatRupiah(selectedProduct.base_price)}` : 'Pilih nominal dulu';
    }

    form.addEventListener('submit', handleSubmit);

    loadGameDetail();
})();
