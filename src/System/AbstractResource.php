<?php

namespace Zhandos717\MoonshineMonitoring\System;

use Illuminate\Support\Str;

abstract class AbstractResource
{
    protected function getOS(): string
    {
        return strtolower(PHP_OS);
    }

    protected function getScriptName(): string
    {
        $scriptDirectory = $this->resolveScriptDirectory();
        $scriptFileName = $this->resolveScriptFileName();

        return sprintf('%s%s%s.sh', $scriptDirectory, DIRECTORY_SEPARATOR, $scriptFileName);
    }

    private function resolveScriptDirectory(): string
    {
        return realpath(
            __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'scripts' . DIRECTORY_SEPARATOR . $this->getOS(
            )
        );
    }

    private function resolveScriptFileName(): string
    {
        $className = $this->getSimpleClassName();
        return strtolower($className);
    }

    private function getSimpleClassName(): string
    {
        $fullClassName = get_called_class();
        $className = substr(strrchr($fullClassName, "\\"), 1);
        return $className ?: $fullClassName; // Fallback in case there is no namespace
    }
}