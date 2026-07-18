@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Ringkasan operasional TopUp Kilat')

@section('content')
    <div class="alert alert-primary border-0" style="background: rgba(91,33,182,.08); color: var(--admin-primary);">
        <i class="bi bi-info-circle me-1"></i>
        Widget KPI (Sales, Profit, Pending, Success Ratio, Best Seller) adalah bagian dari modul
        <strong>Reports</strong> di Fase 2 PRD, jadi belum ditampilkan di sini. Untuk sekarang, dashboard
        ini menjadi pintu masuk ke pengelolaan katalog.
    </div>

    <div class="row g-3">
        <div class="col-md-3">
            <a href="{{ route('admin.orders.index') }}" class="text-decoration-none">
                <div class="admin-card admin-card-accent admin-card-body h-100" style="border-top-color: var(--admin-primary);">
                    <div class="d-flex align-items-center gap-3">
                        <div class="admin-icon-badge" style="background: linear-gradient(135deg, #5B21B6, #431693);">
                            <i class="bi bi-receipt"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-dark">Orders</div>
                            <div class="text-muted small">Transaksi, retry, force success</div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="{{ route('admin.games.index') }}" class="text-decoration-none">
                <div class="admin-card admin-card-accent accent-yellow admin-card-body h-100">
                    <div class="d-flex align-items-center gap-3">
                        <div class="admin-icon-badge accent-yellow">
                            <i class="bi bi-controller"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-dark">Games</div>
                            <div class="text-muted small">Kelola daftar game & banner</div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="{{ route('admin.categories.index') }}" class="text-decoration-none">
                <div class="admin-card admin-card-accent accent-pink admin-card-body h-100">
                    <div class="d-flex align-items-center gap-3">
                        <div class="admin-icon-badge accent-pink">
                            <i class="bi bi-tags-fill"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-dark">Categories</div>
                            <div class="text-muted small">Diamond, Battle Pass, Skin, dll</div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="{{ route('admin.products.index') }}" class="text-decoration-none">
                <div class="admin-card admin-card-accent accent-mint admin-card-body h-100">
                    <div class="d-flex align-items-center gap-3">
                        <div class="admin-icon-badge accent-mint">
                            <i class="bi bi-box-seam-fill"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-dark">Products &amp; SKUs</div>
                            <div class="text-muted small">Harga, margin, stok</div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
@endsection