@php
    $t = fn (string $key, array $replace = []) => __('moonshine-monitoring::ui.' . $key, $replace);
    $bytes = static function (?int $value): string {
        if (!$value) {
            return '—';
        }
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $power = min((int) floor(log($value, 1024)), count($units) - 1);

        return round($value / 1024 ** $power, 1) . ' ' . $units[$power];
    };
    $status = static fn (float $value): string => match (true) {
        $value >= $thresholds['critical'] => 'critical',
        $value >= $thresholds['warning'] => 'warning',
        default => 'ok',
    };
    $statusColor = ['ok' => 'var(--msm-green)', 'warning' => 'var(--msm-orange)', 'critical' => 'var(--msm-red)'];
    $tiles = $current ? [
        [
            'key' => 'cpu',
            'value' => $current['cpu'],
            'sub' => $current['cpu_cores'] ? $current['cpu_cores'] . ' ' . $t($current['cpu_cores'] === 1 ? 'core' : 'cores') : '',
        ],
        [
            'key' => 'memory',
            'value' => $current['memory'],
            'sub' => $current['memory_total_bytes']
                ? $bytes((int) ($current['memory_total_bytes'] * $current['memory'] / 100)) . ' / ' . $bytes($current['memory_total_bytes'])
                : '',
        ],
        [
            'key' => 'disk',
            'value' => $current['disk'],
            'sub' => $current['disk_total_bytes']
                ? $bytes((int) ($current['disk_total_bytes'] * $current['disk'] / 100)) . ' / ' . $bytes($current['disk_total_bytes'])
                : '',
        ],
    ] : [];
@endphp

@if($tiles)
    <div class="msm-tiles">
        @foreach($tiles as $tile)
            @php
                $state = $status($tile['value']);
            @endphp
            <div class="msm-card" style="--c: {{ $statusColor[$state] }}">
                <div class="msm-tile-top">
                    <span>{{ $t($tile['key']) }} · {{ $current['at'] ?? $t('now') }}</span>
                    <span class="msm-status"><i></i>{{ $t('status_' . $state) }}</span>
                </div>
                <div class="msm-value">{{ number_format($tile['value'], 1) }}<small>%</small></div>
                <div class="msm-sub">{{ $tile['sub'] }}</div>
                <div class="msm-bar"><span style="width: {{ min(100, max(0, $tile['value'])) }}%"></span></div>
            </div>
        @endforeach
    </div>
@endif
