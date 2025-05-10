<?php

namespace App\Http\Middleware;

use App\Services\ActiveLogService;
use Closure;
use Exception;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ActiveLogRequest extends Middleware
{
    protected array $excludedPaths = [
        'api/logs', // Исключаем запросы к API логов для избежания рекурсии
    ];

    public function __construct(protected ActiveLogService $logService)
    {
    }

    public function handle(Request $request, Closure $next)
    {
        // Проверяем, не является ли текущий путь исключением
        foreach ($this->excludedPaths as $path) {
            if (Str::startsWith($request->path(), $path)) {
                return $next($request);
            }
        }

        // Запоминаем время начала запроса
        $startTime = microtime(true);

        // Обрабатываем запрос
        $response = $next($request);

        // Логируем только если запрос относится к API
        if (Str::startsWith($request->path(), 'api')) {
            $action = $this->determineAction($request);
            $entityType = $this->determineEntityType($request);
            $entityId = $this->determineEntityId($request);

            // Получаем данные из ответа
            $responseData = null;
            if ($response->getContent()) {
                try {
                    $responseData = json_decode($response->getContent(), true);
                } catch (Exception $e) {
                    $responseData = ['error' => 'Could not decode response content'];
                }
            }

            // Определяем уровень логирования
            $statusCode = $response->getStatusCode();
            $level = $statusCode >= 400 ? ($statusCode >= 500 ? 'error' : 'warning') : 'info';

            // Логируем запрос
            $this->logService->log(
                $request,
                $action,
                $entityType,
                $entityId,
                $responseData,
                $level
            );
        }

        return $response;
    }

    protected function determineAction(Request $request): string
    {
        $method = $request->method();
        $path = $request->path();

        if (Str::contains($path, 'login')) {
            return 'login';
        } elseif (Str::contains($path, 'logout')) {
            return 'logout';
        } elseif (Str::contains($path, 'register')) {
            return 'register';
        }

        return match ($method) {
            'GET' => Str::contains($path, '/') && is_numeric(Str::afterLast($path, '/')) ? 'view' : 'list',
            'POST' => 'create',
            'PUT', 'PATCH' => 'update',
            'DELETE' => 'delete',
            default => 'other',
        };
    }

    protected function determineEntityType(Request $request): ?string
    {
        $path = $request->path();

        $pathParts = explode('/', $path);
        if (count($pathParts) >= 2) {
            return Str::singular($pathParts[1]);
        }

        return null;
    }

    protected function determineEntityId(Request $request): ?int
    {
        $path = $request->path();

        $pathParts = explode('/', $path);
        if (count($pathParts) >= 3 && is_numeric($pathParts[2])) {
            return (int)$pathParts[2];
        }

        return null;
    }
}
