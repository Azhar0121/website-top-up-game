@extends('layouts.admin')

@php $isEdit = $provider->exists; @endphp

@section('title', $isEdit ? 'Edit Provider' : 'Tambah Provider')
@section('page-title', $isEdit ? 'Edit Provider' : 'Tambah Provider')
@section('page-subtitle', $isEdit ? $provider->name : 'Daftarkan provider top up baru')

@section('content')
    <div class="admin-card" style="max-width: 640px;">
        <div class="admin-card-header">
            <div class="admin-page-title mb-0">{{ $isEdit ? 'Edit: '.$provider->name : 'Tambah Provider Baru' }}</div>
            <a href="{{ route('admin.providers.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>

        <div class="admin-card-body">
            <form action="{{ $isEdit ? route('admin.providers.update', $provider) : route('admin.providers.store') }}" method="POST">
                @csrf
                @if ($isEdit) @method('PUT') @endif

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label fw-semibold">Nama Provider <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name', $provider->name) }}"
                               class="form-control @error('name') is-invalid @enderror" placeholder="Digiflazz" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="code" class="form-label fw-semibold">Kode <span class="text-danger">*</span></label>
                        <input type="text" name="code" id="code" value="{{ old('code', $provider->code) }}"
                               class="form-control @error('code') is-invalid @enderror" placeholder="digiflazz" required>
                        @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <div class="form-text">Dipakai internal untuk resolve service class-nya (huruf kecil, tanpa spasi).</div>
                    </div>

                    <div class="col-12">
                        <label for="base_url" class="form-label fw-semibold">Base URL API <span class="text-danger">*</span></label>
                        <input type="text" name="base_url" id="base_url" value="{{ old('base_url', $provider->base_url) }}"
                               class="form-control @error('base_url') is-invalid @enderror" placeholder="https://api.digiflazz.com/v1" required>
                        @error('base_url') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="api_key" class="form-label fw-semibold">API Key / Username</label>
                        <input type="password" name="api_key" id="api_key"
                               class="form-control @error('api_key') is-invalid @enderror"
                               placeholder="{{ $isEdit ? 'Biarkan kosong kalau tidak ingin ganti' : '' }}" autocomplete="new-password">
                        @error('api_key') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="api_secret" class="form-label fw-semibold">API Secret / Key</label>
                        <input type="password" name="api_secret" id="api_secret"
                               class="form-control @error('api_secret') is-invalid @enderror"
                               placeholder="{{ $isEdit ? 'Biarkan kosong kalau tidak ingin ganti' : '' }}" autocomplete="new-password">
                        @error('api_secret') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <div class="form-text">Tersimpan terenkripsi di database. Kredensial yang sudah ada tidak pernah ditampilkan lagi di sini demi keamanan.</div>
                    </div>

                    <div class="col-md-6">
                        <label for="priority" class="form-label fw-semibold">Prioritas <span class="text-danger">*</span></label>
                        <input type="number" name="priority" id="priority" value="{{ old('priority', $provider->priority) }}"
                               class="form-control @error('priority') is-invalid @enderror" min="1" required>
                        @error('priority') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <div class="form-text">Angka lebih kecil dicoba lebih dulu.</div>
                    </div>

                    <div class="col-md-6 d-flex align-items-end">
                        <div class="form-check form-switch mb-2">
                            <input type="checkbox" name="is_active" id="is_active" value="1" class="form-check-input"
                                   {{ old('is_active', $provider->is_active ?? true) ? 'checked' : '' }}>
                            <label for="is_active" class="form-check-label">Aktif</label>
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-admin-primary px-4">
                        <i class="bi bi-check-lg"></i> {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Provider' }}
                    </button>
                    <a href="{{ route('admin.providers.index') }}" class="btn btn-outline-secondary px-4">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection
