<?php

namespace Zhandos717\MoonshineMonitoring\Pages;

use MoonShine\Attributes\Icon;
use MoonShine\Decorations\Grid;
use MoonShine\Metrics\LineChartMetric;
use MoonShine\Metrics\ValueMetric;
use MoonShine\Pages\Page;
use Zhandos717\MoonshineMonitoring\Components\MonitoringComponent;
use MoonShine\Decorations\Column;
use Zhandos717\MoonshineMonitoring\Facades\Monitoring;
use Zhandos717\MoonshineMonitoring\Models\MonitoringRecord;

#[Icon('heroicons.outline.cpu-chip')]
class MonitoringPage extends Page
{
    public function title(): string
    {
        return __('moonshine-monitoring::monitoring.monitoring');
    }

    public function breadcrumbs(): array
    {
        return [
            '#' => $this->title(),
        ];
    }

    public function components(): array
    {
        // Get historical data for charts
        $records = MonitoringRecord::orderBy('created_at', 'desc')->limit(30)->get();
        
        // Prepare chart data
        $cpuData = [];
        $memoryData = [];
        $diskData = [];
        $labels = [];
        
        foreach ($records->reverse() as $record) {
            $labels[] = $record->created_at->format('H:i');
            $cpuData[] = $record->cpu;
            $memoryData[] = $record->memory;
            $diskData[] = $record->disk;
        }

        return [
            Grid::make([
                Column::make([
                    ValueMetric::make('Disk Usage')
                        ->value(Monitoring::disk()->getUsage())
                        ->progress(Monitoring::disk()->getTotal())
                ])->columnSpan(4),

                Column::make([
                    ValueMetric::make('CPU Usage')
                        ->value(Monitoring::cpu()->getUsage())
                        ->progress(Monitoring::cpu()->getTotal())
                ])->columnSpan(4),

                Column::make([
                    ValueMetric::make('Memory Usage')
                        ->value(Monitoring::memory()->getUsage())
                        ->progress(Monitoring::memory()->getTotal())
                ])->columnSpan(4),

                Column::make([
                    LineChartMetric::make('CPU Usage History')
                        ->line([
                            'CPU %' => array_combine($labels, $cpuData)
                        ])
                ])->columnSpan(6),
                
                Column::make([
                    LineChartMetric::make('Memory Usage History')
                        ->line([
                            'Memory %' => array_combine($labels, $memoryData)
                        ])
                ])->columnSpan(6),
            ]),
            
            MonitoringComponent::make()
        ];
    }
}
