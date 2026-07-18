@extends('layouts.admin')

@section('title', 'Products & SKUs')
@section('page-title', 'Products & SKUs')
@section('page-subtitle', 'Harga jual, margin, dan stok tiap produk top up')

@section('content')
    <div class="admin-card">
        <div class="admin-card-header">
            <div>
                <div class="admin-page-title mb-0">Daftar Produk</div>
                <div class="admin-page-subtitle">{{ $products->total() }} produk</div>
            </div>

            <div class="d-flex gap-2 flex-wrap">
                <form action="{{ route('admin.products.index') }}" method="GET" class="d-flex gap-2">
                    <select name="game_id" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">Semua Game</option>
                        @foreach ($games as $game)
                            <option value="{{ $game->id }}" {{ request('game_id') == $game->id ? 'selected' : '' }}>{{ $game->name }}</option>
                        @endforeach
                    </select>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="Cari produk...">
                    @if(request('search') || request('game_id'))
                        <a href="{{ route('admin.products.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
                    @endif
                </form>
                <a href="{{ route('admin.products.create') }}" class="btn btn-admin-primary btn-sm">
                    <i class="bi bi-plus-lg"></i> Tambah Produk
                </a>
            </div>
        </div>

        <div class="admin-card-body p-0">
            <div class="table-responsive">
                <table class="table admin-table mb-0">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Game / Kategori</th>
                            <th>Region</th>
                            <th>Harga Jual</th>
                            <th>Margin</th>
                            <th>Stok</th>
                            <th>Provider</th>
                            <th>Status</th>
                            <th class="text-end" style="width:140px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($products as $product)
                            <tr>
                                <td class="fw-semibold text-dark">{{ $product->name }}</td>
                                <td>
                                    <div>{{ $product->game->name ?? '-' }}</div>
                                    <div class="text-muted small">{{ $product->category->name ?? '-' }}</div>
                                </td>
                                <td>{{ $product->region }}</td>
                                <td>Rp{{ number_format($product->base_price, 0, ',', '.') }}</td>
                                <td>
                                    <span class="badge badge-soft-primary">
                                        {{ $product->margin_type === 'fixed' ? 'Rp'.number_format($product->margin_value, 0, ',', '.') : $product->margin_value.'%' }}
                                    </span>
                                    @if ($product->auto_price)
                                        <div class="text-muted small mt-1"><i class="bi bi-arrow-repeat"></i> Auto</div>
                                    @endif
                                </td>
                                <td>{{ $product->stock ?? '∞' }}</td>
                                <td>
                                    @if ($product->provider_products_count > 0)
                                        <span class="badge badge-soft-success">{{ $product->provider_products_count }} provider</span>
                                    @else
                                        <span class="badge badge-soft-danger" title="Order untuk produk ini akan selalu gagal sampai cost_price diisi">Belum ke-mapping</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($product->is_active)
                                        <span class="badge badge-soft-success">Aktif</span>
                                    @else
                                        <span class="badge badge-soft-muted">Nonaktif</span>
                                    @endif
                                </td>
                                <td class="text-end pe-3">
                                    <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-sm btn-outline-secondary" title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                        ~            </a>
                                    <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="d-inline" data-confirm-delete="{{ $product->name }}">
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
                                <td colspan="9" class="text-center text-muted py-5">
                                    Belum ada produk. <a href="{{ route('admin.products.create') }}">Tambah produk pertama</a>.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($products->hasPages())
            <div class="admin-card-body pt-0">
                {{ $products->links() }}
            </div>
        @endif
    </div>
@endsection
