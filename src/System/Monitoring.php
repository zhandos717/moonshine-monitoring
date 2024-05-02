<?php

namespace Zhandos717\MoonshineMonitoring\System;

class Monitoring
{
    public function cpu(): SystemResource
    {
        return new CPU();
    }

    public function memory(): SystemResource
    {
        return new Memory();
    }

    public function disk(): SystemResource
    {
        return new Disk();
    }
}