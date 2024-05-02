<?php

namespace Zhandos717\MoonshineMonitoring\Pages;

use MoonShine\Attributes\Icon;
use MoonShine\Pages\Page;

#[Icon('heroicons.outline.circle-stack')]
class MonitoringPage extends Page
{
    public function title(): string
    {
        return __('Monitoring');
    }

    public function breadcrumbs(): array
    {
        return [
            '#' => $this->title(),
        ];
    }

    public function components(): array
    {
        // TODO: Implement components() method.
    }
}
