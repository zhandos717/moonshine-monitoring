<?php

namespace Zhandos717\MoonshineMonitoring\System;

class Monitoring
{
    public function cpu(): CpuResource
    {
        return new CPU();
    }

    public function memory(): SizedResource
    {
        return new Memory();
    }

    public function disk(): SizedResource
    {
        return new Disk((string) config('monitoring.disk_path', '/'));
    }
}
