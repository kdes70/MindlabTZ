<?php

namespace App\Repositories;

use App\Models\Sale;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use JsonException;
use RuntimeException;
use Throwable;

class SaleRepository extends BaseRepository
{
    public const CACHE_TTL = 3600; // 1 час

    /**
     * @throws JsonException
     */
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $cacheKey = 'sales_' . md5(json_encode($filters, JSON_THROW_ON_ERROR)) . '_' . $perPage . '_' . request('page', 1);

        return Cache::remember($cacheKey, self::CACHE_TTL, static function () use ($perPage, $filters) {
            $query = Sale::with(['product', 'client']);

            if (isset($filters['product_id'])) {
                $query->where('product_id', $filters['product_id']);
            }

            if (isset($filters['client_id'])) {
                $query->where('client_id', $filters['client_id']);
            }

            if (isset($filters['date_from'])) {
                $query->where('sale_date', '>=', $filters['date_from']);
            }

            if (isset($filters['date_to'])) {
                $query->where('sale_date', '<=', $filters['date_to']);
            }

            return $query->paginate($perPage);
        });
    }

    /**
     * Найти продажу по ID
     */
    public function findById(int $id): ?Sale
    {
        return Cache::remember('sale_' . $id, self::CACHE_TTL,
            static fn() => Sale::with(['product', 'client'])->find($id));
    }

    /**
     * @throws Throwable
     */
    public function create(array $attributes): Sale
    {
        // Начинаем транзакцию для атомарного обновления
        return DB::transaction(function () use ($attributes) {
            // Уменьшаем количество товара на складе
            $product = DB::table('products')->where('id', $attributes['product_id'])->first();

            if ($product->quantity_in_stock < $attributes['quantity']) {
                throw new RuntimeException('Недостаточное количество товара на складе');
            }

            DB::table('products')
                ->where('id', $attributes['product_id'])
                ->decrement('quantity_in_stock', $attributes['quantity']);

            // Создаем запись о продаже
            $sale = Sale::create($attributes);

            // Очищаем кеш
            $this->clearCache();

            return $sale;
        });
    }

    /**
     * @throws Throwable
     */
    public function update(Sale|Model $model, array $attributes): ?Sale
    {
        // Начинаем транзакцию для атомарного обновления
        return DB::transaction(function () use ($model, $attributes) {
            $sale = Sale::find($model->id);

            if (!$sale) {
                return null;
            }

            // Если изменилось количество, обновляем количество товара на складе
            if (isset($attributes['quantity']) && $attributes['quantity'] !== $sale->quantity) {
                $diff = $attributes['quantity'] - $sale->quantity;

                if ($diff > 0) {
                    // Проверяем наличие товара для увеличения количества продажи
                    $product = DB::table('products')->where('id', $sale->product_id)->first();

                    if ($product->quantity_in_stock < $diff) {
                        throw new RuntimeException('Недостаточное количество товара на складе');
                    }

                    // Уменьшаем количество товара на складе
                    DB::table('products')
                        ->where('id', $sale->product_id)
                        ->decrement('quantity_in_stock', $diff);
                } else {
                    // Увеличиваем количество товара на складе
                    DB::table('products')
                        ->where('id', $sale->product_id)
                        ->increment('quantity_in_stock', abs($diff));
                }
            }

            $sale->update($attributes);
            $this->clearCache($model->id);

            return $sale;
        });
    }

    /**
     * @throws Throwable
     */
    public function delete(Sale|Model $model): bool
    {
        // Начинаем транзакцию для атомарного обновления
        return DB::transaction(function () use ($model) {
            $sale = Sale::find($model->id);

            if (!$sale) {
                return false;
            }

            // Возвращаем количество товара на склад
            DB::table('products')
                ->where('id', $sale->product_id)
                ->increment('quantity_in_stock', $sale->quantity);

            $result = $sale->delete();
            $this->clearCache($model->id);

            return $result;
        });
    }

    /**
     * Получить статистику продаж
     * @throws JsonException
     */
    public function getStatistics(array $filters = []): array
    {
        $cacheKey = 'sales_statistics_' . md5(json_encode($filters, JSON_THROW_ON_ERROR));

        return Cache::remember($cacheKey, self::CACHE_TTL, static function () use ($filters) {
            $query = DB::table('sales')
                ->join('products', 'sales.product_id', '=', 'products.id')
                ->select(
                    DB::raw('COUNT(sales.id) as total_sales'),
                    DB::raw('SUM(sales.amount) as total_amount'),
                    DB::raw('AVG(sales.amount) as avg_amount')
                );

            if (isset($filters['date_from'])) {
                $query->where('sales.sale_date', '>=', $filters['date_from']);
            }

            if (isset($filters['date_to'])) {
                $query->where('sales.sale_date', '<=', $filters['date_to']);
            }

            if (isset($filters['product_id'])) {
                $query->where('sales.product_id', $filters['product_id']);
            }

            if (isset($filters['client_id'])) {
                $query->where('sales.client_id', $filters['client_id']);
            }

            $result = $query->first();

            // Добавляем топ продаваемых товаров
            $topProducts = DB::table('sales')
                ->join('products', 'sales.product_id', '=', 'products.id')
                ->select(
                    'products.id',
                    'products.name',
                    DB::raw('SUM(sales.quantity) as total_quantity'),
                    DB::raw('SUM(sales.amount) as total_amount')
                )
                ->groupBy('products.id', 'products.name')
                ->orderByDesc('total_quantity')
                ->limit(5)
                ->get();

            return [
                'summary' => $result,
                'top_products' => $topProducts
            ];
        });
    }

    /**
     * Очистить кеш продаж
     */
    private function clearCache(int $id = null): void
    {
        if ($id) {
            Cache::forget('sale_' . $id);
        }

        // Очищаем все кеши для списков продаж
        $keys = Cache::get('sale_list_cache_keys', []);
        foreach ($keys as $key) {
            Cache::forget($key);
        }

        // Очищаем кеш статистики
        Cache::forget('sales_statistics');

        // Сбрасываем список ключей
        Cache::put('sale_list_cache_keys', [], self::CACHE_TTL);
    }
}
