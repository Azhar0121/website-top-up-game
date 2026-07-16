@extends('layouts.admin')

@section('title', 'Games')
@section('page-title', 'Games')
@section('page-subtitle', 'Kelola daftar game beserta banner, logo, dan status tampil')

@section('content')
    <div class="admin-card">
        <div class="admin-card-header">
            <div>
                <div class="admin-page-title mb-0">Daftar Game</div>
                <div class="admin-page-subtitle">{{ $games->total() }} game terdaftar</div>
            </div>

            <div class="d-flex gap-2 flex-wrap">
                <form action="{{ route('admin.games.index') }}" method="GET" class="d-flex gap-2">
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="Cari nama game...">
                    @if(request('search'))
                        <a href="{{ route('admin.games.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
                    @endif
                </form>
                <a href="{{ route('admin.games.create') }}" class="btn btn-admin-primary btn-sm">
                    <i class="bi bi-plus-lg"></i> Tambah Game
                </a>
            </div>
        </div>

        <div class="admin-card-body p-0">
            <div class="table-responsive">
                <table class="table admin-table mb-0">
                    <thead>
                        <tr>
                            <th style="width:56px;"></th>
                            <th>Nama</th>
                            <th>Kategori</th>
                            <th>Produk</th>
                            <th>Label</th>
                            <th>Status</th>
                            <th class="text-end" style="width:140px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($games as $game)
                            <tr>
                                <td class="ps-3">
                                    @if ($game->logo_image)
                                        <img src="{{ asset('storage/'.$game->logo_image) }}" class="admin-thumb" alt="{{ $game->name }}">
                                    @else
                                        <div class="admin-thumb-placeholder">{{ strtoupper(substr($game->name, 0, 1)) }}</div>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $game->name }}</div>
                                    <div class="text-muted small">/{{ $game->slug }}</div>
                                </td>
                                <td>{{ $game->categories_count }}</td>
                                <td>{{ $game->products_count }}</td>
                                <td>
                                    @if ($game->is_favorite)
                                        <span class="badge badge-soft-primary">Favorite</span>
                                    @endif
                                    @if ($game->is_popular)
                                        <span class="badge badge-soft-primary">Populer</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($game->is_active)
                                        <span class="badge badge-soft-success">Aktif</span>
                                    @else
                                        <span class="badge badge-soft-muted">Nonaktif</span>
                                    @endif
                                </td>
                                <td class="text-end pe-3">
                                    <a href="{{ route('admin.games.edit', $game) }}" class="btn btn-sm btn-outline-secondary" title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <form action="{{ route('admin.games.destroy', $game) }}" method="POST" class="d-inline" data-confirm-delete="{{ $game->name }}">
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
                                <td colspan="7" class="text-center text-muted py-5">
                                    Belum ada game. <a href="{{ route('admin.games.create') }}">Tambah game pertama</a>.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($games->hasPages())
            <div class="admin-card-body pt-0">
                {{ $games->links() }}
            </div>
        @endif
    </div>
@endsection
