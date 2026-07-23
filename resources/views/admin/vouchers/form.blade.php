@extends('layouts.admin')

@php $isEdit = $voucher->exists; @endphp

@section('title', $isEdit ? 'Edit Voucher' : 'Tambah Voucher')
@section('page-title', $isEdit ? 'Edit Voucher' : 'Tambah Voucher')
@section('page-subtitle', $isEdit ? $voucher->code : 'Buat kode diskon baru')

@section('content')
    <div class="admin-card" style="max-width: 640px;">
        <div class="admin-card-header">
            <div class="admin-page-title mb-0">{{ $isEdit ? 'Edit: '.$voucher->code : 'Tambah Voucher Baru' }}</div>
            <a href="{{ route('admin.vouchers.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>

        <div class="admin-card-body">
            <form action="{{ $isEdit ? route('admin.vouchers.update', $voucher) : route('admin.vouchers.store') }}" method="POST">
                @csrf
                @if ($isEdit) @method('PUT') @endif

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="code" class="form-label fw-semibold">Kode Voucher <span class="text-danger">*</span></label>
                        <input type="text" name="code" id="code" value="{{ old('code', $voucher->code) }}"
                               class="form-control @error('code') is-invalid @enderror text-uppercase" placeholder="HEMAT5000" required
                               {{ $isEdit ? 'readonly' : '' }}>
                        @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        @if ($isEdit)
                            <div class="form-text">Kode voucher tidak bisa diubah setelah dibuat - hapus &amp; buat baru kalau perlu kode lain.</div>
                        @endif
                    </div>

                    <div class="col-md-6">
                        <label for="type" class="form-label fw-semibold">Tipe Diskon <span class="text-danger">*</span></label>
                        <select name="type" id="type" class="form-select @error('type') is-invalid @enderror" required>
                            <option value="fixed" {{ old('type', $voucher->type) === 'fixed' ? 'selected' : '' }}>Nominal Tetap (Rp)</option>
                            <option value="percentage" {{ old('type', $voucher->type) === 'percentage' ? 'selected' : '' }}>Persentase (%)</option>
                        </select>
                        @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="value" class="form-label fw-semibold">Nilai Diskon <span class="text-danger">*</span></label>
                        <input type="number" name="value" id="value" value="{{ old('value', $voucher->value) }}"
                               class="form-control @error('value') is-invalid @enderror" min="0" step="0.01" required>
                        @error('value') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <div class="form-text">Isi rupiah kalau tipe Nominal Tetap, atau angka persen (0-100) kalau tipe Persentase.</div>
                    </div>

                    <div class="col-md-6">
                        <label for="max_discount" class="form-label fw-semibold">Maks. Diskon (Rp)</label>
                        <input type="number" name="max_discount" id="max_discount" value="{{ old('max_discount', $voucher->max_discount) }}"
                               class="form-control @error('max_discount') is-invalid @enderror" min="0" placeholder="Khusus tipe Persentase">
                        @error('max_discount') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <div class="form-text">Cap diskon supaya tidak kebablasan untuk tipe Persentase. Kosongkan kalau tidak ada batas.</div>
                    </div>

                    <div class="col-md-6">
                        <label for="min_transaction" class="form-label fw-semibold">Minimal Transaksi (Rp)</label>
                        <input type="number" name="min_transaction" id="min_transaction" value="{{ old('min_transaction', $voucher->min_transaction ?? 0) }}"
                               class="form-control @error('min_transaction') is-invalid @enderror" min="0">
                        @error('min_transaction') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="usage_limit" class="form-label fw-semibold">Kuota Pemakaian</label>
                        <input type="number" name="usage_limit" id="usage_limit" value="{{ old('usage_limit', $voucher->usage_limit) }}"
                               class="form-control @error('usage_limit') is-invalid @enderror" min="1" placeholder="Kosongkan = tanpa batas">
                        @error('usage_limit') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        @if ($isEdit)
                            <div class="form-text">Sudah dipakai {{ $voucher->used_count }} kali.</div>
                        @endif
                    </div>

                    <div class="col-md-6">
                        <label for="start_date" class="form-label fw-semibold">Mulai Berlaku</label>
                        <input type="datetime-local" name="start_date" id="start_date"
                               value="{{ old('start_date', $voucher->start_date?->format('Y-m-d\TH:i')) }}"
                               class="form-control @error('start_date') is-invalid @enderror">
                        @error('start_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="end_date" class="form-label fw-semibold">Berakhir</label>
                        <input type="datetime-local" name="end_date" id="end_date"
                               value="{{ old('end_date', $voucher->end_date?->format('Y-m-d\TH:i')) }}"
                               class="form-control @error('end_date') is-invalid @enderror">
                        @error('end_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <div class="form-text">Kosongkan tanggal mulai/berakhir kalau voucher berlaku terus-menerus.</div>
                    </div>

                    <div class="col-12">
                        <div class="form-check form-switch">
                            <input type="checkbox" name="is_active" id="is_active" value="1" class="form-check-input"
                                   {{ old('is_active', $voucher->is_active ?? true) ? 'checked' : '' }}>
                            <label for="is_active" class="form-check-label">Aktif</label>
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-admin-primary px-4">
                        <i class="bi bi-check-lg"></i> {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Voucher' }}
                    </button>
                    <a href="{{ route('admin.vouchers.index') }}" class="btn btn-outline-secondary px-4">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection
