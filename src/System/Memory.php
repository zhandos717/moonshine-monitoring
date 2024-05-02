<?php

namespace Zhandos717\MoonshineMonitoring\System;

class Memory implements SystemResource
{
    /**
     * @return string|int|float|null
     */
    public function usage(): string|int|null|float
    {
        if (app()->environment() === 'testing') {
            return 50;
        }

        $usage = str_replace("\n", '', shell_exec(file_get_contents(__DIR__ . '/../../scripts/linux/memory.sh')));
        if (is_numeric($usage)) {
            return $usage;
        }

        return null;
    }
}