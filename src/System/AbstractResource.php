<?php

namespace Zhandos717\MoonshineMonitoring\System;

abstract class AbstractResource implements SystemResource
{
    protected ?int $total = null;
    protected ?float $usage = null;

    public function __construct()
    {
        $this->run();
    }

    abstract protected function run(): void;

    protected function os(): string
    {
        return PHP_OS_FAMILY;
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
        return $this->usage ?? 0;
    }

    public function getTotal(): ?int
    {
        return $this->total ?? 0;
    }

    protected function shell(string $command): ?string
    {
        if (!function_exists('shell_exec')) {
            return null;
        }
        $output = @shell_exec($command);

        return is_string($output) && trim($output) !== '' ? $output : null;
    }

    protected function powershell(string $script): ?string
    {
        return $this->shell('powershell -NoProfile -NonInteractive -Command "' . str_replace('"', '\"', $script) . '"');
    }
}
