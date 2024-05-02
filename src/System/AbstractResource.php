<?php

namespace Zhandos717\MoonshineMonitoring\System;

use Illuminate\Support\Str;

abstract class AbstractResource
{
    protected function getOS(): string
    {
        return Str::of(PHP_OS)->lower()->value();
    }

    protected function getScriptName(): string
    {
        return sprintf('%s/../../scripts/%s/%s.sh', __DIR__, $this->getOS(), Str::of(get_class($this))->lower()->value());
    }
}