<?php

namespace Zhandos717\MoonshineMonitoring\System;

class Memory extends AbstractResource
{
    protected function run(): void
    {
        if (app()->environment() === 'testing') {
            $this->setTotal(100);
            $this->setUsage(50);
            return;
        }

        // Используем PHP для получения информации о памяти
        $memoryInfo = $this->getMemoryInfo();
        
        if ($memoryInfo) {
            $this->setTotal($memoryInfo['total']);
            $this->setUsage($memoryInfo['usage']);
        }
    }
    
    private function getMemoryInfo(): ?array
    {
        $os = strtolower(PHP_OS);
        
        try {
            if ($os === 'linux') {
                return $this->getLinuxMemoryInfo();
            } elseif (strpos($os, 'darwin') !== false) { // macOS
                return $this->getMacMemoryInfo();
            } elseif (strpos($os, 'win') !== false) { // Windows
                return $this->getWindowsMemoryInfo();
            }
        } catch (\Exception $e) {
            // В случае ошибки возвращаем null
            return null;
        }
        
        return null;
    }
    
    private function getLinuxMemoryInfo(): ?array
    {
        // Читаем информацию из /proc/meminfo
        if (!file_exists('/proc/meminfo')) {
            return null;
        }
        
        $memData = file_get_contents('/proc/meminfo');
        $lines = explode("\n", $memData);
        
        $memInfo = [];
        foreach ($lines as $line) {
            if (preg_match('/^(\w+):\s+(\d+)/', $line, $matches)) {
                $memInfo[$matches[1]] = (int) $matches[2];
            }
        }
        
        if (isset($memInfo['MemTotal']) && isset($memInfo['MemAvailable'])) {
            $total = $memInfo['MemTotal']; // в KB
            $available = $memInfo['MemAvailable']; // в KB
            $used = $total - $available;
            
            return [
                'total' => 100,
                'usage' => round(($used / $total) * 100, 2)
            ];
        }
        
        return null;
    }
    
    private function getMacMemoryInfo(): ?array
    {
        // Используем vm_stat для получения информации о памяти на macOS
        $vmStat = shell_exec('vm_stat');
        if (!$vmStat) {
            return null;
        }
        
        // Парсим вывод vm_stat
        $lines = explode("\n", $vmStat);
        $memInfo = [];
        
        foreach ($lines as $line) {
            if (preg_match('/([^:]+):\s+(\d+)/', $line, $matches)) {
                $key = trim($matches[1]);
                $value = (int) trim($matches[2]);
                $memInfo[$key] = $value;
            }
        }
        
        // Получаем размер страницы
        $pageSizeOutput = shell_exec('vm_stat | grep "page size of"');
        if (preg_match('/(\d+)/', $pageSizeOutput, $pageSizeMatches)) {
            $pageSize = (int) $pageSizeMatches[1]; // в байтах
            
            // Вычисляем использование памяти
            $freePages = $memInfo['Pages free'] ?? 0;
            $inactivePages = $memInfo['Pages inactive'] ?? 0;
            $activePages = $memInfo['Pages active'] ?? 0;
            
            $totalPages = $freePages + $inactivePages + $activePages;
            $usedPages = $activePages;
            
            if ($totalPages > 0) {
                return [
                    'total' => 100,
                    'usage' => round(($usedPages / $totalPages) * 100, 2)
                ];
            }
        }
        
        return null;
    }
    
    private function getWindowsMemoryInfo(): ?array
    {
        // На Windows используем wmic для получения информации о памяти
        $output = shell_exec('wmic OS get FreePhysicalMemory,TotalVisibleMemorySize /value');
        if ($output) {
            $lines = explode("\n", $output);
            $memInfo = [];
            
            foreach ($lines as $line) {
                if (strpos($line, '=') !== false) {
                    list($key, $value) = explode('=', trim($line));
                    $memInfo[$key] = (int) $value;
                }
            }
            
            if (isset($memInfo['TotalVisibleMemorySize']) && isset($memInfo['FreePhysicalMemory'])) {
                $total = $memInfo['TotalVisibleMemorySize']; // в KB
                $free = $memInfo['FreePhysicalMemory']; // в KB
                $used = $total - $free;
                
                return [
                    'total' => 100,
                    'usage' => round(($used / $total) * 100, 2)
                ];
            }
        }
        
        return null;
    }
}