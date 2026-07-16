<header class="admin-topbar">
    <div class="d-flex align-items-center gap-2">
        <button type="button" class="admin-topbar-toggle" id="adminSidebarToggle" aria-label="Buka menu">
            <i class="bi bi-list"></i>
        </button>
        <div>
            <div class="fw-bold">@yield('page-title', 'Dashboard')</div>
            <div class="admin-page-subtitle small">@yield('page-subtitle', '')</div>
        </div>
    </div>

    <div class="dropdown">
        <button class="btn btn-light d-flex align-items-center gap-2 border" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            <span class="admin-thumb-placeholder" style="width:32px;height:32px;font-size:.75rem;">
                {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
            </span>
            <span class="d-none d-sm-inline fw-semibold">{{ auth()->user()->name ?? 'Admin' }}</span>
            <i class="bi bi-chevron-down small"></i>
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
            <li><h6 class="dropdown-header">{{ auth()->user()->email ?? '' }}</h6></li>
            <li><hr class="dropdown-divider"></li>
            <li>
                <form action="{{ route('admin.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="dropdown-item text-danger">
                        <i class="bi bi-box-arrow-right me-1"></i> Logout
                    </button>
                </form>
            </li>
        </ul>
    </div>
</header>
