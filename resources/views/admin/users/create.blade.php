@extends('layouts.admin')

@section('title', 'Tambah User')
@section('page-title', 'Tambah User')
@section('page-subtitle', 'Buat akun baru untuk staff atau customer')

@section('content')
    <div class="admin-card" style="max-width: 520px;">
        <div class="admin-card-header">
            <div class="admin-page-title mb-0">Tambah User Baru</div>
            <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>

        <div class="admin-card-body">
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="name" class="form-label fw-semibold">Nama <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}"
                           class="form-control @error('name') is-invalid @enderror" required>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}"
                           class="form-control @error('email') is-invalid @enderror" required>
                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="row">
                    <div class="col-sm-6 mb-3">
                        <label for="password" class="form-label fw-semibold">Password <span class="text-danger">*</span></label>
                        <input type="password" name="password" id="password" autocomplete="new-password"
                               class="form-control @error('password') is-invalid @enderror" required>
                        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <div class="form-text">Minimal 8 karakter.</div>
                    </div>
                    <div class="col-sm-6 mb-3">
                        <label for="password_confirmation" class="form-label fw-semibold">Ulangi Password <span class="text-danger">*</span></label>
                        <input type="password" name="password_confirmation" id="password_confirmation" autocomplete="new-password"
                               class="form-control" required>
                    </div>
                </div>

                <label class="form-label fw-semibold">Role (boleh pilih lebih dari satu) <span class="text-danger">*</span></label>
                <div class="row g-2 mb-2">
                    @foreach ($roles as $r)
                        <div class="col-6">
                            <div class="form-check">
                                <input type="checkbox" name="roles[]" value="{{ $r }}" id="role_{{ $r }}"
                                       class="form-check-input" {{ in_array($r, old('roles', ['customer'])) ? 'checked' : '' }}>
                                <label for="role_{{ $r }}" class="form-check-label">{{ ucfirst($r) }}</label>
                            </div>
                        </div>
                    @endforeach
                </div>
                @error('roles') <div class="text-danger small mb-2">{{ $message }}</div> @enderror

                <div class="form-text mb-3">
                    Pilih "Customer" untuk akun belanja biasa. Role staff (owner/admin/finance/cs/marketing/developer) langsung dapat akses dashboard admin sesuai izin masing-masing role.
                </div>

                <button type="submit" class="btn btn-admin-primary px-4">
                    <i class="bi bi-check-lg"></i> Tambah User
                </button>
            </form>
        </div>
    </div>
@endsection
