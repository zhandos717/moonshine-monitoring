<?php

namespace Zhandos717\MoonshineMonitoring\Tests;

use Zhandos717\MoonshineMonitoring\Models\MonitoringRecord;

class MonitoringRecordTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Run migrations
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }

    /** @test */
    public function it_can_create_a_monitoring_record()
    {
        $record = MonitoringRecord::create([
            'instance_name' => 'test-instance',
            'cpu' => 45.5,
            'memory' => 60.2,
            'disk' => 75.8,
        ]);

        $this->assertDatabaseHas('monitoring_records', [
            'instance_name' => 'test-instance',
            'cpu' => 45.5,
            'memory' => 60.2,
            'disk' => 75.8,
        ]);

        $this->assertInstanceOf(MonitoringRecord::class, $record);
        $this->assertEquals('test-instance', $record->instance_name);
        $this->assertEquals(45.5, $record->cpu);
        $this->assertEquals(60.2, $record->memory);
        $this->assertEquals(75.8, $record->disk);
    }

    /** @test */
    public function it_casts_values_to_correct_types()
    {
        $record = MonitoringRecord::create([
            'instance_name' => 'test-instance',
            'cpu' => '45.5',
            'memory' => '60.2',
            'disk' => '75.8',
        ]);

        $this->assertIsFloat($record->cpu);
        $this->assertIsFloat($record->memory);
        $this->assertIsFloat($record->disk);
    }
}