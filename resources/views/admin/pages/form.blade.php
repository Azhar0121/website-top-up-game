@extends('layouts.admin')

@section('title', 'Edit Halaman')
@section('page-title', 'Edit Halaman')
@section('page-subtitle', $page->title)

@section('content')
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-page-title mb-0">Edit: {{ $page->title }}</div>
            <a href="{{ route('admin.pages.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>

        <div class="admin-card-body">
            <form action="{{ route('admin.pages.update', $page->slug) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="title" class="form-label fw-semibold">Judul Halaman <span class="text-danger">*</span></label>
                    <input type="text" name="title" id="title" value="{{ old('title', $page->title) }}"
                           class="form-control @error('title') is-invalid @enderror" required>
                    @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label for="content" class="form-label fw-semibold">Konten <span class="text-danger">*</span></label>
                    <textarea name="content" id="content" rows="16"
                              class="form-control font-monospace @error('content') is-invalid @enderror"
                              style="font-size:.85rem;" required>{{ old('content', $page->content) }}</textarea>
                    @error('content') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    <div class="form-text">
                        Ditulis dalam HTML sederhana (boleh pakai <code>&lt;p&gt;</code>, <code>&lt;h3&gt;</code>, <code>&lt;ul&gt;&lt;li&gt;</code>, <code>&lt;strong&gt;</code>, dll) —
                        akan tampil apa adanya di halaman <code>/{{ $page->slug === 'terms' ? 'syarat-ketentuan' : 'kebijakan-privasi' }}</code>.
                    </div>
                </div>

                <hr class="my-4">

                <button type="submit" class="btn btn-admin-primary">
                    <i class="bi bi-check-lg"></i> Simpan Perubahan
                </button>
            </form>
        </div>
    </div>
@endsection
