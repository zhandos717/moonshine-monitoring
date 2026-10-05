<?php

namespace Zhandos717\MoonshineMonitoring\System;

class CPU extends AbstractResource implements CpuResource
{
    // /proc/stat копит счётчики с загрузки системы, поэтому текущую загрузку даёт только разница двух замеров
    public const SAMPLE_INTERVAL_MS = 250;

    protected ?int $cores = null;

    protected function run(): void
    {
        $this->cores = $this->detectCores();
        $usage = match ($this->os()) {
            'Linux' => $this->linuxUsage(),
            'Darwin' => $this->macUsage(),
            'Windows' => $this->windowsUsage(),
            default => null,
        };

        $this->setTotal($usage === null ? 0 : 100);
        $this->setUsage($usage ?? 0);
    }

    public function getCores(): ?int
    {
        return $this->cores;
    }

    private function detectCores(): ?int
    {
        $cores = match ($this->os()) {
            'Linux' => is_readable('/proc/cpuinfo')
                ? preg_match_all('/^processor\s*:/m', (string) file_get_contents('/proc/cpuinfo'))
                : 0,
            'Darwin' => (int) $this->shell('sysctl -n hw.ncpu 2>/dev/null'),
            'Windows' => (int) getenv('NUMBER_OF_PROCESSORS'),
            default => 0,
        };

        return $cores > 0 ? $cores : null;
    }

    private function linuxUsage(): ?float
    {
        $first = $this->readProcStat();
        if ($first === null) {
            return null;
        }
        usleep(self::SAMPLE_INTERVAL_MS * 1000);
        $second = $this->readProcStat();
        if ($second === null) {
            return null;
        }

        $total = $second['total'] - $first['total'];
        $idle = $second['idle'] - $first['idle'];

        return $total > 0 ? round(($total - $idle) / $total * 100, 2) : null;
    }

    /**
     * @return array{total: int, idle: int}|null
     */
    protected function readProcStat(): ?array
    {
        if (!is_readable('/proc/stat')) {
            return null;
        }
        $handle = fopen('/proc/stat', 'r');
        $line = $handle ? fgets($handle) : false;
        if ($handle) {
            fclose($handle);
        }
        if ($line === false || !str_starts_with($line, 'cpu ')) {
            return null;
        }

        // user nice system idle iowait irq softirq steal; guest уже входит в user
        $fields = array_map('intval', array_slice(preg_split('/\s+/', trim($line)), 1, 8));
        if (count($fields) < 4) {
            return null;
        }

        return ['total' => array_sum($fields), 'idle' => $fields[3] + ($fields[4] ?? 0)];
    }

    private function macUsage(): ?float
    {
        // macOS не отдаёт мгновенную загрузку без внешних утилит: берём load average за минуту на ядро
        $loads = sys_getloadavg();
        if ($loads === false || !$this->cores) {
            return null;
        }

        return min(100, round($loads[0] / $this->cores * 100, 2));
    }

    private function windowsUsage(): ?float
    {
        // wmic удалён в Windows 11 24H2
        $output = $this->powershell('(Get-CimInstance Win32_Processor | Measure-Object -Property LoadPercentage -Average).Average');

        return $output !== null && is_numeric(trim($output)) ? round((float) trim($output), 2) : null;
    }
}
