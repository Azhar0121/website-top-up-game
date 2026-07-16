/**
 * Halaman Form Produk (create & edit) - admin/products/form.blade.php
 * 1. Filter opsi kategori supaya cuma menampilkan kategori milik game yang dipilih.
 * 2. Ganti label satuan margin (Rp / %) sesuai margin_type yang dipilih.
 */
(function () {
    'use strict';

    const gameSelect = document.getElementById('game_id');
    const categorySelect = document.getElementById('category_id');
    const marginTypeSelect = document.getElementById('margin_type');
    const marginUnitLabel = document.getElementById('margin-unit-label');

    if (categorySelect) {
        // Simpan semua <option> asli sekali di awal, supaya bisa di-render ulang
        // tiap kali game berubah tanpa perlu request AJAX ke server.
        const allOptions = Array.from(categorySelect.options).filter(function (opt) {
            return opt.value !== '';
        });
        const selectedCategoryId = categorySelect.dataset.selected || '';

        function renderCategoryOptions() {
            const gameId = gameSelect ? gameSelect.value : '';
            categorySelect.innerHTML = '<option value="">-- Pilih Kategori --</option>';

            allOptions
                .filter(function (opt) { return opt.dataset.gameId === gameId; })
                .forEach(function (opt) {
                    const cloned = opt.cloneNode(true);
                    if (cloned.value === selectedCategoryId) {
                        cloned.selected = true;
                    }
                    categorySelect.appendChild(cloned);
                });
        }

        if (gameSelect) {
            gameSelect.addEventListener('change', renderCategoryOptions);
            renderCategoryOptions();
        }
    }

    function updateMarginUnitLabel() {
        if (!marginTypeSelect || !marginUnitLabel) return;
        marginUnitLabel.textContent = marginTypeSelect.value === 'fixed' ? 'Rp' : '%';
    }

    if (marginTypeSelect) {
        marginTypeSelect.addEventListener('change', updateMarginUnitLabel);
        updateMarginUnitLabel();
    }
})();
