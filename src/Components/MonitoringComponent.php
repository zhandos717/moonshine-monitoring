<?php

namespace Zhandos717\MoonshineMonitoring\Components;

use Illuminate\Support\Str;
use MoonShine\Components\MoonShineComponent;
use Zhandos717\MoonshineMonitoring\Facades\Monitoring;

class MonitoringComponent extends MoonShineComponent
{
    protected string $view = 'moonshine-monitoring::default';

    public function __construct()
    {
    }

    protected function viewData(): array
    {
        $viewer = Str::of(PHP_OS)->lower();

        dd(
            [
                'cpu'    => Monitoring::cpu()->usage(),
                'memory' => Monitoring::memory()->usage(),
                'disk'   => Monitoring::disk()->usage(),
            ]
        );


        return [];
    }
}
