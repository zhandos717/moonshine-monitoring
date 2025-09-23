<?php

namespace Zhandos717\MoonshineMonitoring\Components;

use Closure;
use MoonShine\UI\Components\MoonShineComponent;
use MoonShine\UI\Traits\Components\WithColumnSpan;
use MoonShine\UI\Traits\WithIcon;
use MoonShine\UI\Traits\WithLabel;
use Zhandos717\MoonshineMonitoring\Facades\Monitoring;
use Zhandos717\MoonshineMonitoring\Models\MonitoringRecord;

class MonitoringComponent extends MoonShineComponent
{
    protected string $view = 'moonshine-monitoring::default';

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
        // Get current resource usage
        $cpu = Monitoring::cpu()->getUsage();
        $memory = Monitoring::memory()->getUsage();
        $disk = Monitoring::disk()->getUsage();

        // Get historical data for charts
        $records = MonitoringRecord::orderBy('created_at', 'desc')->limit(20)->get();

        return [
            'cpu'     => $cpu,
            'memory'  => $memory,
            'disk'    => $disk,
            'records' => $records,
        ];
    }
}
