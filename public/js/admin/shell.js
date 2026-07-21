(function () {
    'use strict';

    const sidebar = document.getElementById('adminSidebar');
    const backdrop = document.getElementById('adminSidebarBackdrop');
    const toggleBtn = document.getElementById('adminSidebarToggle');

    function openSidebar() {
        sidebar.classList.add('is-open');
        backdrop.classList.add('is-open');
    }

    function closeSidebar() {
        sidebar.classList.remove('is-open');
        backdrop.classList.remove('is-open');
    }

    if (toggleBtn) {
        toggleBtn.addEventListener('click', function () {
            sidebar.classList.contains('is-open') ? closeSidebar() : openSidebar();
        });
    }

    if (backdrop) {
        backdrop.addEventListener('click', closeSidebar);
    }

    // Form delete dikasih atribut data-confirm-delete="Nama Item" di Blade,
    // supaya satu script ini bisa dipakai ulang untuk delete game/kategori/produk/dst.
    document.querySelectorAll('form[data-confirm-delete]').forEach(function (form) {
        form.addEventListener('submit', function (event) {
            const label = form.getAttribute('data-confirm-delete');
            const confirmed = window.confirm(`Yakin ingin menghapus "${label}"? Tindakan ini tidak bisa dibatalkan.`);

            if (!confirmed) {
                event.preventDefault();
            }
        });
    });

    // Auto-dismiss flash alert setelah beberapa detik supaya tidak menumpuk di layar.
    document.querySelectorAll('.admin-flash-alert').forEach(function (alertEl) {
        setTimeout(function () {
            alertEl.classList.remove('show');
        }, 5000);
    });
})();
