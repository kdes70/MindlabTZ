<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    private ProductService $service;

    /**
     * Конструктор с внедрением зависимости сервиса
     */
    public function __construct(ProductService $service)
    {
        parent::__construct();

        $this->service = $service;
        $this->middleware('auth:sanctum');
        $this->middleware('permission:view_products')->only(['index', 'show']);
        $this->middleware('permission:create_products')->only(['store']);
        $this->middleware('permission:edit_products')->only(['update']);
        $this->middleware('permission:delete_products')->only(['destroy']);
    }

    /**
     * Получить список товаров с пагинацией
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['category_id', 'name', 'price_from', 'price_to']);
        $perPage = $request->input('per_page', 15);

        $products = $this->service->paginate($perPage, $filters);

        return response()->json($products);
    }

    /**
     * Получить товар по ID
     */
    public function show(int $id): JsonResponse
    {
        $product = $this->service->find($id);

        if (!$product) {
            return response()->json(['message' => 'Товар не найден'], 404);
        }

        return response()->json($product);
    }

    /**
     * Создать новый товар
     */
    public function store(ProductRequest $request): JsonResponse
    {
        try {
            $product = $this->service->createProduct($request->validated());

            return response()->json($product, 201);
        } catch (\Exception $e) {
            Log::error('Ошибка при создании товара: ' . $e->getMessage());
            return response()->json(['message' => 'Ошибка при создании товара'], 500);
        }
    }

    /**
     * Обновить товар
     */
    public function update(ProductRequest $request, int $id): JsonResponse
    {
        try {
            $product = $this->service->updateProduct($id, $request->validated());

            if (!$product) {
                return response()->json(['message' => 'Товар не найден'], 404);
            }

            return response()->json($product);
        } catch (\Exception $e) {
            Log::error('Ошибка при обновлении товара: ' . $e->getMessage());
            return response()->json(['message' => 'Ошибка при обновлении товара'], 500);
        }
    }

    /**
     * Удалить товар
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $result = $this->service->deleteProduct($id);

            if (!$result) {
                return response()->json(['message' => 'Товар не найден'], 404);
            }

            return response()->json(['message' => 'Товар успешно удален']);
        } catch (\Exception $e) {
            Log::error('Ошибка при удалении товара: ' . $e->getMessage());
            return response()->json(['message' => 'Ошибка при удалении товара'], 500);
        }
    }

    /**
     * Импорт товаров из внешнего источника
     */
    public function import(Request $request): JsonResponse
    {
        $request->validate([
            'source_url' => 'required|url'
        ]);

        try {
            $this->service->importProductsFromExternalSource($request->input('source_url'));

            return response()->json(['message' => 'Задача на импорт товаров успешно добавлена в очередь']);
        } catch (\Exception $e) {
            Log::error('Ошибка при добавлении задачи импорта: ' . $e->getMessage());
            return response()->json(['message' => 'Ошибка при добавлении задачи импорта'], 500);
        }
    }
}
