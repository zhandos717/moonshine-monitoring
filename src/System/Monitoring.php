<?php

namespace Zhandos717\MoonshineMonitoring\System;

class Monitoring
{

    public function cpu(): SystemResource|CPU
    {
        return new CPU();
    }

    public function memory(): SystemResource|Memory
    {
        return new Memory();
    }


    public function disk():  SystemResource|Memory
    {
        return new Disk();
    }
}