<?php

return [
    'auto_menu'     => true,
    'instance_name' => env('APP_NAME', 'Default Instance'),
    'migrations'    => true,

    // Записи старше этого срока удаляет moonshine-monitoring:record; null — хранить всё
    'purge_before'  => '-30 days',

    // Раздел, занятость которого показывает метрика «Диск»
    'disk_path'     => '/',

    // Пороги в процентах: статус плиток и выделение всплесков памяти на графике
    'thresholds' => [
        'warning'  => 85,
        'critical' => 95,
    ],

    // Всплеск — рост памяти на min_rise п.п. над медианой последних window спокойных замеров
    'memory_spikes' => [
        'min_rise' => 8,
        'window'   => 15,
    ],

    // Уведомление, когда метрика держится выше порога minutes минут подряд; повтор не чаще раза в cooldown минут
    'alerts' => [
        'enabled'    => env('MONITORING_ALERTS', false),
        'thresholds' => [
            'cpu'    => 90,
            'memory' => 90,
            'disk'   => 90,
        ],
        'minutes'  => 5,
        'cooldown' => 60,
        'mail'     => env('MONITORING_ALERT_MAIL'),
        'telegram' => [
            'bot_token' => env('MONITORING_TELEGRAM_BOT_TOKEN'),
            'chat_id'   => env('MONITORING_TELEGRAM_CHAT_ID'),
        ],
    ],

    // Как часто страница мониторинга обновляет данные без перезагрузки, секунды; 0 — выключено
    'auto_refresh' => 60,
];
