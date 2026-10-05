<?php

namespace Zhandos717\MoonshineMonitoring\System;

class Memory extends AbstractResource implements SizedResource
{
    protected ?int $totalBytes = null;
    protected ?int $usedBytes = null;

    protected function run(): void
    {
        $info = match ($this->os()) {
            'Linux' => $this->linux(),
            'Darwin' => $this->mac(),
            'Windows' => $this->windows(),
            default => null,
        };

        if ($info === null || $info['total'] <= 0) {
            $this->setTotal(0);
            $this->setUsage(0);

            return;
        }

        $this->totalBytes = $info['total'];
        $this->usedBytes = $info['used'];
        $this->setTotal(100);
        $this->setUsage(round($info['used'] / $info['total'] * 100, 2));
    }

    public function getTotalBytes(): ?int
    {
        return $this->totalBytes;
    }

    public function getUsedBytes(): ?int
    {
        return $this->usedBytes;
    }

    /**
     * @return array{total: int, used: int}|null
     */
    private function linux(): ?array
    {
        if (!is_readable('/proc/meminfo')) {
            return null;
        }
        preg_match_all('/^(\w+):\s+(\d+)/m', (string) file_get_contents('/proc/meminfo'), $matches);
        $info = array_combine($matches[1], array_map('intval', $matches[2]));
        if (!isset($info['MemTotal'], $info['MemAvailable'])) {
            return null;
        }

        return ['total' => $info['MemTotal'] * 1024, 'used' => ($info['MemTotal'] - $info['MemAvailable']) * 1024];
    }

    /**
     * «Занято» как в Мониторинге системы: память приложений + wired + сжатая.
     *
     * @return array{total: int, used: int}|null
     */
    private function mac(): ?array
    {
        $vmStat = $this->shell('vm_stat 2>/dev/null');
        $total = (int) $this->shell('sysctl -n hw.memsize 2>/dev/null');
        if ($vmStat === null || $total <= 0 || !preg_match('/page size of (\d+) bytes/', $vmStat, $size)) {
            return null;
        }
        preg_match_all('/^"?([^:"]+)"?:\s+(\d+)\./m', $vmStat, $matches);
        $pages = array_combine($matches[1], array_map('intval', $matches[2]));

        $app = isset($pages['Anonymous pages'])
            ? $pages['Anonymous pages'] - ($pages['Pages purgeable'] ?? 0)
            : ($pages['Pages active'] ?? 0);
        $used = ($app + ($pages['Pages wired down'] ?? 0) + ($pages['Pages occupied by compressor'] ?? 0)) * (int) $size[1];

        return ['total' => $total, 'used' => min($used, $total)];
    }

    /**
     * @return array{total: int, used: int}|null
     */
    private function windows(): ?array
    {
        $output = $this->powershell(
            'Get-CimInstance Win32_OperatingSystem | Select-Object FreePhysicalMemory,TotalVisibleMemorySize | ConvertTo-Json -Compress'
        );
        $data = $output !== null ? json_decode($output, true) : null;
        if (!is_array($data) || empty($data['TotalVisibleMemorySize'])) {
            return null;
        }
        $total = (int) $data['TotalVisibleMemorySize'] * 1024;

        return ['total' => $total, 'used' => $total - (int) $data['FreePhysicalMemory'] * 1024];
    }
}
