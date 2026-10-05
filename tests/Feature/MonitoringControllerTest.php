<?php

namespace Zhandos717\MoonshineMonitoring\Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use Illuminate\Foundation\Auth\User;
use Zhandos717\MoonshineMonitoring\Models\MonitoringRecord;
use Zhandos717\MoonshineMonitoring\Tests\TestCase;

class MonitoringControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Run migrations
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
    }

    #[Test]
    public function it_redirects_guests_to_login()
    {
        $this->get('/admin/monitoring/data')->assertRedirect();
    }

    #[Test]
    public function it_returns_monitoring_data_for_authenticated_users()
    {
        MonitoringRecord::factory()->count(5)->create([
            'instance_name' => 'test-instance',
        ]);

        $user = (new User())->forceFill(['id' => 1]);

        $this->actingAs($user, 'moonshine')
            ->getJson('/admin/monitoring/data')
            ->assertOk()
            ->assertJsonPath('status', 'success')
            ->assertJsonCount(6, 'records');
    }
}
