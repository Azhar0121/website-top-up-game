@extends('layouts.admin')

@php $isEdit = $faq->exists; @endphp

@section('title', $isEdit ? 'Edit FAQ' : 'Tambah FAQ')
@section('page-title', $isEdit ? 'Edit FAQ' : 'Tambah FAQ')
@section('page-subtitle', $isEdit ? $faq->question : 'Tambahkan pertanyaan baru')

@section('content')
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-page-title mb-0">{{ $isEdit ? 'Edit FAQ' : 'Tambah FAQ Baru' }}</div>
            <a href="{{ route('admin.faqs.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>

        <div class="admin-card-body">
            <form action="{{ $isEdit ? route('admin.faqs.update', $faq) : route('admin.faqs.store') }}" method="POST">
                @csrf
                @if ($isEdit) @method('PUT') @endif

                <div class="mb-3">
                    <label for="question" class="form-label fw-semibold">Pertanyaan <span class="text-danger">*</span></label>
                    <input type="text" name="question" id="question" value="{{ old('question', $faq->question) }}"
                           class="form-control @error('question') is-invalid @enderror" required>
                    @error('question') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label for="answer" class="form-label fw-semibold">Jawaban <span class="text-danger">*</span></label>
                    <textarea name="answer" id="answer" rows="4"
                              class="form-control @error('answer') is-invalid @enderror" required>{{ old('answer', $faq->answer) }}</textarea>
                    @error('answer') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="row">
                    <div class="col-sm-4">
                        <label for="sort_order" class="form-label fw-semibold">Urutan Tampil</label>
                        <input type="number" name="sort_order" id="sort_order" min="0"
                               value="{{ old('sort_order', $faq->sort_order ?? 0) }}"
                               class="form-control @error('sort_order') is-invalid @enderror">
                        @error('sort_order') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-sm-4 d-flex align-items-center">
                        <div class="form-check form-switch mt-4">
                            <input type="checkbox" name="is_active" id="is_active" value="1" class="form-check-input"
                                   {{ old('is_active', $faq->is_active ?? true) ? 'checked' : '' }}>
                            <label for="is_active" class="form-check-label">Tampilkan di /faq</label>
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <button type="submit" class="btn btn-admin-primary">
                    <i class="bi bi-check-lg"></i> {{ $isEdit ? 'Simpan Perubahan' : 'Tambah FAQ' }}
                </button>
            </form>
        </div>
    </div>
@endsection
