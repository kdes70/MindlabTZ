<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaleRequest;
use App\Services\SaleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SaleController extends Controller
{
    private SaleService $service;

    /**
     * Конструктор с внедрением зависимости сервиса
     */
    public function __construct(SaleService $service)
    {
        parent::__construct();

        $this->service = $service;
        $this->middleware('permission:view_sales')->only(['index', 'show', 'statistics']);
        $this->middleware('permission:create_sales')->only(['store']);
        $this->middleware('permission:edit_sales')->only(['update']);
        $this->middleware('permission:delete_sales')->only(['destroy']);
    }

    /**
     * Получить список продаж с пагинацией
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['product_id', 'client_id', 'date_from', 'date_to']);
        $perPage = $request->input('per_page', 15);

        $sales = $this->service->getSales($filters, $perPage);

        return response()->json($sales);
    }

    /**
     * Получить продажу по ID
     */
    public function show(int $id): JsonResponse
    {
        $sale = $this->service->getSaleById($id);

        if (!$sale) {
            return response()->json(['message' => 'Продажа не найдена'], 404);
        }

        return response()->json($sale);
    }

    /**
     * Создать новую продажу
     */
    public function store(SaleRequest $request): JsonResponse
    {
        try {
            $sale = $this->service->createSale($request->validated());

            return response()->json($sale, 201);
        } catch (\Exception $e) {
            Log::error('Ошибка при создании продажи: ' . $e->getMessage());
            return response()->json(['message' => 'Ошибка при создании продажи: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Обновить продажу
     */
    public function update(SaleRequest $request, int $id): JsonResponse
    {
        $sale = $this->service->getSaleById($id);

        if (!$sale) {
            return response()->json(['message' => 'Продажа не найдена'], 404);
        }

        try {
            $updatedSale = $this->service->updateSale($id, $request->validated());

            return response()->json($updatedSale);
        } catch (\Exception $e) {
            Log::error('Ошибка при обновлении продажи: ' . $e->getMessage());
            return response()->json(['message' => 'Ошибка при обновлении продажи: ' . $e->getMessage()], 500);
        }
    }
}
