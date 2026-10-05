<?php

namespace Zhandos717\MoonshineMonitoring\Support;

/**
 * Находит эпизоды всплесков: подряд идущие замеры, где значение выше базовой линии
 * на min_rise п.п. или выше порога warning. Базовая линия — медиана предыдущих
 * «спокойных» замеров, чтобы длинный всплеск не поднимал её сам себе.
 */
class MemorySpikeDetector
{
    public function __construct(
        private readonly float $minRise = 8.0,
        private readonly float $warning = 85.0,
        private readonly int $window = 15,
    ) {
    }

    /**
     * @param list<array{t: string, value: float}> $points по возрастанию времени
     * @return list<array{start: string, end: string, peak_at: string, peak: float, baseline: float, rise: float, points: int}>
     */
    public function detect(array $points): array
    {
        $calm = [];
        $episodes = [];
        $current = null;

        foreach ($points as $point) {
            $baseline = $this->median(array_slice($calm, -$this->window)) ?? $point['value'];
            $elevated = $point['value'] - $baseline >= $this->minRise || $point['value'] >= $this->warning;

            if (!$elevated) {
                $calm[] = $point['value'];
                if ($current !== null) {
                    $episodes[] = $current;
                    $current = null;
                }
                continue;
            }

            if ($current === null) {
                $current = [
                    'start' => $point['t'], 'end' => $point['t'], 'peak_at' => $point['t'],
                    'peak' => $point['value'], 'baseline' => $baseline, 'points' => 0,
                ];
            }
            $current['end'] = $point['t'];
            $current['points']++;
            if ($point['value'] > $current['peak']) {
                $current['peak'] = $point['value'];
                $current['peak_at'] = $point['t'];
            }
        }

        if ($current !== null) {
            $episodes[] = $current;
        }

        return array_map(static fn (array $e): array => $e + [
            'rise' => round($e['peak'] - $e['baseline'], 2),
        ], $episodes);
    }

    /**
     * @param list<float> $values
     */
    private function median(array $values): ?float
    {
        if ($values === []) {
            return null;
        }
        sort($values);
        $middle = intdiv(count($values), 2);

        return count($values) % 2 ? $values[$middle] : ($values[$middle - 1] + $values[$middle]) / 2;
    }
}
