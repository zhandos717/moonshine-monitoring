<?php

namespace Zhandos717\MoonshineMonitoring\Actions;

use Zhandos717\MoonshineMonitoring\Facades\Monitoring;
use Zhandos717\MoonshineMonitoring\Models\MonitoringRecord;
use Zhandos717\MoonshineMonitoring\Support\Instance;

class RecordUsage
{
    /**
     * @param array{cpu?: ?float, memory?: ?float, disk?: ?float} $resources переопределяет измеренные значения
     */
    public function record(array $resources = []): MonitoringRecord
    {
        $cpu = Monitoring::cpu();
        $memory = Monitoring::memory();
        $disk = Monitoring::disk();

        return MonitoringRecord::create([
            'instance_name' => Instance::current(),
            'cpu' => array_key_exists('cpu', $resources) ? $resources['cpu'] : $cpu->getUsage(),
            'memory' => array_key_exists('memory', $resources) ? $resources['memory'] : $memory->getUsage(),
            'disk' => array_key_exists('disk', $resources) ? $resources['disk'] : $disk->getUsage(),
            'cpu_cores' => $cpu->getCores(),
            'memory_total_bytes' => $memory->getTotalBytes(),
            'disk_total_bytes' => $disk->getTotalBytes(),
        ]);
    }
}
