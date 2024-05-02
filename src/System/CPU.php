<?php

namespace Zhandos717\MoonshineMonitoring\System;

class CPU extends AbstractResource
{
    public function usage(): mixed
    {
        if (app()->environment() === 'testing') {
            return 50;
        }

        $usage = str_replace("\n", '', shell_exec(file_get_contents($this->getScriptName())));
        if (is_numeric($usage)) {
            return $usage;
        }

        return null;
    }

    protected function getScriptName(): string
    {
        return sprintf('%s/../../scripts/%s/cpu.sh', __DIR__, $this->getOS());
    }
}