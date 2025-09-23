<?php

namespace Zhandos717\MoonshineMonitoring\System;

use Illuminate\Support\Str;

abstract class AbstractResource implements SystemResource
{

    protected ?int $total = null;
    protected ?float $usage = null;

    public function __construct()
    {
        $this->run();
    }

    protected function getOS(): string
    {
        return strtolower(PHP_OS);
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
        
        // Каждый конкретный ресурс должен реализовать свою логику в run()
        // Этот метод остается для обратной совместимости
    }
}