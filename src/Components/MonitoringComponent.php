<?php

namespace Zhandos717\MoonshineMonitoring\Components;

use Closure;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use MoonShine\UI\Components\MoonShineComponent;
use MoonShine\UI\Traits\Components\WithColumnSpan;
use MoonShine\UI\Traits\WithIcon;
use MoonShine\UI\Traits\WithLabel;
use Throwable;
use Zhandos717\MoonshineMonitoring\Facades\Monitoring;
use Zhandos717\MoonshineMonitoring\Models\MonitoringRecord;
use Zhandos717\MoonshineMonitoring\Support\MemorySpikeDetector;

class MonitoringComponent extends MoonShineComponent
{
    public const RANGES = ['1h' => 60, '24h' => 1440, '7d' => 10080];

    // Больше точек на графике шириной ~1000px глаз не различает
    private const MAX_POINTS = 240;

    protected string $view = 'moonshine-monitoring::monitoring';

    use WithColumnSpan;
    use WithLabel;
    use WithIcon;

    final public function __construct(Closure|string $label)
    {
        parent::__construct();

        $this->setLabel($label);
    }

    public function viewData(): array
    {
        $range = array_key_exists((string) request('range'), self::RANGES) ? (string) request('range') : '24h';

        try {
            $current = $this->current();
            $history = $this->history(self::RANGES[$range]);
        } catch (Throwable) {
            $current = null;
            $history = collect();
        }

        $thresholds = config('monitoring.thresholds', ['warning' => 85, 'critical' => 95]);
        $detector = new MemorySpikeDetector(
            minRise: (float) config('monitoring.memory_spikes.min_rise', 8),
            warning: (float) $thresholds['warning'],
            window: (int) config('monitoring.memory_spikes.window', 15),
        );
        $memoryPoints = $history->filter(fn (array $p) => $p['memory'] !== null)
            ->map(fn (array $p) => ['t' => $p['t'], 'value' => $p['memory']])
            ->values()->all();

        $spikes = collect($detector->detect($memoryPoints))
            ->map(fn (array $s) => $s + ['peak_bytes' => $this->bytesAt($history, $s['peak_at'], $s['peak'])])
            ->sortByDesc('peak')->values();

        return [
            'current' => $current,
            'history' => $history->values(),
            'spikes' => $spikes,
            'range' => $range,
            'ranges' => array_keys(self::RANGES),
            'thresholds' => $thresholds,
            'recent' => $history->reverse()->take(10)->values(),
        ];
    }

    /**
     * @return array{cpu: float, memory: float, disk: float, cpu_cores: ?int, memory_total_bytes: ?int, disk_total_bytes: ?int}
     */
    private function current(): array
    {
        $cpu = Monitoring::cpu();
        $memory = Monitoring::memory();
        $disk = Monitoring::disk();

        return [
            'cpu' => (float) $cpu->getUsage(),
            'memory' => (float) $memory->getUsage(),
            'disk' => (float) $disk->getUsage(),
            'cpu_cores' => $cpu->getCores(),
            'memory_total_bytes' => $memory->getTotalBytes(),
            'disk_total_bytes' => $disk->getTotalBytes(),
        ];
    }

    /**
     * Записи текущего инстанса за период, сжатые в корзины: память — максимум (чтобы не терять пики),
     * CPU и диск — среднее.
     *
     * @return Collection<int, array{t: string, cpu: ?float, memory: ?float, disk: ?float, memory_total_bytes: ?int}>
     */
    private function history(int $minutes): Collection
    {
        $instance = str_replace(' ', '', (string) config('monitoring.instance_name'));
        $records = MonitoringRecord::query()
            ->where('instance_name', $instance)
            ->where('created_at', '>=', now()->subMinutes($minutes))
            ->orderBy('created_at')
            ->get(['cpu', 'memory', 'disk', 'memory_total_bytes', 'created_at']);

        if ($records->isEmpty()) {
            return collect();
        }

        $bucketSeconds = max(60, (int) ceil($minutes * 60 / self::MAX_POINTS));

        return $records
            ->groupBy(fn (MonitoringRecord $r) => intdiv($r->created_at->getTimestamp(), $bucketSeconds))
            ->map(function (Collection $bucket) {
                $peak = $bucket->sortByDesc('memory')->first();

                return [
                    't' => Carbon::parse($bucket->first()->created_at)->format('Y-m-d H:i'),
                    'cpu' => $this->avg($bucket, 'cpu'),
                    'memory' => $peak->memory !== null ? round((float) $peak->memory, 2) : null,
                    'disk' => $this->avg($bucket, 'disk'),
                    'memory_total_bytes' => $peak->memory_total_bytes,
                ];
            })
            ->values();
    }

    private function avg(Collection $bucket, string $key): ?float
    {
        $values = $bucket->pluck($key)->filter(fn ($v) => $v !== null);

        return $values->isEmpty() ? null : round((float) $values->avg(), 2);
    }

    private function bytesAt(Collection $history, string $time, float $percent): ?int
    {
        $total = $history->firstWhere('t', $time)['memory_total_bytes'] ?? null;

        return $total ? (int) ($total * $percent / 100) : null;
    }
}
