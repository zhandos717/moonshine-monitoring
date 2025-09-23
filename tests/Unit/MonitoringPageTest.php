<?php

namespace Zhandos717\MoonshineMonitoring\Tests\Unit;

use Zhandos717\MoonshineMonitoring\Pages\MonitoringPage;
use Zhandos717\MoonshineMonitoring\Tests\TestCase;

class MonitoringPageTest extends TestCase
{
    /** @test */
    public function it_can_be_instantiated()
    {
        $page = new MonitoringPage();
        
        $this->assertInstanceOf(MonitoringPage::class, $page);
    }

    /** @test */
    public function it_has_the_correct_title()
    {
        $page = new MonitoringPage();
        $title = $page->title();
        
        // This will be translated, so we check if it returns a string
        $this->assertIsString($title);
    }

    /** @test */
    public function it_has_breadcrumbs()
    {
        $page = new MonitoringPage();
        $breadcrumbs = $page->breadcrumbs();
        
        $this->assertIsArray($breadcrumbs);
        $this->assertNotEmpty($breadcrumbs);
    }

    /** @test */
    public function it_has_components()
    {
        $page = new MonitoringPage();
        $components = $page->components();
        
        $this->assertIsArray($components);
        $this->assertNotEmpty($components);
    }
}