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
                    ValueMetric::make('Articles')
                        ->value(Article::query()->count()),
                ])->columnSpan(6),

                LineChartMetric::make('Articles')
                    ->line([
                        'Count' => [
                            now()->subDays()->format('Y-m-d') => 010,
                            now()->format('Y-m-d')            => 010
                        ]
                    ])
                    ->columnSpan(6),
                LineChartMetric::make('Comments')
                    ->line([
                        'Count' => [
                            now()->subDays()->format('Y-m-d') => 10,
                            now()->format('Y-m-d')            => 10
                        ]
                    ])
                    ->columnSpan(6)
            ])
        ];
    }
}
