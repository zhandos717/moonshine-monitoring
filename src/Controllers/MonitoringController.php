<?php

namespace Zhandos717\MoonshineMonitoring\Controllers;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Str;
use MoonShine\Http\Controllers\MoonShineController;
use MoonShine\MoonShineRequest;

class MonitoringController extends MoonShineController
{
    /**
     * @throws Exception
     */
    public function index(MoonShineRequest $request, ?string $file = null): array
    {
        $viewer = Str::of(PHP_OS)->lower();

        dd($viewer);

        return [

        ];
    }
}
