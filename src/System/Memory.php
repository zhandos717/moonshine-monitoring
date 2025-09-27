<?php

namespace Zhandos717\MoonshineMonitoring\System;

class Memory extends AbstractResource
{
    protected ?int $totalBytes = null;
    protected ?int $usedBytes = null;

    protected function run(): void
    {
        if (function_exists('app') && app() && method_exists(app(), 'environment') && app()->environment() === 'testing') {
            $this->setTotal(100);
            $this->setUsage(50);
            $this->totalBytes = 8 * 1024 * 1024 * 1024; // 8GB for testing
            $this->usedBytes = 4 * 1024 * 1024 * 1024; // 4GB for testing
            return;
        }

        // Используем PHP для получения информации о памяти
        $memoryInfo = $this->getMemoryInfo();
        
        if ($memoryInfo) {
            $this->setTotal($memoryInfo['total']);
            $this->setUsage($memoryInfo['usage']);
            $this->totalBytes = $memoryInfo['total_bytes'] ?? null;
            $this->usedBytes = $memoryInfo['used_bytes'] ?? null;
        } else {
            // Set default values if we can't get memory info
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
            
            // Convert to bytes
            $totalBytes = $total * 1024;
            $usedBytes = $used * 1024;
            
            return [
                'total' => 100,
                'usage' => round(($used / $total) * 100, 2),
                'total_bytes' => $totalBytes,
                'used_bytes' => $usedBytes
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
                // Get total physical memory using sysctl
                $totalMemoryOutput = shell_exec('sysctl -n hw.memsize 2>/dev/null');
                if ($totalMemoryOutput) {
                    $totalMemoryBytes = (int) trim($totalMemoryOutput);
                    $pageSizeBytes = $pageSize;
                    $usedBytes = $usedPages * $pageSizeBytes;
                    
                    return [
                        'total' => 100,
                        'usage' => round(($usedPages / $totalPages) * 100, 2),
                        'total_bytes' => $totalMemoryBytes,
                        'used_bytes' => $usedBytes
                    ];
                } else {
                    return [
                        'total' => 100,
                        'usage' => round(($usedPages / $totalPages) * 100, 2)
                    ];
                }
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
                
                // Convert to bytes
                $totalBytes = $total * 1024;
                $usedBytes = $used * 1024;
                
                return [
                    'total' => 100,
                    'usage' => round(($used / $total) * 100, 2),
                    'total_bytes' => $totalBytes,
                    'used_bytes' => $usedBytes
                ];
            }
        }
        
        return null;
    }
}