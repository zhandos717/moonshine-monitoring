<?php

namespace Zhandos717\MoonshineMonitoring\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Zhandos717\MoonshineMonitoring\Support\MemorySpikeDetector;

class MemorySpikeDetectorTest extends TestCase
{
    private function series(array $values): array
    {
        return array_map(static fn ($v, $i) => ['t' => sprintf('2026-10-05 10:%02d', $i), 'value' => (float) $v], $values, array_keys($values));
    }

    #[Test]
    public function it_finds_no_spikes_in_flat_series()
    {
        $this->assertSame([], (new MemorySpikeDetector())->detect($this->series([50, 51, 49, 50, 52, 50])));
    }

    #[Test]
    public function it_groups_consecutive_elevated_points_into_one_episode()
    {
        $spikes = (new MemorySpikeDetector())->detect($this->series([50, 50, 51, 50, 63, 72, 66, 51, 50]));

        $this->assertCount(1, $spikes);
        $this->assertSame('2026-10-05 10:04', $spikes[0]['start']);
        $this->assertSame('2026-10-05 10:06', $spikes[0]['end']);
        $this->assertSame('2026-10-05 10:05', $spikes[0]['peak_at']);
        $this->assertSame(72.0, $spikes[0]['peak']);
        $this->assertSame(50.0, $spikes[0]['baseline']);
        $this->assertSame(22.0, $spikes[0]['rise']);
        $this->assertSame(3, $spikes[0]['points']);
    }

    #[Test]
    public function long_spike_does_not_raise_its_own_baseline()
    {
        $spikes = (new MemorySpikeDetector(window: 3))->detect($this->series([40, 40, 40, 60, 60, 60, 60, 60, 40]));

        $this->assertCount(1, $spikes);
        $this->assertSame(5, $spikes[0]['points']);
        $this->assertSame(40.0, $spikes[0]['baseline']);
    }

    #[Test]
    public function values_above_warning_threshold_count_as_spike_even_without_rise()
    {
        $spikes = (new MemorySpikeDetector(warning: 85))->detect($this->series([84, 86, 84]));

        $this->assertCount(1, $spikes);
        $this->assertSame(86.0, $spikes[0]['peak']);
    }

    #[Test]
    public function it_separates_two_episodes()
    {
        $spikes = (new MemorySpikeDetector())->detect($this->series([50, 50, 70, 50, 50, 75, 50]));

        $this->assertCount(2, $spikes);
        $this->assertSame([70.0, 75.0], array_column($spikes, 'peak'));
    }
}
