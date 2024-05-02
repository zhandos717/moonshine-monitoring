<?php

namespace Zhandos717\MoonshineMonitoring\System;

interface SystemResource
{
    public function usage(): int|float;
}