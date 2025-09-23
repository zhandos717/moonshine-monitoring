<?php

namespace Zhandos717\MoonshineMonitoring\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Zhandos717\MoonshineMonitoring\System\CPU;
use Zhandos717\MoonshineMonitoring\System\Memory;
use Zhandos717\MoonshineMonitoring\System\Disk;

class PHPSysResourceTest extends TestCase
{
    /** @test */
    public function it_has_correct_class_structure()
    {
        // Проверяем только структуру классов, не создавая экземпляры
        $this->assertTrue(class_exists(CPU::class));
        $this->assertTrue(class_exists(Memory::class));
        $this->assertTrue(class_exists(Disk::class));

        $this->assertTrue(is_subclass_of(CPU::class, 'Zhandos717\MoonshineMonitoring\System\AbstractResource'));
        $this->assertTrue(is_subclass_of(Memory::class, 'Zhandos717\MoonshineMonitoring\System\AbstractResource'));
        $this->assertTrue(is_subclass_of(Disk::class, 'Zhandos717\MoonshineMonitoring\System\AbstractResource'));
    }
}