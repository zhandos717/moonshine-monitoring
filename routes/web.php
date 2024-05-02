<?php


use Illuminate\Support\Facades\Route;

Route::group([
    'prefix'     => config('moonshine.route.prefix'),
    'as'         => 'moonshine.',
    'middleware' => [config('moonshine.auth.middleware'), 'web'],
], function () {
    Route::get('monitoring', [\Zhandos717\MoonshineMonitoring\Controllers\MonitoringController::class, 'index'])
        ->name(
            'monitoring.index'
        );
});
