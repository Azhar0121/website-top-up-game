@extends('layouts.admin')

@php $isEdit = $product->exists; @endphp

@section('title', $isEdit ? 'Edit Produk' : 'Tambah Produk')
@section('page-title', $isEdit ? 'Edit Produk' : 'Tambah Produk')
@section('page-subtitle', $isEdit ? $product->name : 'Lengkapi data produk baru')

@section('content')
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-page-title mb-0">{{ $isEdit ? 'Edit: '.$product->name : 'Tambah Produk Baru' }}</div>
            <a href="{{ route('admin.products.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>

        <div class="admin-card-body">
            <form action="{{ $isEdit ? route('admin.products.update', $product) : route('admin.products.store') }}" method="POST">
                @csrf
                @if ($isEdit) @method('PUT') @endif

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="game_id" class="form-label fw-semibold">Game <span class="text-danger">*</span></label>
                        <select name="game_id" id="game_id" class="form-select @error('game_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Game --</option>
                            @foreach ($games as $game)
                                <option value="{{ $game->id }}" {{ old('game_id', $product->game_id) == $game->id ? 'selected' : '' }}>{{ $game->name }}</option>
                            @endforeach
                        </select>
                        @error('game_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="category_id" class="form-label fw-semibold">Kategori <span class="text-danger">*</span></label>
                        <select name="category_id" id="category_id" data-selected="{{ old('category_id', $product->category_id) }}"
                                class="form-select @error('category_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Kategori --</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" data-game-id="{{ $category->game_id }}"
                                        {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <div class="form-text">Daftar kategori otomatis mengikuti game yang dipilih.</div>
                    </div>

                    <div class="col-md-8">
                        <label for="name" class="form-label fw-semibold">Nama Produk <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name', $product->name) }}"
                               class="form-control @error('name') is-invalid @enderror"
                               placeholder="86 Diamonds" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="region" class="form-label fw-semibold">Region <span class="text-danger">*</span></label>
                        <select name="region" id="region" class="form-select @error('region') is-invalid @enderror" required>
                            @foreach (['Global', 'Indo', 'SEA'] as $region)
                                <option value="{{ $region }}" {{ old('region', $product->region ?? 'Global') === $region ? 'selected' : '' }}>{{ $region }}</option>
                            @endforeach
                        </select>
                        @error('region') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="cost_price" class="form-label fw-semibold">Harga Modal / Cost Price (Rp) <span class="text-danger">*</span></label>
                        <input type="number" name="cost_price" id="cost_price" value="{{ old('cost_price', $costPrice) }}"
                               class="form-control @error('cost_price') is-invalid @enderror" min="0" step="1" required>
                        @error('cost_price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <div class="form-text">Harga modal dari provider. Dipakai untuk hitung margin, dan otomatis dipetakan ke semua provider yang sedang aktif supaya produk ini benar-benar bisa diproses saat ada order masuk.</div>
                    </div>

                    <div class="col-md-4">
                        <label for="provider_sku_code" class="form-label fw-semibold">Kode SKU Provider</label>
                        <input type="text" name="provider_sku_code" id="provider_sku_code" value="{{ old('provider_sku_code', $providerSkuCode) }}"
                               class="form-control @error('provider_sku_code') is-invalid @enderror" placeholder="Kosongkan untuk auto-generate">
                        @error('provider_sku_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="base_price" class="form-label fw-semibold">Harga Jual (Rp) <span class="text-danger">*</span></label>
                        <input type="number" name="base_price" id="base_price" value="{{ old('base_price', $product->base_price) }}"
                               class="form-control @error('base_price') is-invalid @enderror" min="0" step="1" required>
                        @error('base_price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        @if ($product->exists && $product->auto_price)
                            <div class="form-text">Auto-price aktif: harga akan dihitung ulang otomatis dari margin di bawah, mengikuti modal provider prioritas utama.</div>
                        @endif
                    </div>

                    <div class="col-md-4">
                        <label for="stock" class="form-label fw-semibold">Stok</label>
                        <input type="number" name="stock" id="stock" value="{{ old('stock', $product->stock) }}"
                               class="form-control @error('stock') is-invalid @enderror" min="0" placeholder="Kosongkan jika unlimited">
                        @error('stock') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <div class="form-text">Kosongkan untuk produk digital tanpa batas stok.</div>
                    </div>

                    <div class="col-md-4">
                        <label for="sort_order" class="form-label fw-semibold">Urutan Tampil</label>
                        <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', $product->sort_order ?? 0) }}"
                               class="form-control @error('sort_order') is-invalid @enderror" min="0">
                        @error('sort_order') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="margin_type" class="form-label fw-semibold">Tipe Margin <span class="text-danger">*</span></label>
                        <select name="margin_type" id="margin_type" class="form-select @error('margin_type') is-invalid @enderror" required>
                            <option value="percentage" {{ old('margin_type', $product->margin_type ?? 'percentage') === 'percentage' ? 'selected' : '' }}>Persentase (%)</option>
                            <option value="fixed" {{ old('margin_type', $product->margin_type ?? 'percentage') === 'fixed' ? 'selected' : '' }}>Nominal Tetap (Rp)</option>
                        </select>
                        @error('margin_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="margin_value" class="form-label fw-semibold">Nilai Margin <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text" id="margin-unit-label">%</span>
                            <input type="number" name="margin_value" id="margin_value" value="{{ old('margin_value', $product->margin_value ?? 10) }}"
                                   class="form-control @error('margin_value') is-invalid @enderror" min="0" step="0.1" required>
                        </div>
                        @error('margin_value') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4 d-flex align-items-end">
                        <div class="form-check form-switch mb-2">
                            <input type="checkbox" name="auto_price" id="auto_price" value="1" class="form-check-input"
                                   {{ old('auto_price', $product->auto_price ?? true) ? 'checked' : '' }}>
                            <label for="auto_price" class="form-check-label">Auto-price dari margin</label>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-check form-switch mt-4">
                            <input type="checkbox" name="is_active" id="is_active" value="1" class="form-check-input"
                                   {{ old('is_active', $product->is_active ?? true) ? 'checked' : '' }}>
                            <label for="is_active" class="form-check-label">Aktif / Dijual</label>
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-admin-primary px-4">
                        <i class="bi bi-check-lg"></i> {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Produk' }}
                    </button>
                    <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary px-4">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/admin/product-form.js') }}"></script>
@endpush
