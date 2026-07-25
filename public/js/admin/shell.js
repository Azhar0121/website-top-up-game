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

    if (sidebar) {
        const SCROLL_KEY = 'adminSidebarScrollTop';
        const savedScroll = sessionStorage.getItem(SCROLL_KEY);

        if (savedScroll !== null) {
            sidebar.scrollTop = parseInt(savedScroll, 10);
        } else {
            const activeLink = sidebar.querySelector('.admin-nav-link.active');
            if (activeLink) {
                activeLink.scrollIntoView({ block: 'center' });
            }
        }

        sidebar.addEventListener('scroll', function () {
            sessionStorage.setItem(SCROLL_KEY, sidebar.scrollTop);
        });

        sidebar.querySelectorAll('a.admin-nav-link').forEach(function (link) {
            link.addEventListener('click', function () {
                sessionStorage.setItem(SCROLL_KEY, sidebar.scrollTop);
            });
        });
    }

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
