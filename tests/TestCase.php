<?php

namespace Zhandos717\MoonshineMonitoring\Tests;

use Illuminate\Foundation\Application;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Zhandos717\MoonshineMonitoring\MonitoringServiceProvider;

class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app)
    {
        return [
            MonitoringServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
        
        // Setup monitoring config
        $app['config']->set('monitoring', [
            'auto_menu' => true,
            'instance_name' => 'test-instance',
            'notifications' => [
                'telegram' => ''
            ],
            'migrations' => true,
            'purge_before' => '-1 day',
        ]);
        
        // MoonShine configuration
        $app['config']->set('moonshine', [
            'dir' => 'app/MoonShine',
            'namespace' => 'App\MoonShine',
            'route' => [
                'prefix' => 'admin',
                'middleware' => ['web'],
            ],
            'auth' => [
                'enable' => true,
                'middleware' => 'auth',
            ],
            'locales' => ['en', 'ru'],
        ]);
    }
    
    protected function defineEnvironment($app)
    {
        // Define any additional environment setup here
        parent::defineEnvironment($app);
    }
    
    protected function defineDatabaseMigrations()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }
}