<?php

namespace Zhandos717\MoonshineMonitoring\Alerts;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification;
use Zhandos717\MoonshineMonitoring\Models\MonitoringRecord;

/**
 * Шлёт уведомление, когда метрика держится выше порога alerts.minutes минут подряд.
 * Повтор по той же метрике — не раньше чем через alerts.cooldown минут.
 */
class AlertManager
{
    /**
     * @return list<string> метрики, по которым ушло уведомление
     */
    public function check(string $instance): array
    {
        $config = (array) config('monitoring.alerts', []);
        if (empty($config['enabled']) || !$this->routes($config)) {
            return [];
        }

        $minutes = max(1, (int) ($config['minutes'] ?? 5));
        $records = MonitoringRecord::query()
            ->where('instance_name', $instance)
            ->where('created_at', '>=', now()->subMinutes($minutes))
            ->orderBy('created_at')
            ->get();

        // Один замер не доказывает, что нагрузка держится
        if ($records->count() < 2 || $records->first()->created_at->diffInMinutes(now()) < $minutes - 1) {
            return [];
        }

        $sent = [];
        foreach ((array) ($config['thresholds'] ?? []) as $metric => $threshold) {
            $values = $records->pluck($metric)->filter(fn ($v) => $v !== null);
            if ($values->isEmpty() || $values->min() < (float) $threshold) {
                continue;
            }

            $key = "moonshine-monitoring:alert:{$instance}:{$metric}";
            if (!Cache::add($key, true, now()->addMinutes((int) ($config['cooldown'] ?? 60)))) {
                continue;
            }

            Notification::routes($this->routes($config))->notify(new ResourceAlert(
                instance: $instance,
                metric: (string) $metric,
                current: (float) $values->last(),
                peak: (float) $values->max(),
                threshold: (float) $threshold,
                minutes: $minutes,
            ));
            $sent[] = (string) $metric;
        }

        return $sent;
    }

    /**
     * @param array<string, mixed> $config
     * @return array<string, mixed>
     */
    private function routes(array $config): array
    {
        $routes = [];
        if (!empty($config['mail'])) {
            $routes['mail'] = $config['mail'];
        }
        if (!empty($config['telegram']['bot_token']) && !empty($config['telegram']['chat_id'])) {
            $routes[TelegramChannel::class] = $config['telegram']['chat_id'];
        }

        return $routes;
    }
}
