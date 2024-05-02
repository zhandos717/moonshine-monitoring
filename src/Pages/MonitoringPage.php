<?php

namespace Zhandos717\MoonshineMonitoring\Pages;

use MoonShine\Attributes\Icon;
use MoonShine\Decorations\Divider;
use MoonShine\Decorations\Grid;
use MoonShine\Metrics\LineChartMetric;
use MoonShine\Metrics\ValueMetric;
use MoonShine\Pages\Page;
use Zhandos717\MoonshineMonitoring\Components\MonitoringComponent;
use MoonShine\Decorations\Column;
use Zhandos717\MoonshineMonitoring\Facades\Monitoring;

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
        return [

            Grid::make([

                Column::make([
                    ValueMetric::make(__('moonshine-monitoring::monitoring.monitoring'))
                        ->value(Monitoring::disk()->getUsage())
                        ->progress(Monitoring::disk()->getTotal())
                ])->columnSpan(4),

                Column::make([
                    ValueMetric::make(__('moonshine-monitoring::monitoring.monitoring'))
                        ->value(Monitoring::cpu()->getUsage())
                        ->progress(Monitoring::cpu()->getTotal())
                ])->columnSpan(4),

                Column::make([
                    ValueMetric::make(__('moonshine-monitoring::monitoring.monitoring'))
                        ->value(Monitoring::memory()->getUsage())
                        ->progress(Monitoring::memory()->getTotal())
                ])->columnSpan(4),

//                LineChartMetric::make('Articles')
//                    ->line([
//                        'Count' => [
//                            now()->subDays()->format('Y-m-d') => 010,
//                            now()->format('Y-m-d')            => 010
//                        ]
//                    ])
//                    ->columnSpan(6),
//                LineChartMetric::make('Comments')
//                    ->line([
//                        'Count' => [
//                            now()->subDays()->format('Y-m-d') => 10,
//                            now()->format('Y-m-d')            => 10
//                        ]
//                    ])
//                    ->columnSpan(6)
            ])
        ];
    }
}
