<?php

namespace Zhandos717\MoonshineMonitoring\System;

interface SizedResource extends SystemResource
{
    public function getTotalBytes(): ?int;

    public function getUsedBytes(): ?int;
}
