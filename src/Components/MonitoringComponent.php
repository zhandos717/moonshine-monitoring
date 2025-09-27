<?php

namespace Zhandos717\MoonshineMonitoring\Components;

use Closure;
use Illuminate\Support\Facades\Config;
use MoonShine\UI\Components\MoonShineComponent;
use MoonShine\UI\Traits\Components\WithColumnSpan;
use MoonShine\UI\Traits\WithIcon;
use MoonShine\UI\Traits\WithLabel;
use Zhandos717\MoonshineMonitoring\Facades\Monitoring;
use Zhandos717\MoonshineMonitoring\Models\MonitoringRecord;

class MonitoringComponent extends MoonShineComponent
{
    protected string $view = 'moonshine-monitoring::monitoring';

    use WithColumnSpan;
    use WithLabel;
    use WithIcon;

    final public function __construct(Closure|string $label)
    {
        parent::__construct();

        $this->setLabel($label);
    }

    public function viewData(): array
    {
        try {
            // Get current resource usage
            $cpuResource = Monitoring::cpu();
            $memoryResource = Monitoring::memory();
            $diskResource = Monitoring::disk();
            
            $cpu = $cpuResource->getUsage();
            $memory = $memoryResource->getUsage();
            $disk = $diskResource->getUsage();
            
            // Get additional resource information
            $cpuCores = $cpuResource->getCores();
            $memoryTotalBytes = $memoryResource->getTotalBytes();
            $diskTotalBytes = $diskResource->getTotalBytes();

            // Get historical data for charts
            $records = MonitoringRecord::orderBy('created_at', 'desc')->limit(20)->get();

            // Add current data as the first record
            $currentRecord = new \stdClass();
            $currentRecord->instance_name = config('monitoring.instance_name', gethostname());
            $currentRecord->cpu = $cpu;
            $currentRecord->memory = $memory;
            $currentRecord->disk = $disk;
            $currentRecord->cpu_cores = $cpuCores;
            $currentRecord->memory_total_bytes = $memoryTotalBytes;
            $currentRecord->disk_total_bytes = $diskTotalBytes;
            $currentRecord->created_at = now()->format('Y-m-d H:i:s');
            
            // Prepend current data to records
            $allRecords = collect([$currentRecord])->merge($records);

            return [
                'records' => $allRecords,
            ];
        } catch (\Exception $e) {
            // Return empty array if there's an error
            return [
                'records' => collect([]),
            ];
        }
    }
}
