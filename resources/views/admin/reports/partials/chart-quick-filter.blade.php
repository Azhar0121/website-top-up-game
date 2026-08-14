@php
    $today = \Illuminate\Support\Carbon::today();
    $quickPresets = [
        'hourly' => ['label' => 'Hari Ini', 'from' => $today->toDateString(), 'to' => $today->toDateString()],
        'daily' => ['label' => 'Harian', 'from' => $today->copy()->subDays(29)->toDateString(), 'to' => $today->toDateString()],
        'weekly' => ['label' => 'Mingguan', 'from' => $today->copy()->subWeeks(11)->startOfWeek()->toDateString(), 'to' => $today->toDateString()],
        'yearly' => ['label' => 'Tahunan', 'from' => $today->copy()->subYears(4)->startOfYear()->toDateString(), 'to' => $today->toDateString()],
    ];
@endphp
<select class="form-select form-select-sm" style="max-width:150px;" onchange="window.location.href = this.value">
    @foreach ($quickPresets as $key => $preset)
        <option value="{{ route($indexRoute, ['granularity' => $key, 'from' => $preset['from'], 'to' => $preset['to']]) }}"
                {{ $granularity === $key ? 'selected' : '' }}>{{ $preset['label'] }}</option>
    @endforeach
</select>
