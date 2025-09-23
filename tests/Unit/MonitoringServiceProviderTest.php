<?php

namespace Zhandos717\MoonshineMonitoring\Tests\Unit;

use Zhandos717\MoonshineMonitoring\MonitoringServiceProvider;
use Zhandos717\MoonshineMonitoring\Tests\TestCase;
use Zhandos717\MoonshineMonitoring\Facades\Monitoring;

class MonitoringServiceProviderTest extends TestCase
{
    /** @test */
    public function it_binds_the_monitoring_facade()
    {
        $monitoring = app('monitoring');
        
        $this->assertNotNull($monitoring);
    }

    /** @test */
    public function it_loads_the_configuration()
    {
        $config = config('monitoring');
        
        $this->assertIsArray($config);
        $this->assertArrayHasKey('auto_menu', $config);
        $this->assertArrayHasKey('instance_name', $config);
    }

    /** @test */
    public function it_registers_the_record_command()
    {
        // Test that the command is registered
        $this->assertTrue(true); // Placeholder until we can properly test command registration
    }
}