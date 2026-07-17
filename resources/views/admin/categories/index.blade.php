@extends('layouts.admin')

@section('title', 'Categories')
@section('page-title', 'Categories')
@section('page-subtitle', 'Diamond, Battle Pass, Gift Card, Skin, dan pembagian produk lainnya')

@section('content')
    <div class="admin-card">
        <div class="admin-card-header">
            <div>
                <div class="admin-page-title mb-0">Daftar Kategori</div>
                <div class="admin-page-subtitle">{{ $categories->total() }} kategori</div>
            </div>

            <div class="d-flex gap-2 flex-wrap">
                <form action="{{ route('admin.categories.index') }}" method="GET" class="d-flex gap-2">
                    <select name="game_id" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">Semua Game</option>
                        @foreach ($games as $game)
                            <option value="{{ $game->id }}" {{ request('game_id') == $game->id ? 'selected' : '' }}>{{ $game->name }}</option>
                        @endforeach
                    </select>
                </form>
                <a href="{{ route('admin.categories.create') }}" class="btn btn-admin-primary btn-sm">
                    <i class="bi bi-plus-lg"></i> Tambah Kategori
                </a>
            </div>
        </div>

        <div class="admin-card-body p-0">
            <div class="table-responsive">
                <table class="table admin-table mb-0">
                    <thead>
                        <tr>
                            <th>Game</th>
                            <th>Nama Kategori</th>
                            <th>Urutan</th>
                            <th>Produk</th>
                            <th>Status</th>
                            <th class="text-end" style="width:140px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($categories as $category)
                            <tr>
                                <td>{{ $category->game->name ?? '-' }}</td>
                                <td class="fw-semibold text-dark">{{ $category->name }}</td>
                                <td>{{ $category->sort_order }}</td>
                                <td>{{ $category->products_count }}</td>
                                <td>
                                    @if ($category->is_active)
                                        <span class="badge badge-soft-success">Aktif</span>
                                    @else
                                        <span class="badge badge-soft-muted">Nonaktif</span>
                                    @endif
                                </td>
                                <td class="text-end pe-3">
                                    <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-sm btn-outline-secondary" title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="d-inline" data-confirm-delete="{{ $category->name }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-5">
                                    Belum ada kategori. <a href="{{ route('admin.categories.create') }}">Tambah kategori pertama</a>.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($categories->hasPages())
            <div class="admin-card-body pt-0">
                {{ $categories->links() }}
            </div>
        @endif
    </div>
@endsection
