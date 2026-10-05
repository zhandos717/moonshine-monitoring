<?php

namespace Zhandos717\MoonshineMonitoring\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use Zhandos717\MoonshineMonitoring\Commands\RecordCommand;
use Zhandos717\MoonshineMonitoring\Models\MonitoringRecord;
use Zhandos717\MoonshineMonitoring\Tests\TestCase;
use Illuminate\Support\Facades\Artisan;

class RecordCommandTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Run migrations
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
    }

    #[Test]
    public function it_can_execute_the_record_command()
    {
        
        // Execute the command
        $this->artisan('moonshine-monitoring:record')
             ->expectsOutput('Resource usage recorded')
             ->assertExitCode(0);
        
        // Assert that a record was created
        $this->assertDatabaseHas('monitoring_records', [
            'instance_name' => 'test-instance',
        ]);
    }
    
}