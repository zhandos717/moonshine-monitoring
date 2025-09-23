<?php

namespace Zhandos717\MoonshineMonitoring\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Zhandos717\MoonshineMonitoring\Models\MonitoringRecord;

class MonitoringRecordFactory extends Factory
{
    protected $model = MonitoringRecord::class;

    public function definition()
    {
        return [
            'instance_name' => $this->faker->word,
            'cpu' => $this->faker->randomFloat(2, 0, 100),
            'memory' => $this->faker->randomFloat(2, 0, 100),
            'disk' => $this->faker->randomFloat(2, 0, 100),
        ];
    }
}