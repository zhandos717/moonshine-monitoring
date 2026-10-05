<?php

namespace Zhandos717\MoonshineMonitoring\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Zhandos717\MoonshineMonitoring\Models\MonitoringRecord;

class SimpleMonitoringRecordTest extends TestCase
{
    #[Test]
    public function it_has_the_correct_fillable_attributes()
    {
        $record = new MonitoringRecord();
        
        $expected = [
            'instance_name',
            'cpu',
            'memory',
            'disk',
            'cpu_cores',
            'memory_total_bytes',
            'disk_total_bytes',
        ];
        
        $this->assertEquals($expected, $record->getFillable());
    }

    #[Test]
    public function it_has_the_correct_casts()
    {
        $record = new MonitoringRecord();
        
        $casts = $record->getCasts();
        
        // Check that our expected casts are present
        $this->assertEquals('float', $casts['cpu']);
        $this->assertEquals('float', $casts['memory']);
        $this->assertEquals('float', $casts['disk']);
    }
}