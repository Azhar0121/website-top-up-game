/**
 * Preview gambar sebelum upload - dipakai di form Game (banner_image, logo_image).
 * Cari semua <input type="file"> yang punya atribut data-preview-target, lalu
 * tampilkan gambar yang dipilih ke elemen <img> dengan id sesuai atribut itu.
 */
(function () {
    'use strict';

    document.querySelectorAll('input[type="file"][data-preview-target]').forEach(function (input) {
        input.addEventListener('change', function () {
            const targetId = input.getAttribute('data-preview-target');
            const previewImg = document.getElementById(targetId);
            const previewWrapper = previewImg ? previewImg.closest('.admin-image-preview') : null;

            if (!input.files || !input.files[0] || !previewImg) {
                return;
            }

            const reader = new FileReader();
            reader.onload = function (e) {
                previewImg.src = e.target.result;
                if (previewWrapper) {
                    previewWrapper.classList.remove('d-none');
                }
            };
            reader.readAsDataURL(input.files[0]);
        });
    });
})();
