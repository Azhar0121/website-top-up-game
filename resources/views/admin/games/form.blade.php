@extends('layouts.admin')

@php $isEdit = $game->exists; @endphp

@section('title', $isEdit ? 'Edit Game' : 'Tambah Game')
@section('page-title', $isEdit ? 'Edit Game' : 'Tambah Game')
@section('page-subtitle', $isEdit ? $game->name : 'Lengkapi data game baru')

@section('content')
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-page-title mb-0">{{ $isEdit ? 'Edit: '.$game->name : 'Tambah Game Baru' }}</div>
            <a href="{{ route('admin.games.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>

        <div class="admin-card-body">
            <form action="{{ $isEdit ? route('admin.games.update', $game) : route('admin.games.store') }}"
                  method="POST" enctype="multipart/form-data">
                @csrf
                @if ($isEdit) @method('PUT') @endif

                <div class="row g-4">
                    <div class="col-lg-8">
                        <div class="mb-3">
                            <label for="name" class="form-label fw-semibold">Nama Game <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" value="{{ old('name', $game->name) }}"
                                   class="form-control @error('name') is-invalid @enderror"
                                   placeholder="Mobile Legends: Bang Bang" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            @if ($isEdit)
                                <div class="form-text">URL saat ini: <code>/game/{{ $game->slug }}</code>. Slug baru otomatis dibuat kalau nama diubah.</div>
                            @endif
                        </div>

                        <div class="mb-3">
                            <label for="tutorial_text" class="form-label fw-semibold">Petunjuk Letak ID Game</label>
                            <textarea name="tutorial_text" id="tutorial_text" rows="3"
                                      class="form-control @error('tutorial_text') is-invalid @enderror"
                                      placeholder="Contoh: ID dan Zone ID bisa dilihat di halaman profil dalam game.">{{ old('tutorial_text', $game->tutorial_text) }}</textarea>
                            @error('tutorial_text') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label fw-semibold">Deskripsi</label>
                            <textarea name="description" id="description" rows="4"
                                      class="form-control @error('description') is-invalid @enderror"
                                      placeholder="Deskripsi singkat game untuk halaman detail...">{{ old('description', $game->description) }}</textarea>
                            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-check form-switch mb-2">
                                    <input type="checkbox" name="is_active" id="is_active" value="1" class="form-check-input"
                                           {{ old('is_active', $game->is_active ?? true) ? 'checked' : '' }}>
                                    <label for="is_active" class="form-check-label">Aktif / Tampil</label>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-check form-switch mb-2">
                                    <input type="checkbox" name="is_favorite" id="is_favorite" value="1" class="form-check-input"
                                           {{ old('is_favorite', $game->is_favorite) ? 'checked' : '' }}>
                                    <label for="is_favorite" class="form-check-label">Favorite Games</label>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-check form-switch mb-2">
                                    <input type="checkbox" name="is_popular" id="is_popular" value="1" class="form-check-input"
                                           {{ old('is_popular', $game->is_popular) ? 'checked' : '' }}>
                                    <label for="is_popular" class="form-check-label">Top Popular</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <label class="form-label fw-semibold">Logo Game</label>
                        <div class="admin-image-preview mb-2 {{ $game->logo_image ? '' : 'd-none' }}" id="logo-preview-wrapper">
                            <img id="logo-preview" src="{{ $game->logo_image ? asset('storage/'.$game->logo_image) : '' }}" alt="Preview logo">
                        </div>
                        <input type="file" name="logo_image" accept="image/*" data-preview-target="logo-preview"
                               class="form-control form-control-sm mb-1 @error('logo_image') is-invalid @enderror">
                        <div class="form-text mb-3">Rasio 1:1 disarankan. Maks 1MB.</div>
                        @error('logo_image') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror

                        <label class="form-label fw-semibold">Banner Game</label>
                        <div class="admin-image-preview mb-2 {{ $game->banner_image ? '' : 'd-none' }}" id="banner-preview-wrapper">
                            <img id="banner-preview" src="{{ $game->banner_image ? asset('storage/'.$game->banner_image) : '' }}" alt="Preview banner">
                        </div>
                        <input type="file" name="banner_image" accept="image/*" data-preview-target="banner-preview"
                               class="form-control form-control-sm @error('banner_image') is-invalid @enderror">
                        <div class="form-text">Rasio 16:9 disarankan. Maks 2MB.</div>
                        @error('banner_image') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>
                </div>

                <hr class="my-4">

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-admin-primary px-4">
                        <i class="bi bi-check-lg"></i> {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Game' }}
                    </button>
                    <a href="{{ route('admin.games.index') }}" class="btn btn-outline-secondary px-4">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/admin/image-preview.js') }}"></script>
@endpush
