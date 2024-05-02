<?php

namespace Zhandos717\MoonshineMonitoring\Actions;

use Zhandos717\MoonshineMonitoring\Models\MonitoringRecord;

class RecordUsage
{
    public function record(array $resources): MonitoringRecord
    {
        return MonitoringRecord::create(
            [
                'instance_name' => str_replace(' ', '', config('monitoring.instance_name')),
                'cpu'           => $resources['cpu'] ?? null,
                'memory'        => $resources['memory'] ?? null,
                'disk'          => $resources['disk'] ?? null,
            ]
        );
    }

}