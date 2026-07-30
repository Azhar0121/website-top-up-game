@extends('layouts.admin')

@section('title', 'Banner')
@section('page-title', 'Banner Management')
@section('page-subtitle', 'Kelola banner promo carousel di beranda customer')

@section('content')
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-page-title mb-0">{{ $banners->count() }} Banner</div>
            <a href="{{ route('admin.banners.create') }}" class="btn btn-admin-primary btn-sm">
                <i class="bi bi-plus-lg"></i> Tambah Banner
            </a>
        </div>

        <div class="admin-card-body p-0">
            <div class="table-responsive">
                <table class="table admin-table mb-0">
                    <thead>
                        <tr>
                            <th style="width:140px;">Preview</th>
                            <th>Judul</th>
                            <th>Link Tujuan</th>
                            <th style="width:80px;">Urutan</th>
                            <th>Status</th>
                            <th class="text-end" style="width:170px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($banners as $banner)
                            <tr>
                                <td>
                                    <img src="{{ $banner->image_url }}" alt="{{ $banner->title }}"
                                         style="width:110px; height:55px; object-fit:cover; border-radius:8px;">
                                </td>
                                <td class="fw-semibold text-dark">{{ $banner->title }}</td>
                                <td class="text-muted small">{{ $banner->link_url ?: '-' }}</td>
                                <td><span class="badge badge-soft-primary">#{{ $banner->sort_order }}</span></td>
                                <td>
                                    @if ($banner->is_active)
                                        <span class="badge badge-soft-success">Aktif</span>
                                    @else
                                        <span class="badge badge-soft-muted">Nonaktif</span>
                                    @endif
                                </td>
                                <td class="text-end pe-3">
                                    <form action="{{ route('admin.banners.toggle', $banner) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm {{ $banner->is_active ? 'btn-outline-danger' : 'btn-outline-success' }}" title="{{ $banner->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                            <i class="bi bi-power"></i>
                                        </button>
                                    </form>
                                    <a href="{{ route('admin.banners.edit', $banner) }}" class="btn btn-sm btn-outline-secondary" title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <form action="{{ route('admin.banners.destroy', $banner) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Hapus banner &quot;{{ $banner->title }}&quot;?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-5">Belum ada banner. Tambahkan banner pertama untuk tampil di carousel beranda.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="alert alert-primary border-0 mt-3" style="background: rgba(91,33,182,.08); color: var(--admin-primary);">
        <i class="bi bi-info-circle me-1"></i>
        Banner ditampilkan di beranda diurut dari angka Urutan terkecil. Ukuran gambar disarankan rasio lebar 21:7 (misal 1400x467px) supaya tidak terpotong aneh di carousel.
    </div>
@endsection
