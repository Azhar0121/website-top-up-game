@extends('layouts.admin')

@section('title', 'Flash Sale')
@section('page-title', 'Flash Sale')
@section('page-subtitle', 'Diskon otomatis berbasis waktu, tanpa perlu kode voucher')

@section('content')
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-page-title mb-0">{{ $flashSales->count() }} Flash Sale</div>
            <a href="{{ route('admin.flash-sales.create') }}" class="btn btn-admin-primary btn-sm">
                <i class="bi bi-plus-lg"></i> Buat Flash Sale
            </a>
        </div>

        <div class="admin-card-body p-0">
            <div class="table-responsive">
                <table class="table admin-table mb-0">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Diskon</th>
                            <th>Produk</th>
                            <th>Periode</th>
                            <th>Status</th>
                            <th class="text-end" style="width:170px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($flashSales as $flashSale)
                            <tr>
                                <td class="fw-semibold text-dark">{{ $flashSale->name }}</td>
                                <td>
                                    {{ $flashSale->discount_type === 'percentage' ? rtrim(rtrim(number_format($flashSale->discount_value, 2), '0'), '.').'%' : 'Rp'.number_format($flashSale->discount_value, 0, ',', '.') }}
                                </td>
                                <td>{{ $flashSale->products_count }} produk</td>
                                <td class="small text-muted">
                                    {{ $flashSale->start_at->translatedFormat('d M Y, H:i') }}<br>
                                    s/d {{ $flashSale->end_at->translatedFormat('d M Y, H:i') }}
                                </td>
                                <td>
                                    @php
                                        $statusColor = match ($flashSale->statusLabel()) {
                                            'Berjalan' => 'success',
                                            'Terjadwal' => 'primary',
                                            'Berakhir' => 'muted',
                                            default => 'muted',
                                        };
                                    @endphp
                                    <span class="badge badge-soft-{{ $statusColor }}">{{ $flashSale->statusLabel() }}</span>
                                </td>
                                <td class="text-end pe-3">
                                    <form action="{{ route('admin.flash-sales.toggle', $flashSale) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm {{ $flashSale->is_active ? 'btn-outline-danger' : 'btn-outline-success' }}" title="{{ $flashSale->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                            <i class="bi bi-power"></i>
                                        </button>
                                    </form>
                                    <a href="{{ route('admin.flash-sales.edit', $flashSale) }}" class="btn btn-sm btn-outline-secondary" title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <form action="{{ route('admin.flash-sales.destroy', $flashSale) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Hapus flash sale &quot;{{ $flashSale->name }}&quot;?');">
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
                                <td colspan="6" class="text-center text-muted py-5">Belum ada flash sale. Buat yang pertama untuk kasih diskon otomatis ke produk tertentu.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="alert alert-primary border-0 mt-3" style="background: rgba(91,33,182,.08); color: var(--admin-primary);">
        <i class="bi bi-info-circle me-1"></i>
        Diskon flash sale otomatis kepakai saat checkout tanpa customer perlu masukkan kode apapun. Kalau ada dua flash sale yang tumpang tindih untuk produk yang sama, sistem pakai yang diskonnya paling besar. Voucher kode tetap bisa dipakai di atas harga flash sale (menumpuk).
    </div>
@endsection
