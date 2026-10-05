<?php

return [
    'auto_menu'     => true,
    'instance_name' => env('APP_NAME', 'Default Instance'),
    'migrations'    => true,
    'purge_before'  => '-1 day',

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
];
