<?php

namespace Zhandos717\MoonshineMonitoring\Tests\Unit;

use Zhandos717\MoonshineMonitoring\Facades\Monitoring;
use Zhandos717\MoonshineMonitoring\System\CPU;
use Zhandos717\MoonshineMonitoring\System\Memory;
use Zhandos717\MoonshineMonitoring\System\Disk;
use Zhandos717\MoonshineMonitoring\Tests\TestCase;

class MonitoringFacadeTest extends TestCase
{
    /** @test */
    public function it_can_get_cpu_resource()
    {
        $cpu = Monitoring::cpu();
        
        $this->assertInstanceOf(CPU::class, $cpu);
    }

    /** @test */
    public function it_can_get_memory_resource()
    {
        $memory = Monitoring::memory();
        
        $this->assertInstanceOf(Memory::class, $memory);
    }

    /** @test */
    public function it_can_get_disk_resource()
    {
        $disk = Monitoring::disk();
        
        $this->assertInstanceOf(Disk::class, $disk);
    }
}