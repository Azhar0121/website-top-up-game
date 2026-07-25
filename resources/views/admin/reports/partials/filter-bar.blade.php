{{-- Dipakai oleh semua halaman Reports. Perlu variabel: $indexRoute, $exportRoute, $from, $to --}}
<form action="{{ route($indexRoute) }}" method="GET" class="d-flex gap-2 flex-wrap align-items-center">
    <input type="date" name="from" value="{{ $from->toDateString() }}" class="form-control form-control-sm" style="max-width:160px;">
    <span class="text-muted small">s/d</span>
    <input type="date" name="to" value="{{ $to->toDateString() }}" class="form-control form-control-sm" style="max-width:160px;">
    <button type="submit" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-funnel"></i> Terapkan
    </button>
    <a href="{{ route($exportRoute, ['from' => $from->toDateString(), 'to' => $to->toDateString()]) }}" class="btn btn-admin-primary btn-sm">
        <i class="bi bi-download"></i> Export CSV
    </a>
</form>
