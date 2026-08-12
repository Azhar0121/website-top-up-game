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
    const summaryProductQty = document.getElementById('summary-product-qty');
    const summaryProductPrice = document.getElementById('summary-product-price');

    const qtyInput = document.getElementById('qty-input');
    const qtyMinusBtn = document.getElementById('qty-minus');
    const qtyPlusBtn = document.getElementById('qty-plus');
    const qtyNote = document.getElementById('qty-note');

    const DEFAULT_MAX_QTY = 5; // dipakai kalau produk tidak punya batas stok eksplisit
    let quantity = 1;
    let maxQty = DEFAULT_MAX_QTY;

    /**
     * Ambil token reCAPTCHA v3 untuk action tertentu
     */
    function getRecaptchaToken(action) {
        const siteKey = window.APP_CONFIG?.recaptchaSiteKey;

        if (!siteKey || typeof grecaptcha === 'undefined') {
            return Promise.resolve(null);
        }

        return new Promise(function (resolve) {
            grecaptcha.ready(function () {
                grecaptcha.execute(siteKey, { action: action })
                    .then(resolve)
                    .catch(function () { resolve(null); });
            });
        });
    }

    let selectedProduct = null;
    let allCategories = [];
    let isSubmitting = false;

    function formatRupiah(value) {
        return 'Rp' + Number(value).toLocaleString('id-ID');
    }

    /**
     * Refresh tampilan tombol +/- dan angka quantity sesuai batas maxQty saat ini.
     */
    function updateQtyUi() {
        qtyInput.value = quantity;
        qtyMinusBtn.disabled = quantity <= 1;
        qtyPlusBtn.disabled = quantity >= maxQty;
    }

    /**
     * Hitung ulang harga total (harga satuan x quantity) dan refresh ringkasan
     * order + teks tombol Bayar. Dipanggil setiap kali produk ATAU quantity berubah.
     */
    function updatePriceSummary() {
        if (!selectedProduct) return;

        const hasFlashSale = selectedProduct.flash_sale_price !== null && selectedProduct.flash_sale_price !== undefined;
        const unitPrice = hasFlashSale ? selectedProduct.flash_sale_price : selectedProduct.base_price;
        const total = unitPrice * quantity;

        summaryProductName.textContent = selectedProduct.name;
        summaryProductQty.textContent = quantity;
        summaryProductPrice.textContent = formatRupiah(total);
        orderSummary.classList.remove('d-none');

        submitBtn.disabled = false;
        submitBtn.textContent = `Bayar ${formatRupiah(total)}`;
    }

    qtyMinusBtn.addEventListener('click', function () {
        if (quantity > 1) {
            quantity -= 1;
            updateQtyUi();
            updatePriceSummary();
        }
    });

    qtyPlusBtn.addEventListener('click', function () {
        if (quantity < maxQty) {
            quantity += 1;
            updateQtyUi();
            updatePriceSummary();
        }
    });

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

        if (game.banner_image_url) {
            const logoHtml = game.logo_image_url
                ? `<img src="${escapeHtml(game.logo_image_url)}" alt="${escapeHtml(game.name)}">`
                : initial;

            gameHeader.innerHTML = `
                <div class="game-hero-banner">
                    <img src="${escapeHtml(game.banner_image_url)}" alt="${escapeHtml(game.name)}" loading="lazy">
                    <div class="game-hero-banner-info">
                        <div class="game-hero-banner-logo d-flex align-items-center justify-content-center">${logoHtml}</div>
                        <div>
                            <h1 class="game-hero-banner-title">${escapeHtml(game.name)}</h1>
                            <p class="game-hero-banner-sub">Top up otomatis &middot; Proses 1-3 menit</p>
                        </div>
                    </div>
                </div>`;
        } else {
            gameHeader.innerHTML = `
                <div class="d-flex align-items-center gap-3">
                    <div class="game-header-icon">${initial}</div>
                    <div>
                        <h1 class="game-header-title mb-1">${escapeHtml(game.name)}</h1>
                        <p class="game-header-sub mb-0">Top up otomatis &middot; Proses 1-3 menit</p>
                    </div>
                </div>`;
        }

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

        productGrid.innerHTML = products.map((product) => {
            const hasFlashSale = product.flash_sale_price !== null && product.flash_sale_price !== undefined;
            const effectivePrice = hasFlashSale ? product.flash_sale_price : product.base_price;

            return `
            <div class="col-6 col-md-4">
                <button type="button" class="product-card ${hasFlashSale ? 'has-flash-sale' : ''}" data-product-id="${product.id}" data-product='${JSON.stringify(product)}'>
                    ${hasFlashSale ? '<span class="product-card-flash-badge"><i class="bi bi-lightning-charge-fill"></i> Flash Sale</span>' : ''}
                    <div class="product-card-name">${escapeHtml(product.name)}</div>
                    ${hasFlashSale ? `<div class="product-card-price-old">${formatRupiah(product.base_price)}</div>` : ''}
                    <div class="product-card-price">${formatRupiah(effectivePrice)}</div>
                </button>
            </div>
        `;
        }).join('');

        productGrid.querySelectorAll('.product-card').forEach((card) => {
            card.addEventListener('click', function () {
                productGrid.querySelectorAll('.product-card').forEach((c) => c.classList.remove('selected'));
                card.classList.add('selected');

                selectedProduct = JSON.parse(card.dataset.product);

                // Reset ke 1 tiap ganti produk - batas stok beda-beda per produk.
                quantity = 1;
                maxQty = (selectedProduct.stock !== null && selectedProduct.stock !== undefined)
                    ? Math.max(1, Math.min(selectedProduct.stock, 20)) // dibatasi 20 juga biar tidak kebablasan meski stok gede
                    : DEFAULT_MAX_QTY;

                qtyNote.textContent = (selectedProduct.stock !== null && selectedProduct.stock !== undefined)
                    ? `Stok tersedia: ${selectedProduct.stock}. Maks. ${maxQty} per transaksi.`
                    : `Maks. ${maxQty} per transaksi.`;

                updateQtyUi();
                updatePriceSummary();
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
            prefillLoggedInUser();
            applyRepeatOrder();
        } catch (err) {
            console.error(err);
            gameHeader.innerHTML = `<div class="catalog-state"><div class="catalog-state-emoji"><i class="bi bi-exclamation-triangle-fill"></i></div><p class="fw-bold mb-0">Gagal memuat data game</p></div>`;
        }
    }

    function prefillLoggedInUser() {
        if (window.APP_CONFIG.user && window.APP_CONFIG.user.email) {
            document.getElementById('customer_email').value = window.APP_CONFIG.user.email;
        }
    }

    function applyRepeatOrder() {
        const params = new URLSearchParams(window.location.search);
        const repeatProductId = params.get('repeat_product_id');
        if (!repeatProductId) return;

        const targetGameIdInput = document.getElementById('target_game_id');
        const targetServerIdInput = document.getElementById('target_server_id');
        if (params.get('target_game_id')) targetGameIdInput.value = params.get('target_game_id');
        if (params.get('target_server_id')) targetServerIdInput.value = params.get('target_server_id');

        const ownerCategory = allCategories.find((cat) =>
            (cat.products || []).some((p) => String(p.id) === String(repeatProductId))
        );
        if (!ownerCategory) return;

        const tabBtn = categoryTabs.querySelector(`[data-category-id="${ownerCategory.id}"]`);
        if (tabBtn) {
            tabBtn.click();
        } else {
            renderProducts(ownerCategory.products);
        }

        requestAnimationFrame(() => {
            const card = productGrid.querySelector(`[data-product-id="${repeatProductId}"]`);
            if (card) card.click();
        });

        document.getElementById('checkout-form').scrollIntoView({ behavior: 'smooth', block: 'start' });
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

        const recaptchaToken = await getRecaptchaToken('checkout');

        const payload = {
            product_id: selectedProduct.id,
            quantity: quantity,
            target_game_id: targetGameId,
            target_server_id: document.getElementById('target_server_id').value.trim() || null,
            customer_email: document.getElementById('customer_email').value.trim() || null,
            customer_whatsapp: document.getElementById('customer_whatsapp').value.trim() || null,
            voucher_code: document.getElementById('voucher_code').value.trim() || null,
            recaptcha_token: recaptchaToken,
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
        if (selectedProduct) {
            updatePriceSummary();
        } else {
            submitBtn.textContent = 'Pilih nominal dulu';
        }
    }

    qtyInput.value = quantity;
    form.addEventListener('submit', handleSubmit);

    loadGameDetail();
})();
