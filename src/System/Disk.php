<?php

namespace Zhandos717\MoonshineMonitoring\System;

class Disk extends AbstractResource
{
    public function usage(): int|float
    {
        return $this->run();
    }
}