<?php

namespace Zhandos717\MoonshineMonitoring\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use MoonShine\Laravel\Http\Controllers\MoonShineController;
use MoonShine\Laravel\MoonShineRequest;
use Zhandos717\MoonshineMonitoring\Models\MonitoringRecord;

class MonitoringController extends MoonShineController
{
    /**
     * @throws Exception
     */
    public function index(MoonShineRequest $request): View|JsonResponse
    {
        // Если это AJAX запрос, возвращаем JSON данные
        if ($request->ajax()) {
            // Get recent monitoring records
            $records = MonitoringRecord::query()
                ->orderBy('created_at', 'desc')
                ->limit(100)->get();

            return response()->json([
                'records' => $records,
                'status'  => 'success',
            ]);
        }
        
        // Для обычного запроса возвращаем представление
        return view('moonshine-monitoring::monitoring');
    }
}
