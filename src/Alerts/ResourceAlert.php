<?php

namespace Zhandos717\MoonshineMonitoring\Alerts;

use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResourceAlert extends Notification
{
    public function __construct(
        public readonly string $instance,
        public readonly string $metric,
        public readonly float $current,
        public readonly float $peak,
        public readonly float $threshold,
        public readonly int $minutes,
    ) {
    }

    /**
     * @return list<string>
     */
    public function via(AnonymousNotifiable $notifiable): array
    {
        return array_keys($notifiable->routes);
    }

    public function toMail(AnonymousNotifiable $notifiable): MailMessage
    {
        return (new MailMessage())
            ->error()
            ->subject($this->title())
            ->line($this->text());
    }

    public function toTelegram(AnonymousNotifiable $notifiable): string
    {
        return "⚠️ {$this->title()}\n{$this->text()}";
    }

    public function title(): string
    {
        return __('moonshine-monitoring::ui.alert_title', [
            'metric' => __('moonshine-monitoring::ui.' . $this->metric),
            'instance' => $this->instance,
        ]);
    }

    public function text(): string
    {
        return __('moonshine-monitoring::ui.alert_text', [
            'minutes' => $this->minutes,
            'threshold' => $this->format($this->threshold),
            'current' => $this->format($this->current),
            'peak' => $this->format($this->peak),
        ]);
    }

    private function format(float $value): string
    {
        return rtrim(rtrim(number_format($value, 1, '.', ''), '0'), '.');
    }
}
