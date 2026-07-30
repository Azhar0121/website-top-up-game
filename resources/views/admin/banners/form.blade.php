@extends('layouts.admin')

@php $isEdit = $banner->exists; @endphp

@section('title', $isEdit ? 'Edit Banner' : 'Tambah Banner')
@section('page-title', $isEdit ? 'Edit Banner' : 'Tambah Banner')
@section('page-subtitle', $isEdit ? $banner->title : 'Tambahkan banner baru untuk carousel beranda')

@section('content')
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-page-title mb-0">{{ $isEdit ? 'Edit: '.$banner->title : 'Tambah Banner Baru' }}</div>
            <a href="{{ route('admin.banners.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>

        <div class="admin-card-body">
            <form action="{{ $isEdit ? route('admin.banners.update', $banner) : route('admin.banners.store') }}"
                  method="POST" enctype="multipart/form-data">
                @csrf
                @if ($isEdit) @method('PUT') @endif

                <div class="row g-4">
                    <div class="col-lg-8">
                        <div class="mb-3">
                            <label for="title" class="form-label fw-semibold">Judul Banner <span class="text-danger">*</span></label>
                            <input type="text" name="title" id="title" value="{{ old('title', $banner->title) }}"
                                   class="form-control @error('title') is-invalid @enderror"
                                   placeholder="Promo Diamond MLBB Diskon 10%" required>
                            @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <div class="form-text">Judul ini cuma buat identifikasi di dashboard, tidak ditampilkan di atas gambar banner.</div>
                        </div>

                        <div class="mb-3">
                            <label for="link_url" class="form-label fw-semibold">Link Tujuan</label>
                            <input type="url" name="link_url" id="link_url" value="{{ old('link_url', $banner->link_url) }}"
                                   class="form-control @error('link_url') is-invalid @enderror"
                                   placeholder="https://topupkilat.test/game/mobile-legends">
                            @error('link_url') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <div class="form-text">Opsional. Kosongkan kalau banner tidak perlu bisa diklik.</div>
                        </div>

                        <div class="row">
                            <div class="col-sm-4">
                                <label for="sort_order" class="form-label fw-semibold">Urutan Tampil</label>
                                <input type="number" name="sort_order" id="sort_order" min="0"
                                       value="{{ old('sort_order', $banner->sort_order ?? 0) }}"
                                       class="form-control @error('sort_order') is-invalid @enderror">
                                @error('sort_order') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                <div class="form-text">Angka lebih kecil tampil lebih dulu.</div>
                            </div>
                            <div class="col-sm-4 d-flex align-items-center">
                                <div class="form-check form-switch mt-4">
                                    <input type="checkbox" name="is_active" id="is_active" value="1" class="form-check-input"
                                           {{ old('is_active', $banner->is_active ?? true) ? 'checked' : '' }}>
                                    <label for="is_active" class="form-check-label">Aktif / Tampil</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <label class="form-label fw-semibold">Gambar Banner {{ $isEdit ? '' : '*' }}</label>
                        @if ($isEdit && $banner->image_url)
                            <img src="{{ $banner->image_url }}" alt="{{ $banner->title }}"
                                 class="mb-2 rounded" style="width:100%; aspect-ratio:21/7; object-fit:cover;">
                        @endif
                        <input type="file" name="image" accept="image/*"
                               class="form-control @error('image') is-invalid @enderror">
                        @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <div class="form-text">Rasio disarankan 21:7 (misal 1400x467px), maks 2MB. {{ $isEdit ? 'Kosongkan kalau tidak mau ganti gambar.' : '' }}</div>
                    </div>
                </div>

                <hr class="my-4">

                <button type="submit" class="btn btn-admin-primary">
                    <i class="bi bi-check-lg"></i> {{ $isEdit ? 'Simpan Perubahan' : 'Tambah Banner' }}
                </button>
            </form>
        </div>
    </div>
@endsection
