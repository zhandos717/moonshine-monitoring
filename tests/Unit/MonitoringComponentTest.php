<?php

namespace Zhandos717\MoonshineMonitoring\Tests\Unit;

use Zhandos717\MoonshineMonitoring\Components\MonitoringComponent;
use Zhandos717\MoonshineMonitoring\Models\MonitoringRecord;
use Zhandos717\MoonshineMonitoring\Tests\TestCase;

class MonitoringComponentTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Run migrations
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
    }

    /** @test */
    public function it_can_be_instantiated()
    {
        $component = new MonitoringComponent();
        
        $this->assertInstanceOf(MonitoringComponent::class, $component);
    }

    /** @test */
    public function it_returns_view_data()
    {
        // Create some test records
        MonitoringRecord::factory()->count(3)->create();
        
        $component = new MonitoringComponent();
        $viewData = $component->viewData();
        
        $this->assertIsArray($viewData);
        // Note: Since we're not actually executing shell commands in tests,
        // the CPU, memory, and disk values will be null
    }
}