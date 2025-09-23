<?php

namespace Zhandos717\MoonshineMonitoring\Tests\Unit;

use Zhandos717\MoonshineMonitoring\Tests\TestCase;

class ConfigurationTest extends TestCase
{
    /** @test */
    public function it_has_default_configuration_values()
    {
        $config = include __DIR__ . '/../../config/monitoring.php';
        
        $this->assertArrayHasKey('auto_menu', $config);
        $this->assertArrayHasKey('instance_name', $config);
        $this->assertArrayHasKey('notifications', $config);
        $this->assertArrayHasKey('migrations', $config);
        $this->assertArrayHasKey('purge_before', $config);
        
        $this->assertTrue($config['auto_menu']);
        $this->assertEquals(env('MONITORING_INSTANCE_NAME', env('APP_NAME')), $config['instance_name']);
        $this->assertTrue($config['migrations']);
    }
}