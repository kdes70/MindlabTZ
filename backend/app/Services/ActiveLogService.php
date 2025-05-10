<?php

namespace App\Services;

use App\Models\ActiveLog;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class ActiveLogService
{
    public function log(
        Request $request,
                $action,
                $entityType = null,
                $entityId = null,
                $responseData = null,
                $level = 'info'
    ): ActiveLog
    {
        $requestData = $this->sanitizeRequestData($request->all());

        return ActiveLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'request_data' => $requestData,
            'response_data' => $responseData,
            'level' => $level,
        ]);
    }

    public function getLogsFiltered(array $filters = [], $perPage = 15): LengthAwarePaginator
    {
        $query = ActiveLog::query();

        foreach (['user_id', 'action', 'entity_type', 'entity_id', 'level'] as $field) {
            if (isset($filters[$field])) {
                $query->where($field, $filters[$field]);
            }
        }

        if (isset($filters['date_from']) || isset($filters['date_to'])) {
            $query->whereBetween('created_at', [
                $filters['date_from'] ?? '1970-01-01',
                $filters['date_to'] ?? now(),
            ]);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    // Добавим простым способом немного безопасности
    private function sanitizeRequestData($data): array
    {
        // Удаляем конфиденциальные данные перед логированием
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = $this->sanitizeRequestData($value);
            } elseif (in_array($key, ['password', 'password_confirmation'], true)) {
                $data[$key] = '********';
            }
        }

        return $data;
    }

}
