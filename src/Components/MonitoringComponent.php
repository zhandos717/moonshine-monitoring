<?php

namespace Zhandos717\MoonshineMonitoring\Components;

use MoonShine\Components\MoonShineComponent;
use Zhandos717\MoonshineMonitoring\Facades\Monitoring;
use Zhandos717\MoonshineMonitoring\Models\MonitoringRecord;

class MonitoringComponent extends MoonShineComponent
{
    protected string $view = 'moonshine-monitoring::default';

    public function __construct()
    {
    }

    public function viewData(): array
    {
        // Get current resource usage
        $cpu = Monitoring::cpu()->getUsage();
        $memory = Monitoring::memory()->getUsage();
        $disk = Monitoring::disk()->getUsage();
        
        // Get historical data for charts
        $records = MonitoringRecord::orderBy('created_at', 'desc')->limit(20)->get();

        return [
            'cpu' => $cpu,
            'memory' => $memory,
            'disk' => $disk,
            'records' => $records
        ];
    }
}
