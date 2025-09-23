<?php

namespace Zhandos717\MoonshineMonitoring\Tests\Unit;

use Zhandos717\MoonshineMonitoring\System\CPU;
use Zhandos717\MoonshineMonitoring\System\Memory;
use Zhandos717\MoonshineMonitoring\System\Disk;
use Zhandos717\MoonshineMonitoring\Tests\TestCase;

class ShellScriptTest extends TestCase
{
    /** @test */
    public function it_can_get_script_paths()
    {
        if (strtolower(PHP_OS) === 'darwin') {
            $cpu = new CPU();
            $scriptPath = $this->getPrivateMethod($cpu, 'getScriptName');
            
            $this->assertStringContainsString('scripts/darwin/cpu.sh', $scriptPath);
        }
    }
    
    /**
     * Helper method to call private methods for testing
     */
    private function getPrivateMethod($object, $methodName)
    {
        $reflection = new \ReflectionClass($object);
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);
        return $method->invoke($object);
    }
}