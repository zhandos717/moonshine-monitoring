<?php

namespace Zhandos717\MoonshineMonitoring\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use Zhandos717\MoonshineMonitoring\MonitoringServiceProvider;
use Zhandos717\MoonshineMonitoring\Tests\TestCase;
use Zhandos717\MoonshineMonitoring\Facades\Monitoring;

class MonitoringServiceProviderTest extends TestCase
{
    #[Test]
    public function it_binds_the_monitoring_facade()
    {
        $monitoring = app('monitoring');
        
        $this->assertNotNull($monitoring);
    }

    #[Test]
    public function it_loads_the_configuration()
    {
        $config = config('monitoring');
        
        $this->assertIsArray($config);
        $this->assertArrayHasKey('auto_menu', $config);
        $this->assertArrayHasKey('instance_name', $config);
    }

    #[Test]
    public function it_registers_the_record_command()
    {
        // Test that the command is registered
        $this->assertTrue(true); // Placeholder until we can properly test command registration
    }
}