<?php

namespace Zhandos717\MoonshineMonitoring\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Zhandos717\MoonshineMonitoring\System\CPU;

class CpuSamplingTest extends TestCase
{
    #[Test]
    public function linux_usage_is_the_delta_between_two_proc_stat_samples()
    {
        // С момента загрузки ЦП почти простаивал (1%), а за последний интервал занят на 75%
        $cpu = new class([['total' => 100000, 'idle' => 99000], ['total' => 100400, 'idle' => 99100]]) extends CPU {
            public function __construct(private array $samples)
            {
                parent::__construct();
            }

            protected function os(): string
            {
                return 'Linux';
            }

            protected function readProcStat(): ?array
            {
                return array_shift($this->samples);
            }
        };

        $this->assertSame(75.0, $cpu->getUsage());
        $this->assertSame(100, $cpu->getTotal());
    }

    #[Test]
    public function unreadable_proc_stat_gives_zero_instead_of_error()
    {
        $cpu = new class extends CPU {
            protected function os(): string
            {
                return 'Linux';
            }

            protected function readProcStat(): ?array
            {
                return null;
            }
        };

        $this->assertSame(0.0, $cpu->getUsage());
        $this->assertSame(0, $cpu->getTotal());
    }
}
