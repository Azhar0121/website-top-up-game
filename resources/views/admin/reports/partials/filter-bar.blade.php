<form action="{{ route($indexRoute) }}" method="GET" class="d-flex gap-2 flex-wrap align-items-center">
    @isset($granularity)
        <select name="granularity" class="form-select form-select-sm" style="max-width:150px;">
            <option value="daily" {{ $granularity === 'daily' ? 'selected' : '' }}>Harian</option>
            <option value="weekly" {{ $granularity === 'weekly' ? 'selected' : '' }}>Mingguan</option>
            <option value="monthly" {{ $granularity === 'monthly' ? 'selected' : '' }}>Bulanan</option>
            <option value="yearly" {{ $granularity === 'yearly' ? 'selected' : '' }}>Tahunan</option>
        </select>
    @endisset
    <input type="date" name="from" value="{{ $from->toDateString() }}" class="form-control form-control-sm" style="max-width:160px;">
    <span class="text-muted small">s/d</span>
    <input type="date" name="to" value="{{ $to->toDateString() }}" class="form-control form-control-sm" style="max-width:160px;">
    <button type="submit" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-funnel"></i> Terapkan
    </button>
    <a href="{{ route($exportRoute, array_filter(['from' => $from->toDateString(), 'to' => $to->toDateString(), 'granularity' => $granularity ?? null])) }}" class="btn btn-admin-primary btn-sm">
        <i class="bi bi-download"></i> Export CSV
    </a>
</form>
