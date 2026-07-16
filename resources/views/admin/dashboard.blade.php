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
        <div class="col-md-4">
            <a href="{{ route('admin.games.index') }}" class="text-decoration-none">
                <div class="admin-card admin-card-body h-100">
                    <div class="d-flex align-items-center gap-3">
                        <div class="admin-thumb-placeholder" style="width:52px;height:52px;">
                            <i class="bi bi-controller fs-4" style="color: var(--admin-primary);"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-dark">Games</div>
                            <div class="text-muted small">Kelola daftar game & banner</div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('admin.categories.index') }}" class="text-decoration-none">
                <div class="admin-card admin-card-body h-100">
                    <div class="d-flex align-items-center gap-3">
                        <div class="admin-thumb-placeholder" style="width:52px;height:52px;">
                            <i class="bi bi-tags-fill fs-4" style="color: var(--admin-primary);"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-dark">Categories</div>
                            <div class="text-muted small">Diamond, Battle Pass, Skin, dll</div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('admin.products.index') }}" class="text-decoration-none">
                <div class="admin-card admin-card-body h-100">
                    <div class="d-flex align-items-center gap-3">
                        <div class="admin-thumb-placeholder" style="width:52px;height:52px;">
                            <i class="bi bi-box-seam-fill fs-4" style="color: var(--admin-primary);"></i>
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
