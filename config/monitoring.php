<?php

return [
    'auto_menu'     => true,
    'instance_name' => env('MONITORING_INSTANCE_NAME', env('APP_NAME')),


    'notifications' => [
        'telegram' => ''
    ],
    /** You can enable or disable migrations here*/
    'migrations'    => true,

    /** Purge recorded data
     * Supports PHP strtotime options like: '-1 day', '-2 hours', ...
     */
    'purge_before'  => '-1 day',
];
