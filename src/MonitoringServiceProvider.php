<?php

namespace Zhandos717\MoonshineMonitoring;


use Illuminate\Support\ServiceProvider;
use Zhandos717\MoonshineMonitoring\Commands\RecordCommand;
use Zhandos717\MoonshineMonitoring\Pages\MonitoringPage;
use Zhandos717\MoonshineMonitoring\System\Monitoring;

use MoonShine\Contracts\Core\DependencyInjection\CoreContract;
use MoonShine\Contracts\MenuManager\MenuManagerContract;
use MoonShine\MenuManager\MenuItem;


class MonitoringServiceProvider extends ServiceProvider
{

    public function register(): void
    {
        $this->app->bind('monitoring', function () {
            return new Monitoring();
        });
    }

    private function registerCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                RecordCommand::class,
            ]);
        }
    }

    public function boot(CoreContract $core, MenuManagerContract $menu): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'moonshine-monitoring');
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'moonshine-monitoring');
        $this->mergeConfigFrom(__DIR__.'/../config/monitoring.php', 'moonshine.monitoring');

        $this->registerCommands();

        $core->pages([MonitoringPage::class]);

        if (config('moonshine.monitoring.auto_menu')) {
            $menu->add([
                MenuItem::make(
                    __('Log viewer'),
                    MonitoringPage::class,
                ),
            ]);
        }
    }
}
