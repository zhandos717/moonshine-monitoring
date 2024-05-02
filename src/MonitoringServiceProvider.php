<?php

namespace Zhandos717\MoonshineMonitoring;


use Illuminate\Support\ServiceProvider;
use MoonShine\Menu\MenuItem;
use MoonShine\MoonShine;
use Zhandos717\MoonshineMonitoring\Commands\RecordCommand;
use Zhandos717\MoonshineMonitoring\Pages\MonitoringPage;
use Zhandos717\MoonshineMonitoring\System\Monitoring;

class MonitoringServiceProvider extends ServiceProvider
{

    public function register(): void
    {
        // facade
        $this->app->bind('monitoring', function () {
            return new Monitoring();
        });
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'moonshine-monitoring');
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'moonshine-monitoring');
        $this->mergeConfigFrom(__DIR__ . '/../config/monitoring.php', 'moonshine.monitoring');
        $this->loadMigrations();
        $this->registerCommands();

        moonshine()
            ->pages([
                new MonitoringPage(),
            ])
            ->when(
                config('moonshine.monitoring.auto_menu'),
                fn(MoonShine $moonshine) => $moonshine->
                vendorsMenu([
                    MenuItem::make(
                        static fn() => __('moonshine-monitoring::monitoring.monitoring'),
                        new MonitoringPage(),
                    ),
                ])
            );
    }

    private function registerCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                RecordCommand::class,
            ]);
        }
    }

    private function loadMigrations()
    {
        if (config('monitoring.migrations', true) && $this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__ . '/../database/migrations/');
        }
    }
}
