<?php

namespace Zhandos717\MoonshineMonitoring\Testing;

use Zhandos717\MoonshineMonitoring\System\CpuResource;
use Zhandos717\MoonshineMonitoring\System\SizedResource;
use Zhandos717\MoonshineMonitoring\System\SystemResource;

class FakeResource implements CpuResource, SizedResource
{
    public function __construct(
        private ?float $usage,
        private ?int $totalBytes = null,
        private readonly ?int $cores = null,
        private ?int $total = 100,
    ) {
    }

    public function getUsage(): ?float
    {
        return $this->usage;
    }

    public function getTotal(): ?int
    {
        return $this->total;
    }

    public function setUsage(?float $usage): SystemResource
    {
        $this->usage = $usage;

        return $this;
    }

    public function setTotal(?int $total): SystemResource
    {
        $this->total = $total;

        return $this;
    }

    public function getCores(): ?int
    {
        return $this->cores;
    }

    public function getTotalBytes(): ?int
    {
        return $this->totalBytes;
    }

    public function getUsedBytes(): ?int
    {
        return $this->totalBytes && $this->usage !== null ? (int) ($this->totalBytes * $this->usage / 100) : null;
    }
}
