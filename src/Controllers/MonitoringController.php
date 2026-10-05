<?php

namespace Zhandos717\MoonshineMonitoring\Controllers;

use Illuminate\Http\JsonResponse;
use MoonShine\Laravel\Http\Controllers\MoonShineController;
use Zhandos717\MoonshineMonitoring\Facades\Monitoring;
use Zhandos717\MoonshineMonitoring\Models\MonitoringRecord;
use Zhandos717\MoonshineMonitoring\Support\Instance;

class MonitoringController extends MoonShineController
{
    public function data(): JsonResponse
    {
        $instance = Instance::current();
        $cpu = Monitoring::cpu();
        $memory = Monitoring::memory();
        $disk = Monitoring::disk();

        $records = MonitoringRecord::query()
            ->where('instance_name', $instance)
            ->latest()
            ->limit(100)
            ->get();

        return response()->json([
            'status' => 'success',
            'current' => [
                'instance_name' => $instance,
                'cpu' => $cpu->getUsage(),
                'memory' => $memory->getUsage(),
                'disk' => $disk->getUsage(),
                'cpu_cores' => $cpu->getCores(),
                'memory_total_bytes' => $memory->getTotalBytes(),
                'disk_total_bytes' => $disk->getTotalBytes(),
                'created_at' => now()->toDateTimeString(),
            ],
            'records' => $records,
        ]);
    }
}
