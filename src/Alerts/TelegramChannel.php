<?php

namespace Zhandos717\MoonshineMonitoring\Alerts;

use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Http;

class TelegramChannel
{
    public function send(AnonymousNotifiable $notifiable, ResourceAlert $notification): void
    {
        $token = (string) config('monitoring.alerts.telegram.bot_token');

        Http::asJson()
            ->timeout(10)
            ->post("https://api.telegram.org/bot{$token}/sendMessage", [
                'chat_id' => $notifiable->routeNotificationFor(self::class),
                'text' => $notification->toTelegram($notifiable),
            ])
            ->throw();
    }
}
