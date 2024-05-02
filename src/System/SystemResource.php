<?php

namespace Zhandos717\MoonshineMonitoring\System;

interface SystemResource
{
    public function getUsage(): ?float;

    public function getTotal(): ?int;

    public function setUsage(?float $usage): SystemResource;

    public function setTotal(?int $total): SystemResource;
}