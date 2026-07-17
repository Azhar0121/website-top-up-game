@extends('layouts.admin')

@php $isEdit = $category->exists; @endphp

@section('title', $isEdit ? 'Edit Kategori' : 'Tambah Kategori')
@section('page-title', $isEdit ? 'Edit Kategori' : 'Tambah Kategori')
@section('page-subtitle', $isEdit ? $category->name : 'Lengkapi data kategori baru')

@section('content')
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-page-title mb-0">{{ $isEdit ? 'Edit: '.$category->name : 'Tambah Kategori Baru' }}</div>
            <a href="{{ route('admin.categories.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>

        <div class="admin-card-body">
            <form action="{{ $isEdit ? route('admin.categories.update', $category) : route('admin.categories.store') }}" method="POST">
                @csrf
                @if ($isEdit) @method('PUT') @endif

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="game_id" class="form-label fw-semibold">Game <span class="text-danger">*</span></label>
                        <select name="game_id" id="game_id" class="form-select @error('game_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Game --</option>
                            @foreach ($games as $game)
                                <option value="{{ $game->id }}" {{ old('game_id', $category->game_id) == $game->id ? 'selected' : '' }}>{{ $game->name }}</option>
                            @endforeach
                        </select>
                        @error('game_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="name" class="form-label fw-semibold">Nama Kategori <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name', $category->name) }}"
                               class="form-control @error('name') is-invalid @enderror" placeholder="Diamond" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="sort_order" class="form-label fw-semibold">Urutan Tampil</label>
                        <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', $category->sort_order ?? 0) }}"
                               class="form-control @error('sort_order') is-invalid @enderror" min="0">
                        @error('sort_order') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <div class="form-text">Angka lebih kecil ditampilkan lebih dulu.</div>
                    </div>

                    <div class="col-md-6 d-flex align-items-end">
                        <div class="form-check form-switch mb-2">
                            <input type="checkbox" name="is_active" id="is_active" value="1" class="form-check-input"
                                   {{ old('is_active', $category->is_active ?? true) ? 'checked' : '' }}>
                            <label for="is_active" class="form-check-label">Aktif / Tampil</label>
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-admin-primary px-4">
                        <i class="bi bi-check-lg"></i> {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Kategori' }}
                    </button>
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary px-4">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection
