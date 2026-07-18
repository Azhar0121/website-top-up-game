@php
    // Helper kecil biar readable di Blade: kasih class "active" kalau nama route
    // saat ini diawali dengan salah satu prefix yang dikasih.
    $isActive = fn (string $prefix) => request()->routeIs($prefix) ? 'active' : '';
@endphp

<aside class="admin-sidebar" id="adminSidebar">
    <div class="admin-sidebar-brand">
        <i class="bi bi-lightning-charge-fill"></i>
        TopUp Kilat <span class="badge-enterprise">ADMIN</span>
    </div>

    <nav class="pb-4">
        <div class="admin-nav-section">Utama</div>
        <a href="{{ route('admin.dashboard') }}" class="admin-nav-link {{ $isActive('admin.dashboard') }}">
            <i class="bi bi-grid-1x2-fill"></i> Dashboard
        </a>

        <div class="admin-nav-section">Orders</div>
        <a href="{{ route('admin.orders.index') }}" class="admin-nav-link {{ request()->routeIs('admin.orders.*') && ! request('status') ? 'active' : '' }}">
            <i class="bi bi-receipt"></i> Transactions
        </a>
        <a href="{{ route('admin.orders.index', ['status' => 'failed']) }}" class="admin-nav-link {{ request()->routeIs('admin.orders.*') && request('status') === 'failed' ? 'active' : '' }}">
            <i class="bi bi-arrow-repeat"></i> Retry Queue
        </a>

        <div class="admin-nav-section">Games &amp; Products</div>
        <a href="{{ route('admin.games.index') }}" class="admin-nav-link {{ $isActive('admin.games.*') }}">
            <i class="bi bi-controller"></i> Games
        </a>
        <a href="{{ route('admin.categories.index') }}" class="admin-nav-link {{ $isActive('admin.categories.*') }}">
            <i class="bi bi-tags-fill"></i> Categories
        </a>
        <a href="{{ route('admin.products.index') }}" class="admin-nav-link {{ $isActive('admin.products.*') }}">
            <i class="bi bi-box-seam-fill"></i> Products &amp; SKUs
        </a>
        <a href="#" class="admin-nav-link disabled" tabindex="-1">
            <i class="bi bi-lightning-fill"></i> Flash Sale &amp; Promo
            <span class="admin-nav-soon">Segera</span>
        </a>

        <div class="admin-nav-section">Providers &amp; API</div>
        <a href="#" class="admin-nav-link disabled" tabindex="-1">
            <i class="bi bi-hdd-network-fill"></i> Provider List &amp; Priority
            <span class="admin-nav-soon">Segera</span>
        </a>
        <a href="#" class="admin-nav-link disabled" tabindex="-1">
            <i class="bi bi-journal-code"></i> API &amp; Webhook Logs
            <span class="admin-nav-soon">Segera</span>
        </a>

        <div class="admin-nav-section">Payments &amp; Finance</div>
        <a href="#" class="admin-nav-link disabled" tabindex="-1">
            <i class="bi bi-credit-card-fill"></i> Payment Gateway
            <span class="admin-nav-soon">Segera</span>
        </a>
        <a href="#" class="admin-nav-link disabled" tabindex="-1">
            <i class="bi bi-wallet-fill"></i> Wallet &amp; Withdrawal
            <span class="admin-nav-soon">Segera</span>
        </a>

        <div class="admin-nav-section">Customers &amp; Affiliates</div>
        <a href="#" class="admin-nav-link disabled" tabindex="-1">
            <i class="bi bi-people-fill"></i> Users &amp; Leveling
            <span class="admin-nav-soon">Segera</span>
        </a>

        <div class="admin-nav-section">Reports</div>
        <a href="#" class="admin-nav-link disabled" tabindex="-1">
            <i class="bi bi-bar-chart-fill"></i> Sales &amp; Revenue
            <span class="admin-nav-soon">Segera</span>
        </a>

        <div class="admin-nav-section">Content &amp; Marketing</div>
        <a href="#" class="admin-nav-link disabled" tabindex="-1">
            <i class="bi bi-megaphone-fill"></i> CMS &amp; Banner
            <span class="admin-nav-soon">Segera</span>
        </a>

        <div class="admin-nav-section">System &amp; Security</div>
        <a href="#" class="admin-nav-link disabled" tabindex="-1">
            <i class="bi bi-shield-lock-fill"></i> Roles &amp; Audit Log
            <span class="admin-nav-soon">Segera</span>
        </a>
    </nav>
</aside>