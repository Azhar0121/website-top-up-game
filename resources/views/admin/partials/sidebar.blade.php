@php
    $isActive = fn (string $prefix) => request()->routeIs($prefix) ? 'active' : '';
@endphp

<aside class="admin-sidebar" id="adminSidebar">
    <div class="admin-sidebar-brand">
        <i class="bi bi-lightning-charge-fill"></i>
        TopUp Kilat <span class="badge-enterprise">ADMIN</span>
    </div>

    <nav class="pb-4">
        @can('dashboard.view')
            <div class="admin-nav-section">Utama</div>
            <a href="{{ route('admin.dashboard') }}" class="admin-nav-link {{ $isActive('admin.dashboard') }}">
                <i class="bi bi-grid-1x2-fill"></i> Dashboard
            </a>
        @endcan

        @can('orders.view')
            <div class="admin-nav-section">Orders</div>
            <a href="{{ route('admin.orders.index') }}" class="admin-nav-link {{ request()->routeIs('admin.orders.*') && ! request('status') ? 'active' : '' }}">
                <i class="bi bi-receipt"></i> Transactions
            </a>
            <a href="{{ route('admin.orders.index', ['status' => 'failed']) }}" class="admin-nav-link {{ request()->routeIs('admin.orders.*') && request('status') === 'failed' ? 'active' : '' }}">
                <i class="bi bi-arrow-repeat"></i> Retry Queue
            </a>
        @endcan

        @canany(['games.manage', 'vouchers.manage', 'flash-sales.manage'])
            <div class="admin-nav-section">Games &amp; Products</div>
            @can('games.manage')
                <a href="{{ route('admin.games.index') }}" class="admin-nav-link {{ $isActive('admin.games.*') }}">
                    <i class="bi bi-controller"></i> Games
                </a>
                <a href="{{ route('admin.categories.index') }}" class="admin-nav-link {{ $isActive('admin.categories.*') }}">
                    <i class="bi bi-tags-fill"></i> Categories
                </a>
                <a href="{{ route('admin.products.index') }}" class="admin-nav-link {{ $isActive('admin.products.*') }}">
                    <i class="bi bi-box-seam-fill"></i> Products &amp; SKUs
                </a>
            @endcan
            @can('vouchers.manage')
                <a href="{{ route('admin.vouchers.index') }}" class="admin-nav-link {{ $isActive('admin.vouchers.*') }}">
                    <i class="bi bi-lightning-fill"></i> Vouchers &amp; Promo
                </a>
            @endcan
            @can('flash-sales.manage')
                <a href="{{ route('admin.flash-sales.index') }}" class="admin-nav-link {{ $isActive('admin.flash-sales.*') }}">
                    <i class="bi bi-stopwatch-fill"></i> Flash Sale
                </a>
            @endcan
        @endcanany

        @canany(['providers.manage', 'api-logs.view'])
            <div class="admin-nav-section">Providers &amp; API</div>
            @can('providers.manage')
                <a href="{{ route('admin.providers.index') }}" class="admin-nav-link {{ $isActive('admin.providers.*') }}">
                    <i class="bi bi-hdd-network-fill"></i> Provider List &amp; Priority
                </a>
            @endcan
            @can('api-logs.view')
                <a href="{{ route('admin.api-logs.index') }}" class="admin-nav-link {{ $isActive('admin.api-logs.*') }}">
                    <i class="bi bi-journal-code"></i> API &amp; Webhook Logs
                </a>
            @endcan
        @endcanany

        @can('payment-gateways.manage')
            <div class="admin-nav-section">Payments &amp; Finance</div>
            <a href="{{ route('admin.payment-gateways.index') }}" class="admin-nav-link {{ $isActive('admin.payment-gateways.*') }}">
                <i class="bi bi-credit-card-fill"></i> Payment Gateway
            </a>
        @endcan

        @can('users.manage')
            <div class="admin-nav-section">Customers &amp; Affiliates</div>
            <a href="{{ route('admin.users.index') }}" class="admin-nav-link {{ $isActive('admin.users.*') }}">
                <i class="bi bi-people-fill"></i> Users
            </a>
        @endcan

        @can('reports.view')
            <div class="admin-nav-section">Reports</div>
            <a href="{{ route('admin.reports.sales-revenue') }}" class="admin-nav-link {{ $isActive('admin.reports.sales-revenue*') }}">
                <i class="bi bi-bar-chart-fill"></i> Sales &amp; Revenue
            </a>
            <a href="{{ route('admin.reports.profit-margin') }}" class="admin-nav-link {{ $isActive('admin.reports.profit-margin*') }}">
                <i class="bi bi-graph-up-arrow"></i> Profit / Margin
            </a>
            <a href="{{ route('admin.reports.provider-performance') }}" class="admin-nav-link {{ $isActive('admin.reports.provider-performance*') }}">
                <i class="bi bi-hdd-network"></i> Provider Performance
            </a>
            <a href="{{ route('admin.reports.product-performance') }}" class="admin-nav-link {{ $isActive('admin.reports.product-performance*') }}">
                <i class="bi bi-controller"></i> Product &amp; Game Performance
            </a>
        @endcan

        @can('cms.manage')
            <div class="admin-nav-section">Content &amp; Marketing</div>
            <a href="{{ route('admin.banners.index') }}" class="admin-nav-link {{ $isActive('admin.banners.*') }}">
                <i class="bi bi-images"></i> Banner
            </a>
            <a href="{{ route('admin.faqs.index') }}" class="admin-nav-link {{ $isActive('admin.faqs.*') }}">
                <i class="bi bi-question-circle-fill"></i> FAQ
            </a>
            <a href="{{ route('admin.pages.index') }}" class="admin-nav-link {{ $isActive('admin.pages.*') }}">
                <i class="bi bi-file-earmark-text-fill"></i> Pages
            </a>
        @endcan

        @can('complaints.manage')
            <div class="admin-nav-section">Support</div>
            <a href="{{ route('admin.complaints.index') }}" class="admin-nav-link {{ $isActive('admin.complaints.*') }}">
                <i class="bi bi-chat-left-text-fill"></i> Customer Complaints
            </a>
        @endcan

        @can('audit-logs.view')
            <div class="admin-nav-section">System &amp; Security</div>
            <a href="{{ route('admin.audit-logs.index') }}" class="admin-nav-link {{ $isActive('admin.audit-logs.*') }}">
                <i class="bi bi-shield-lock-fill"></i> Audit Log
            </a>
        @endcan
    </nav>
</aside>
