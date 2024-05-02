<?php

namespace Zhandos717\MoonshineMonitoring\Facades;

use Illuminate\Support\Facades\Facade;
use Zhandos717\MoonshineMonitoring\System\SystemResource;

/**
 * Class Monitoring.
 *
 *
 * @method static SystemResource cpu()
 * @method static SystemResource memory()
 * @method static SystemResource disk()
 */
class Monitoring extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'monitoring';
    }
}