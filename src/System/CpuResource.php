<?php

namespace Zhandos717\MoonshineMonitoring\System;

interface CpuResource extends SystemResource
{
    public function getCores(): ?int;
}
