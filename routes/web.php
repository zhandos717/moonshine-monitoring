<?php

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use Zhandos717\MoonshineMonitoring\Controllers\MonitoringController;

Route::moonshine(static function (Router $router): void {
    // withAuthenticate макроса перекрывается middleware группы по умолчанию, поэтому авторизация задаётся явно
    $router->middleware(moonshineConfig()->getAuthMiddleware())->group(static function (Router $router): void {
        $router->get('monitoring/data', [MonitoringController::class, 'data'])->name('monitoring.data');
    });
});
