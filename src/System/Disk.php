<?php

namespace Zhandos717\MoonshineMonitoring\System;

class Disk extends AbstractResource implements SizedResource
{
    protected ?int $totalBytes = null;
    protected ?int $usedBytes = null;

    public function __construct(private readonly string $path = '/')
    {
        parent::__construct();
    }

    protected function run(): void
    {
        $total = @disk_total_space($this->path);
        $free = @disk_free_space($this->path);

        if (!$total || $free === false) {
            $this->setTotal(0);
            $this->setUsage(0);

            return;
        }

        $this->totalBytes = (int) $total;
        $this->usedBytes = (int) ($total - $free);
        $this->setTotal(100);
        $this->setUsage(round(($total - $free) / $total * 100, 2));
    }

    public function getTotalBytes(): ?int
    {
        return $this->totalBytes;
    }

    public function getUsedBytes(): ?int
    {
        return $this->usedBytes;
    }
}
