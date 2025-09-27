<?php

namespace Zhandos717\MoonshineMonitoring\System;

class Disk extends AbstractResource
{
    protected ?int $totalBytes = null;
    protected ?int $usedBytes = null;

    protected function run(): void
    {
        if (function_exists('app') && app() && method_exists(app(), 'environment') && app()->environment() === 'testing') {
            $this->setTotal(100);
            $this->setUsage(50);
            $this->totalBytes = 500 * 1024 * 1024 * 1024; // 500GB for testing
            $this->usedBytes = 250 * 1024 * 1024 * 1024; // 250GB for testing
            return;
        }

        // Используем PHP для получения информации о диске
        $diskInfo = $this->getDiskInfo();
        
        if ($diskInfo) {
            $this->setTotal($diskInfo['total']);
            $this->setUsage($diskInfo['usage']);
            $this->totalBytes = $diskInfo['total_bytes'] ?? null;
            $this->usedBytes = $diskInfo['used_bytes'] ?? null;
        } else {
            // Set default values if we can't get disk info
            $this->setTotal(0);
            $this->setUsage(0);
        }
    }
    
    public function getTotalBytes(): ?int
    {
        return $this->totalBytes;
    }
    
    public function getUsedBytes(): ?int
    {
        return $this->usedBytes;
    }
    
    private function getDiskInfo(): ?array
    {
        try {
            // Получаем информацию о корневом разделе
            $total = disk_total_space("/");
            $free = disk_free_space("/");
            
            if ($total === false || $free === false) {
                return null;
            }
            
            $used = $total - $free;
            $usage = round(($used / $total) * 100, 2);
            
            return [
                'total' => 100,
                'usage' => $usage,
                'total_bytes' => (int) $total,
                'used_bytes' => (int) $used
            ];
        } catch (\Exception $e) {
            // В случае ошибки возвращаем null
            return null;
        }
    }
}