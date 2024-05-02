<?php

namespace Zhandos717\MoonshineMonitoring\System;

use Illuminate\Support\Str;

abstract class AbstractResource implements SystemResource
{

    protected ?int $total;
    protected ?float $usage;

    public function __construct()
    {
        $this->run();
    }

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

    public function setUsage(?float $usage): SystemResource
    {
        $this->usage = $usage;

        return $this;
    }

    public function setTotal(?int $total): SystemResource
    {
        $this->total = $total;

        return $this;
    }


    public function getUsage(): ?float
    {
        return $this->usage;
    }

    public function getTotal(): ?int
    {
        return $this->total;
    }

    protected function run(): void
    {
        if (app()->environment() === 'testing') {
            $this->setTotal(100);
            $this->setUsage(50);
        }

        $usage = str_replace("\n", '', shell_exec(file_get_contents($this->getScriptName())));


        if (is_numeric($usage)) {
            $this->setTotal(100);
            $this->setUsage($usage);
            return;
        }

        $result = json_decode($usage, true);
        if (is_array($result)) {
            $this->setTotal($result['total'] ?? null);
            $this->setUsage($result['used'] ?? null);
        }
    }
}