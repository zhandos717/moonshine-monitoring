<?php

namespace Zhandos717\MoonshineMonitoring\Support;

class Instance
{
    public static function current(): string
    {
        return str_replace(' ', '', (string) config('monitoring.instance_name'));
    }
}
