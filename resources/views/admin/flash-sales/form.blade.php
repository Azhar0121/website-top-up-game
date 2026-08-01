@extends('layouts.admin')

@php
    $isEdit = $flashSale->exists;
    $selected = collect($selectedProductIds ?? old('product_ids', []))->map(fn ($id) => (int) $id);
@endphp

@section('title', $isEdit ? 'Edit Flash Sale' : 'Buat Flash Sale')
@section('page-title', $isEdit ? 'Edit Flash Sale' : 'Buat Flash Sale')
@section('page-subtitle', $isEdit ? $flashSale->name : 'Atur diskon otomatis berbasis waktu untuk produk tertentu')

@section('content')
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-page-title mb-0">{{ $isEdit ? 'Edit: '.$flashSale->name : 'Buat Flash Sale Baru' }}</div>
            <a href="{{ route('admin.flash-sales.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>

        <div class="admin-card-body">
            <form action="{{ $isEdit ? route('admin.flash-sales.update', $flashSale) : route('admin.flash-sales.store') }}" method="POST">
                @csrf
                @if ($isEdit) @method('PUT') @endif

                <div class="row g-3 mb-2">
                    <div class="col-md-6">
                        <label for="name" class="form-label fw-semibold">Nama Flash Sale <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name', $flashSale->name) }}"
                               class="form-control @error('name') is-invalid @enderror"
                               placeholder="Flash Sale Gajian 25%" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-3">
                        <label for="discount_type" class="form-label fw-semibold">Jenis Diskon <span class="text-danger">*</span></label>
                        <select name="discount_type" id="discount_type" class="form-select @error('discount_type') is-invalid @enderror">
                            <option value="percentage" {{ old('discount_type', $flashSale->discount_type ?? 'percentage') === 'percentage' ? 'selected' : '' }}>Persentase (%)</option>
                            <option value="fixed" {{ old('discount_type', $flashSale->discount_type) === 'fixed' ? 'selected' : '' }}>Nominal Tetap (Rp)</option>
                        </select>
                        @error('discount_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-3">
                        <label for="discount_value" class="form-label fw-semibold">Nilai Diskon <span class="text-danger">*</span></label>
                        <input type="number" name="discount_value" id="discount_value" step="0.01" min="0.01"
                               value="{{ old('discount_value', $flashSale->discount_value) }}"
                               class="form-control @error('discount_value') is-invalid @enderror" required>
                        @error('discount_value') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="start_at" class="form-label fw-semibold">Mulai <span class="text-danger">*</span></label>
                        <input type="datetime-local" name="start_at" id="start_at"
                               value="{{ old('start_at', optional($flashSale->start_at)->format('Y-m-d\TH:i')) }}"
                               class="form-control @error('start_at') is-invalid @enderror" required>
                        @error('start_at') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="end_at" class="form-label fw-semibold">Selesai <span class="text-danger">*</span></label>
                        <input type="datetime-local" name="end_at" id="end_at"
                               value="{{ old('end_at', optional($flashSale->end_at)->format('Y-m-d\TH:i')) }}"
                               class="form-control @error('end_at') is-invalid @enderror" required>
                        @error('end_at') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12">
                        <div class="form-check form-switch">
                            <input type="checkbox" name="is_active" id="is_active" value="1" class="form-check-input"
                                   {{ old('is_active', $flashSale->is_active ?? true) ? 'checked' : '' }}>
                            <label for="is_active" class="form-check-label">Aktif</label>
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <label class="form-label fw-semibold d-block">
                    Produk yang Kena Flash Sale <span class="text-danger">*</span>
                </label>
                <div class="form-text mb-2">Centang produk mana saja yang dapat diskon ini. Bisa pilih beberapa produk lintas game.</div>
                @error('product_ids') <div class="text-danger small mb-2">{{ $message }}</div> @enderror

                <input type="text" id="productSearch" class="form-control form-control-sm mb-3" style="max-width:320px;"
                       placeholder="Cari produk...">

                <div style="max-height: 420px; overflow-y: auto;" class="border rounded p-3" id="productPickerList">
                    @forelse ($games as $game)
                        <div class="mb-3 product-picker-group">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <div class="fw-semibold text-dark">{{ $game->name }}</div>
                                <button type="button" class="btn btn-link btn-sm p-0 select-all-btn" data-game="{{ $game->id }}">Pilih semua</button>
                            </div>
                            <div class="row g-1">
                                @foreach ($game->products as $product)
                                    <div class="col-md-6 product-picker-item" data-name="{{ strtolower($product->name.' '.$game->name) }}">
                                        <div class="form-check">
                                            <input type="checkbox" name="product_ids[]" value="{{ $product->id }}"
                                                   id="product-{{ $product->id }}" class="form-check-input product-checkbox-{{ $game->id }}"
                                                   {{ $selected->contains($product->id) ? 'checked' : '' }}>
                                            <label for="product-{{ $product->id }}" class="form-check-label small">
                                                {{ $product->name }} <span class="text-muted">- Rp{{ number_format($product->base_price, 0, ',', '.') }}</span>
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <p class="text-muted small mb-0">Belum ada produk aktif. Tambahkan produk dulu di menu Products &amp; SKUs.</p>
                    @endforelse
                </div>

                <hr class="my-4">

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-admin-primary px-4">
                        <i class="bi bi-check-lg"></i> {{ $isEdit ? 'Simpan Perubahan' : 'Buat Flash Sale' }}
                    </button>
                    <a href="{{ route('admin.flash-sales.index') }}" class="btn btn-outline-secondary px-4">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.getElementById('productSearch')?.addEventListener('input', function (e) {
            const term = e.target.value.trim().toLowerCase();
            document.querySelectorAll('.product-picker-item').forEach((el) => {
                el.style.display = el.dataset.name.includes(term) ? '' : 'none';
            });
            document.querySelectorAll('.product-picker-group').forEach((group) => {
                const anyVisible = [...group.querySelectorAll('.product-picker-item')].some((el) => el.style.display !== 'none');
                group.style.display = anyVisible ? '' : 'none';
            });
        });

        document.querySelectorAll('.select-all-btn').forEach((btn) => {
            btn.addEventListener('click', function () {
                const checkboxes = document.querySelectorAll('.product-checkbox-' + btn.dataset.game);
                const allChecked = [...checkboxes].every((cb) => cb.checked);
                checkboxes.forEach((cb) => { cb.checked = !allChecked; });
            });
        });
    </script>
@endpush
