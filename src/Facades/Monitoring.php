<?php

namespace Zhandos717\MoonshineMonitoring\Facades;

use Illuminate\Support\Facades\Facade;
use Zhandos717\MoonshineMonitoring\System\CpuResource;
use Zhandos717\MoonshineMonitoring\System\SizedResource;
use Zhandos717\MoonshineMonitoring\Testing\FakeMonitoring;

/**
 * @method static CpuResource cpu()
 * @method static SizedResource memory()
 * @method static SizedResource disk()
 */
class Monitoring extends Facade
{
    public static function fake(
        float $cpu = 50,
        float $memory = 50,
        float $disk = 50,
        int $cores = 4,
        int $memoryTotalBytes = 8 * 1024 ** 3,
        int $diskTotalBytes = 500 * 1024 ** 3,
    ): FakeMonitoring {
        static::swap($fake = new FakeMonitoring($cpu, $memory, $disk, $cores, $memoryTotalBytes, $diskTotalBytes));

        return $fake;
    }

    protected static function getFacadeAccessor(): string
    {
        return 'monitoring';
    }
}
