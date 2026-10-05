<?php

namespace Zhandos717\MoonshineMonitoring\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use Zhandos717\MoonshineMonitoring\Tests\TestCase;

class ConfigurationTest extends TestCase
{
    #[Test]
    public function it_has_default_configuration_values()
    {
        $config = include __DIR__ . '/../../config/monitoring.php';
        
        $this->assertArrayHasKey('auto_menu', $config);
        $this->assertArrayHasKey('instance_name', $config);
        $this->assertArrayHasKey('migrations', $config);
        $this->assertArrayHasKey('purge_before', $config);
        
        $this->assertTrue($config['auto_menu']);
        $this->assertEquals(env('APP_NAME', 'Default Instance'), $config['instance_name']);
        $this->assertTrue($config['migrations']);
    }
}