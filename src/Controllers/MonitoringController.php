<?php

namespace Zhandos717\MoonshineMonitoring\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Config;
use Illuminate\View\View;
use MoonShine\Laravel\Http\Controllers\MoonShineController;
use MoonShine\Laravel\MoonShineRequest;
use Zhandos717\MoonshineMonitoring\Facades\Monitoring;
use Zhandos717\MoonshineMonitoring\Models\MonitoringRecord;

class MonitoringController extends MoonShineController
{
    /**
     * @throws Exception
     */
    public function index(MoonShineRequest $request): View|JsonResponse
    {
        // Если это AJAX запрос, возвращаем JSON данные
        if ($request->ajax()) {
            return $this->getData();
        }
        
        // Для обычного запроса возвращаем представление
        return view('moonshine-monitoring::monitoring');
    }
    
    /**
     * Get monitoring data as JSON
     *
     * @throws Exception
     */
    public function data(): JsonResponse
    {
        return $this->getData();
    }
    
    /**
     * Get monitoring data
     *
     * @throws Exception
     */
    private function getData(): JsonResponse
    {
        // Get recent monitoring records
        $records = MonitoringRecord::query()
            ->orderBy('created_at', 'desc')
            ->limit(100)->get();

        // Get current resource usage for the latest record
        $cpuResource = Monitoring::cpu();
        $memoryResource = Monitoring::memory();
        $diskResource = Monitoring::disk();
        
        // Add current data to the beginning of records
        $currentData = [
            'instance_name' => config('monitoring.instance_name', gethostname()),
            'cpu' => $cpuResource->getUsage(),
            'memory' => $memoryResource->getUsage(),
            'disk' => $diskResource->getUsage(),
            'cpu_cores' => $cpuResource->getCores(),
            'memory_total_bytes' => $memoryResource->getTotalBytes(),
            'disk_total_bytes' => $diskResource->getTotalBytes(),
            'created_at' => now()->format('Y-m-d H:i:s')
        ];
        
        // Prepend current data to records
        $allRecords = collect([$currentData])->merge($records);

        return response()->json([
            'records' => $allRecords,
            'status'  => 'success',
        ]);
    }
}
