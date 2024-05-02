<?php

namespace Zhandos717\MoonshineMonitoring\System;

class CPU extends AbstractResource
{
    public function usage(): int|float
    {
        return $this->run();
    }
}