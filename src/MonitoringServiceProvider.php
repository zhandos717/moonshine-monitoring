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
        $this->mergeConfigFrom(__DIR__.'/../config/monitoring.php', 'monitoring');
        
        // Регистрация миграций
        if ($this->app->runningInConsole() && config('monitoring.migrations', true)) {
            $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        }

        $this->registerCommands();

        $core->pages([MonitoringPage::class]);

        if (config('monitoring.auto_menu')) {
            $menu->add([
                MenuItem::make(
                    __('moonshine-monitoring::ui.monitoring'),
                    MonitoringPage::class,
                ),
            ]);
        }
        
        // Публикация ресурсов
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/monitoring.php' => config_path('monitoring.php'),
            ], 'moonshine-monitoring-config');
            
            $this->publishes([
                __DIR__.'/../database/migrations' => database_path('migrations'),
            ], 'moonshine-monitoring-migrations');
            
            $this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/moonshine-monitoring'),
            ], 'moonshine-monitoring-views');
            
            $this->publishes([
                __DIR__.'/../resources/lang' => resource_path('lang/vendor/moonshine-monitoring'),
            ], 'moonshine-monitoring-lang');
        }
    }
}
