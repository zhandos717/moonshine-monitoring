<?php

namespace Zhandos717\MoonshineMonitoring\Actions;

use SaeedVaziry\Monitoring\Exceptions\OSIsNotSupported;
use SaeedVaziry\Monitoring\Models\MonitoringRecord;

class RecordUsage
{
    /**
     * @param array $resources
     *
     * @return MonitoringRecord
     * @throws \Exception
     *
     */
    public function record(array $resources)
    {
        $this->checkOS();

        $model = config('monitoring.models.monitoring_record');
        $record = new $model([
            'instance_name' => str_replace(' ', '', config('monitoring.instance_name')),
            'cpu'           => $resources['cpu'] ?? null,
            'memory'        => $resources['memory'] ?? null,
            'disk'          => $resources['disk'] ?? null,
        ]);
        $record->save();

        return $record;
    }

    /**
     * @return void
     * @throws \Exception
     *
     */
    protected function checkOS()
    {
        if (PHP_OS !== 'Linux' && app()->environment() !== 'testing') {
            throw new OSIsNotSupported(PHP_OS);
        }
    }
}