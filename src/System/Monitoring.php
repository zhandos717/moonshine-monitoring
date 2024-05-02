<?php

namespace Zhandos717\MoonshineMonitoring\System;

class Monitoring
{
    /**
     * @return SystemResource
     */
    public function cpu()
    {
        return new CPU();
    }

    /**
     * @return SystemResource
     */
    public function memory()
    {
        return new Memory();
    }

    /**
     * @return SystemResource
     */
    public function disk()
    {
        return new Disk();
    }
}