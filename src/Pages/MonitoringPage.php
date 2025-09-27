<?php

namespace Zhandos717\MoonshineMonitoring\Pages;

use MoonShine\Apexcharts\Components\LineChartMetric;
use MoonShine\Laravel\Pages\Page;
use MoonShine\Support\Attributes\Icon;
use MoonShine\UI\Components\Layout\Column;
use MoonShine\UI\Components\Layout\Grid;
use MoonShine\UI\Components\Metrics\Wrapped\ValueMetric;
use Zhandos717\MoonshineMonitoring\Components\MonitoringComponent;
use Zhandos717\MoonshineMonitoring\Facades\Monitoring;
use Zhandos717\MoonshineMonitoring\Models\MonitoringRecord;

#[Icon('s.cpu-chip')]
class MonitoringPage extends Page
{
    public function getTitle(): string
    {
        return $this->title ?: __('moonshine-monitoring::ui.monitoring');
    }

    public function getBreadcrumbs(): array
    {
        return [
            '#' => $this->getTitle(),
        ];
    }


    public function components(): array
    {
        return [
            MonitoringComponent::make(__('moonshine-monitoring::ui.monitoring')),
        ];
    }
}
