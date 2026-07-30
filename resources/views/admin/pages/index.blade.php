@extends('layouts.admin')

@section('title', 'Pages')
@section('page-title', 'Pages')
@section('page-subtitle', 'Kelola konten halaman Syarat & Ketentuan dan Kebijakan Privasi')

@section('content')
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-page-title mb-0">Halaman Statis</div>
        </div>

        <div class="admin-card-body p-0">
            <div class="table-responsive">
                <table class="table admin-table mb-0">
                    <thead>
                        <tr>
                            <th>Halaman</th>
                            <th>URL Customer</th>
                            <th>Terakhir Diupdate</th>
                            <th class="text-end" style="width:110px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pages as $page)
                            <tr>
                                <td class="fw-semibold text-dark">{{ $page->title }}</td>
                                <td><code>/{{ $page->slug === 'terms' ? 'syarat-ketentuan' : 'kebijakan-privasi' }}</code></td>
                                <td class="text-muted small">{{ $page->exists ? $page->updated_at->translatedFormat('d M Y, H:i') : 'Belum diisi' }}</td>
                                <td class="text-end pe-3">
                                    <a href="{{ route('admin.pages.edit', $page->slug) }}" class="btn btn-sm btn-outline-secondary" title="Edit">
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
