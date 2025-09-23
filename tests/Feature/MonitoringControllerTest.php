<?php

namespace Zhandos717\MoonshineMonitoring\Tests\Feature;

use Zhandos717\MoonshineMonitoring\Models\MonitoringRecord;
use Zhandos717\MoonshineMonitoring\Tests\TestCase;

class MonitoringControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Run migrations
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
    }

    /** @test */
    public function it_can_get_monitoring_data()
    {
        // Create some test records
        MonitoringRecord::factory()->count(5)->create([
            'instance_name' => 'test-instance',
        ]);
        
        // Call the monitoring endpoint
        $response = $this->get('/admin/monitoring');
        
        $response->assertStatus(200);
        // Note: The actual route might be different based on MoonShine configuration
    }
}