<?php

namespace Zhandos717\MoonshineMonitoring\System;

class Disk
{
    /**
     * @return float|mixed
     */
    public function usage()
    {
        if (app()->environment() === 'testing') {
            return 50;
        }

        $usage = str_replace("\n", '', shell_exec(file_get_contents(__DIR__ . '/../../scripts/linux/disk.sh')));
        if (is_numeric($usage)) {
            return $usage;
        }

        return null;
    }
}