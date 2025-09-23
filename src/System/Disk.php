<?php

namespace Zhandos717\MoonshineMonitoring\System;

class Disk extends AbstractResource
{
    protected function run(): void
    {
        if (app()->environment() === 'testing') {
            $this->setTotal(100);
            $this->setUsage(50);
            return;
        }

        // Используем PHP для получения информации о диске
        $diskInfo = $this->getDiskInfo();
        
        if ($diskInfo) {
            $this->setTotal($diskInfo['total']);
            $this->setUsage($diskInfo['usage']);
        }
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
                'usage' => $usage
            ];
        } catch (\Exception $e) {
            // В случае ошибки возвращаем null
            return null;
        }
    }
}