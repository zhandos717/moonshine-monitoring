<?php

namespace Zhandos717\MoonshineMonitoring\Controllers;

use Exception;
use MoonShine\Http\Controllers\MoonShineController;
use MoonShine\MoonShineRequest;
use Zhandos717\MoonshineMonitoring\Models\MonitoringRecord;

class MonitoringController extends MoonShineController
{
    /**
     * @throws Exception
     */
    public function index(MoonShineRequest $request)
    {
        // Get recent monitoring records
        $records = MonitoringRecord::orderBy('created_at', 'desc')->limit(100)->get();
        
        return response()->json([
            'records' => $records,
            'status' => 'success'
        ]);
    }
}
