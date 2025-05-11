<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActiveLog;
use App\Services\ActiveLogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ActiveLogController extends Controller
{

    public function __construct(protected ActiveLogService $logService)
    {
        parent::__construct();
    }

    public function index(Request $request): JsonResponse
    {
        // Проверка доступа с помощью политики ActiveLogPolicy
        $this->authorize('viewAny', ActiveLog::class);

        $filters = $request->only([
            'user_id',
            'action',
            'entity_type',
            'entity_id',
            'level',
            'date_from',
            'date_to',
        ]);

        $perPage = $request->get('per_page', 15);

        $logs = $this->logService->getLogsFiltered($filters, $perPage);

        return response()->json($logs);
    }
}
