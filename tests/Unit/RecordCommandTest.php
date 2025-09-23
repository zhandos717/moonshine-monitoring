<?php

namespace Zhandos717\MoonshineMonitoring\Tests\Unit;

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

    /** @test */
    public function it_can_execute_the_record_command()
    {
        // Mock the monitoring facade to return specific values
        $this->mockMonitoringFacade();
        
        // Execute the command
        $this->artisan('moonshine-monitoring:record')
             ->expectsOutput('Record resources usages')
             ->assertExitCode(0);
        
        // Assert that a record was created
        $this->assertDatabaseHas('monitoring_records', [
            'instance_name' => 'test-instance',
        ]);
    }
    
    private function mockMonitoringFacade()
    {
        // We'll create a more comprehensive test by mocking the shell execution
        // For now, we'll test that the command can be instantiated
    }
}