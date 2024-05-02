<?php

namespace Zhandos717\MoonshineMonitoring;


use Illuminate\Support\ServiceProvider;
use MoonShine\Menu\MenuItem;
use MoonShine\MoonShine;
use Zhandos717\MoonshineMonitoring\Pages\MonitoringPage;

class MonitoringServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'moonshine-monitoring');
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'moonshine-monitoring');
        $this->mergeConfigFrom(__DIR__ . '/../config/monitoring.php', 'moonshine.monitoring');

        moonshine()
            ->pages([
                new MonitoringPage(),
            ])
            ->when(
                config('moonshine.monitoring.auto_menu'),
                fn(MoonShine $moonshine) => $moonshine->
                vendorsMenu([
                    MenuItem::make(
                        static fn() => __('Log viewer'),
                        new MonitoringPage(),
                    ),
                ])
            );
    }
}
