<?php

namespace Zhandos717\MoonshineMonitoring\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use Zhandos717\MoonshineMonitoring\Components\MonitoringComponent;
use Zhandos717\MoonshineMonitoring\Components\MonitoringWidget;
use Zhandos717\MoonshineMonitoring\Facades\Monitoring;
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

    #[Test]
    public function it_can_be_instantiated()
    {
        $component = new MonitoringComponent('Test Label');
        
        $this->assertInstanceOf(MonitoringComponent::class, $component);
    }

    #[Test]
    public function it_returns_view_data()
    {
        // Create some test records
        MonitoringRecord::factory()->count(3)->create();
        
        $component = new MonitoringComponent('Test Label');
        $viewData = $component->viewData();
        
        $this->assertIsArray($viewData);
        // Note: Since we're not actually executing shell commands in tests,
        // the CPU, memory, and disk values will be null
    }

    #[Test]
    public function it_detects_memory_spike_and_keeps_peak_in_7_day_buckets()
    {
        $start = now()->subHours(3)->startOfMinute();
        foreach (range(0, 179) as $minute) {
            $memory = in_array($minute, [100, 101, 102], true) ? [70, 92, 75][$minute - 100] : 50;
            MonitoringRecord::factory()->create([
                'instance_name' => 'test-instance',
                'memory' => $memory,
                'created_at' => $start->copy()->addMinutes($minute),
            ]);
        }
        MonitoringRecord::factory()->create(['instance_name' => 'other', 'memory' => 99, 'created_at' => now()]);

        request()->merge(['range' => '7d']);
        $data = (new MonitoringComponent('Test'))->viewData();

        $this->assertSame('7d', $data['range']);
        $this->assertSame(92.0, $data['history']->max('memory'));
        $this->assertCount(1, $data['spikes']);
        $this->assertSame(92.0, $data['spikes'][0]['peak']);
    }

    #[Test]
    public function it_renders_the_dashboard()
    {
        MonitoringRecord::factory()->count(5)->create(['instance_name' => 'test-instance', 'created_at' => now()]);

        $html = (string) (new MonitoringComponent('Test'))->render();

        $this->assertStringContainsString('data-chart="memory"', $html);
        $this->assertStringContainsString('msm-tiles', $html);
    }

    #[Test]
    public function unknown_range_falls_back_to_24_hours()
    {
        request()->merge(['range' => 'year']);

        $this->assertSame('24h', (new MonitoringComponent('Test'))->viewData()['range']);
    }

    #[Test]
    public function it_switches_to_another_server_and_shows_its_last_sample()
    {
        Monitoring::fake(memory: 10);
        MonitoringRecord::factory()->create(['instance_name' => 'test-instance', 'memory' => 40, 'created_at' => now()->subMinute()]);
        MonitoringRecord::factory()->create(['instance_name' => 'worker-2', 'memory' => 77, 'created_at' => now()->subMinute()]);

        request()->merge(['instance' => 'worker-2']);
        $data = (new MonitoringComponent('Test'))->viewData();

        $this->assertSame('worker-2', $data['instance']);
        $this->assertFalse($data['isOwnInstance']);
        $this->assertSame(77.0, $data['current']['memory']);
        $this->assertSame(['test-instance', 'worker-2'], $data['instances']->all());
    }

    #[Test]
    public function unknown_server_falls_back_to_own()
    {
        Monitoring::fake(memory: 10);
        request()->merge(['instance' => 'nope']);

        $data = (new MonitoringComponent('Test'))->viewData();

        $this->assertSame('test-instance', $data['instance']);
        $this->assertSame(10.0, $data['current']['memory']);
    }

    #[Test]
    public function widget_renders_tiles()
    {
        Monitoring::fake(cpu: 12, memory: 96);

        $html = (string) MonitoringWidget::make()->render();

        $this->assertStringContainsString('msm-tiles', $html);
        $this->assertStringContainsString('96.0', $html);
    }
}
