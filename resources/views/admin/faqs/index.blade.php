@extends('layouts.admin')

@section('title', 'FAQ')
@section('page-title', 'FAQ')
@section('page-subtitle', 'Kelola pertanyaan yang sering diajukan di halaman /faq')

@section('content')
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-page-title mb-0">{{ $faqs->count() }} FAQ</div>
            <a href="{{ route('admin.faqs.create') }}" class="btn btn-admin-primary btn-sm">
                <i class="bi bi-plus-lg"></i> Tambah FAQ
            </a>
        </div>

        <div class="admin-card-body p-0">
            <div class="table-responsive">
                <table class="table admin-table mb-0">
                    <thead>
                        <tr>
                            <th style="width:80px;">Urutan</th>
                            <th>Pertanyaan</th>
                            <th>Status</th>
                            <th class="text-end" style="width:130px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($faqs as $faq)
                            <tr>
                                <td><span class="badge badge-soft-primary">#{{ $faq->sort_order }}</span></td>
                                <td class="fw-semibold text-dark">{{ $faq->question }}</td>
                                <td>
                                    @if ($faq->is_active)
                                        <span class="badge badge-soft-success">Tampil</span>
                                    @else
                                        <span class="badge badge-soft-muted">Disembunyikan</span>
                                    @endif
                                </td>
                                <td class="text-end pe-3">
                                    <a href="{{ route('admin.faqs.edit', $faq) }}" class="btn btn-sm btn-outline-secondary" title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <form action="{{ route('admin.faqs.destroy', $faq) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Hapus FAQ ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-5">Belum ada FAQ.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
