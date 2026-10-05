<?php

namespace Zhandos717\MoonshineMonitoring\Tests\Unit;

use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use PHPUnit\Framework\Attributes\Test;
use Zhandos717\MoonshineMonitoring\Alerts\ResourceAlert;
use Zhandos717\MoonshineMonitoring\Alerts\TelegramChannel;
use Zhandos717\MoonshineMonitoring\Facades\Monitoring;
use Zhandos717\MoonshineMonitoring\Models\MonitoringRecord;
use Zhandos717\MoonshineMonitoring\Tests\TestCase;

class PurgeAndAlertsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
        Monitoring::fake(memory: 95);
    }

    private function history(float $memory, int $minutes = 6): void
    {
        foreach (range($minutes, 1) as $ago) {
            MonitoringRecord::factory()->create([
                'instance_name' => 'test-instance', 'cpu' => 10, 'memory' => $memory, 'disk' => 10,
                'created_at' => now()->subMinutes($ago),
            ]);
        }
    }

    private function enableAlerts(array $overrides = []): void
    {
        config(['monitoring.alerts' => array_replace_recursive([
            'enabled' => true,
            'thresholds' => ['cpu' => 90, 'memory' => 90, 'disk' => 90],
            'minutes' => 5,
            'cooldown' => 60,
            'mail' => 'ops@example.com',
            'telegram' => ['bot_token' => null, 'chat_id' => null],
        ], $overrides)]);
    }

    #[Test]
    public function record_command_purges_records_older_than_configured_period()
    {
        MonitoringRecord::factory()->create(['instance_name' => 'test-instance', 'created_at' => now()->subDays(31)]);
        MonitoringRecord::factory()->create(['instance_name' => 'test-instance', 'created_at' => now()->subDays(29)]);

        $this->artisan('moonshine-monitoring:record')->expectsOutput('Purged 1 old records')->assertExitCode(0);

        $this->assertSame(2, MonitoringRecord::count());
    }

    #[Test]
    public function null_purge_period_keeps_everything()
    {
        config(['monitoring.purge_before' => null]);
        MonitoringRecord::factory()->create(['instance_name' => 'test-instance', 'created_at' => now()->subYears(2)]);

        $this->assertSame(0, MonitoringRecord::purgeOld());
    }

    #[Test]
    public function alert_is_sent_when_memory_stays_above_threshold()
    {
        Notification::fake();
        $this->enableAlerts();
        $this->history(93);

        $this->artisan('moonshine-monitoring:record')->expectsOutput('Alert sent: memory')->assertExitCode(0);

        Notification::assertSentOnDemand(ResourceAlert::class, function (ResourceAlert $alert, array $channels, AnonymousNotifiable $notifiable) {
            return $alert->metric === 'memory'
                && $alert->peak === 95.0
                && $notifiable->routes['mail'] === 'ops@example.com';
        });
    }

    #[Test]
    public function short_dip_below_threshold_prevents_alert()
    {
        Notification::fake();
        $this->enableAlerts();
        $this->history(93);
        MonitoringRecord::query()->orderBy('created_at')->skip(2)->first()->update(['memory' => 70]);

        $this->artisan('moonshine-monitoring:record')->assertExitCode(0);

        Notification::assertNothingSent();
    }

    #[Test]
    public function repeated_alert_waits_for_cooldown()
    {
        Notification::fake();
        $this->enableAlerts();
        $this->history(93);

        $this->artisan('moonshine-monitoring:record');
        $this->artisan('moonshine-monitoring:record');

        Notification::assertSentOnDemandTimes(ResourceAlert::class, 1);
    }

    #[Test]
    public function alerts_are_disabled_by_default()
    {
        Notification::fake();
        $this->history(99);

        $this->artisan('moonshine-monitoring:record');

        Notification::assertNothingSent();
    }

    #[Test]
    public function telegram_channel_posts_message_to_bot_api()
    {
        Http::fake(['api.telegram.org/*' => Http::response(['ok' => true])]);
        $this->enableAlerts(['mail' => null, 'telegram' => ['bot_token' => 'TEST', 'chat_id' => '-100']]);
        $this->history(93);

        $this->artisan('moonshine-monitoring:record')->expectsOutput('Alert sent: memory');

        Http::assertSent(fn ($request) => $request->url() === 'https://api.telegram.org/botTEST/sendMessage'
            && $request['chat_id'] === '-100'
            && str_contains($request['text'], '95%'));
    }
}
