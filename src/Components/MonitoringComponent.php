<?php

namespace Zhandos717\MoonshineMonitoring\Components;

use MoonShine\Components\MoonShineComponent;

class MonitoringComponent extends MoonShineComponent
{
    protected string $view = 'moonshine-log-viewer::default';

    public function __construct()
    {
        //
    }

    protected function viewData(): array
    {
        return [];
    }
}
