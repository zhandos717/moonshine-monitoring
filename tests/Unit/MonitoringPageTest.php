<?php

namespace Zhandos717\MoonshineMonitoring\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use Zhandos717\MoonshineMonitoring\Pages\MonitoringPage;
use Zhandos717\MoonshineMonitoring\Tests\TestCase;

class MonitoringPageTest extends TestCase
{
    #[Test]
    public function it_can_be_instantiated()
    {
        $page = app(MonitoringPage::class);
        
        $this->assertInstanceOf(MonitoringPage::class, $page);
    }

    #[Test]
    public function it_has_the_correct_title()
    {
        $page = app(MonitoringPage::class);
        $title = $page->getTitle();
        
        // This will be translated, so we check if it returns a string
        $this->assertIsString($title);
    }

    #[Test]
    public function it_has_breadcrumbs()
    {
        $page = app(MonitoringPage::class);
        $breadcrumbs = $page->getBreadcrumbs();
        
        $this->assertIsArray($breadcrumbs);
        $this->assertNotEmpty($breadcrumbs);
    }

    #[Test]
    public function it_has_components()
    {
        $page = app(MonitoringPage::class);
        $components = $page->components();
        
        $this->assertIsArray($components);
        $this->assertNotEmpty($components);
    }
}