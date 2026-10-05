<?php

namespace Zhandos717\MoonshineMonitoring\Components;

use MoonShine\UI\Components\MoonShineComponent;
use Throwable;
use Zhandos717\MoonshineMonitoring\Facades\Monitoring;
use Zhandos717\MoonshineMonitoring\Pages\MonitoringPage;

/**
 * Плитки ЦП, памяти и диска для главной страницы MoonShine:
 * MonitoringWidget::make() в components() дашборда.
 */
class MonitoringWidget extends MoonShineComponent
{
    protected string $view = 'moonshine-monitoring::widget';

    public function viewData(): array
    {
        try {
            $cpu = Monitoring::cpu();
            $memory = Monitoring::memory();
            $disk = Monitoring::disk();
            $current = [
                'cpu' => (float) $cpu->getUsage(),
                'memory' => (float) $memory->getUsage(),
                'disk' => (float) $disk->getUsage(),
                'cpu_cores' => $cpu->getCores(),
                'memory_total_bytes' => $memory->getTotalBytes(),
                'disk_total_bytes' => $disk->getTotalBytes(),
                'at' => null,
            ];
        } catch (Throwable) {
            $current = null;
        }

        return [
            'current' => $current,
            'thresholds' => config('monitoring.thresholds', ['warning' => 85, 'critical' => 95]),
            'pageUrl' => $this->pageUrl(),
        ];
    }

    private function pageUrl(): ?string
    {
        try {
            return app(MonitoringPage::class)->getUrl();
        } catch (Throwable) {
            return null;
        }
    }
}
