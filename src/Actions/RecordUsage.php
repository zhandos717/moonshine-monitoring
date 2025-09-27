<?php

namespace Zhandos717\MoonshineMonitoring\Actions;

use Zhandos717\MoonshineMonitoring\Facades\Monitoring;
use Zhandos717\MoonshineMonitoring\Models\MonitoringRecord;

class RecordUsage
{
    public function record(array $resources): MonitoringRecord
    {
        // Get additional resource information
        $cpuResource = Monitoring::cpu();
        $memoryResource = Monitoring::memory();
        $diskResource = Monitoring::disk();
        
        return MonitoringRecord::create(
            [
                'instance_name' => str_replace(' ', '', config('monitoring.instance_name')),
                'cpu' => $resources['cpu'] ?? null,
                'memory' => $resources['memory'] ?? null,
                'disk' => $resources['disk'] ?? null,
                'cpu_cores' => $cpuResource->getCores(),
                'memory_total_bytes' => $memoryResource->getTotalBytes(),
                'disk_total_bytes' => $diskResource->getTotalBytes(),
            ]
        );
    }

}