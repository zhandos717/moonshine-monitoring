<?php

namespace Zhandos717\MoonshineMonitoring\System;

class CPU implements SystemResource
{
    public function usage(): mixed
    {
        if (app()->environment() === 'testing') {
            return 50;
        }

        $usage = str_replace("\n", '', shell_exec(file_get_contents(__DIR__ . '/../../scripts/linux/cpu.sh')));
        if (is_numeric($usage)) {
            return $usage;
        }

        return null;
    }
}