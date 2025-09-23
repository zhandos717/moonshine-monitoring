<?php

namespace Zhandos717\MoonshineMonitoring\Tests\Unit;

use Zhandos717\MoonshineMonitoring\Actions\RecordUsage;
use Zhandos717\MoonshineMonitoring\Models\MonitoringRecord;
use Zhandos717\MoonshineMonitoring\Tests\TestCase;

class RecordUsageTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Run migrations
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
    }

    /** @test */
    public function it_can_record_resource_usage()
    {
        $action = new RecordUsage();
        
        $record = $action->record([
            'cpu' => 45.5,
            'memory' => 60.2,
            'disk' => 75.8,
        ]);

        $this->assertInstanceOf(MonitoringRecord::class, $record);
        $this->assertDatabaseHas('monitoring_records', [
            'instance_name' => 'test-instance',
            'cpu' => 45.5,
            'memory' => 60.2,
            'disk' => 75.8,
        ]);
    }

    /** @test */
    public function it_handles_missing_resource_values()
    {
        $action = new RecordUsage();
        
        $record = $action->record([
            'cpu' => 45.5,
            // memory and disk are missing
        ]);

        $this->assertInstanceOf(MonitoringRecord::class, $record);
        $this->assertDatabaseHas('monitoring_records', [
            'instance_name' => 'test-instance',
            'cpu' => 45.5,
            'memory' => null,
            'disk' => null,
        ]);
    }
}