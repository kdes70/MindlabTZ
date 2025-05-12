<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ActiveLogService;
use App\Services\BaseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

abstract class BaseApiController extends Controller
{
    protected JsonResource $resourceClass;
    protected array $collectionClass;
    protected string $entityType;
    protected array $validationRules = [];

    public function __construct(protected BaseService $service, protected ActiveLogService $logService)
    {
        parent::__construct();
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = $request->get('per_page', 15);
        $items = $this->service->paginate($perPage);

        if ($this->collectionClass) {
            return new $this->collectionClass($items);
        }

        return $this->resourceClass::collection($items);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate($this->getValidationRules('store'));

        $item = $this->service->create($validated);

        $this->logService->log(
            $request,
            'create',
            $this->entityType,
            $item->id,
            ['id' => $item->id]
        );

        return new $this->resourceClass($item);
    }

    public function show($id): JsonResponse
    {
        $item = $this->service->findOrFail($id);

        return new $this->resourceClass($item);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $validated = $request->validate($this->getValidationRules('update', $id));

        $this->service->update($id, $validated);
        $item = $this->service->findOrFail($id);

        $this->logService->log(
            $request,
            'update',
            $this->entityType,
            $id,
            ['id' => $id]
        );

        return new $this->resourceClass($item);
    }

    public function destroy(Request $request, $id): JsonResponse
    {
        $item = $this->service->findOrFail($id);
        $this->service->delete($id);

        $this->logService->log(
            $request,
            'delete',
            $this->entityType,
            $id,
            ['id' => $id]
        );

        return response()->json(['message' => 'Запись успешно удалена']);
    }

    protected function getValidationRules(string $action, $id = null): array
    {
        return $this->validationRules[$action] ?? [];
    }
}
