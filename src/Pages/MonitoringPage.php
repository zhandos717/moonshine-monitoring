<?php

namespace Zhandos717\MoonshineMonitoring\Pages;

use MoonShine\Attributes\Icon;
use MoonShine\Pages\Page;
use Zhandos717\MoonshineMonitoring\Components\MonitoringComponent;

#[Icon('heroicons.outline.cpu-chip')]
class MonitoringPage extends Page
{
    public function title(): string
    {
        return __('moonshine-monitoring::monitoring.monitoring');
    }

    public function breadcrumbs(): array
    {
        return [
            '#' => $this->title(),
        ];
    }

    public function components(): array
    {
        return [
            MonitoringComponent::make(),
        ];
    }
}
