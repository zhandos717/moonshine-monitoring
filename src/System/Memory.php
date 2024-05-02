<?php

namespace Zhandos717\MoonshineMonitoring\System;

class Memory extends AbstractResource
{
    public function usage(): int|float
    {
        return $this->run();
    }
}