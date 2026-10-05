<?php

namespace Zhandos717\MoonshineMonitoring\Testing;

use Zhandos717\MoonshineMonitoring\System\Monitoring;

/**
 * Подменяет замеры в тестах приложения: Monitoring::fake(cpu: 40, memory: 90).
 */
class FakeMonitoring extends Monitoring
{
    public function __construct(
        public float $cpu = 50,
        public float $memory = 50,
        public float $disk = 50,
        public int $cores = 4,
        public int $memoryTotalBytes = 8 * 1024 ** 3,
        public int $diskTotalBytes = 500 * 1024 ** 3,
    ) {
    }

    public function cpu(): FakeResource
    {
        return new FakeResource($this->cpu, cores: $this->cores);
    }

    public function memory(): FakeResource
    {
        return new FakeResource($this->memory, $this->memoryTotalBytes);
    }

    public function disk(): FakeResource
    {
        return new FakeResource($this->disk, $this->diskTotalBytes);
    }
}
