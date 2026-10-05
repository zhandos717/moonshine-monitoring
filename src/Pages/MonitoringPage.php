<?php

namespace Zhandos717\MoonshineMonitoring\Pages;

use MoonShine\Laravel\Pages\Page;
use MoonShine\Support\Attributes\Icon;
use Zhandos717\MoonshineMonitoring\Components\MonitoringComponent;

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
