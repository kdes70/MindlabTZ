<?php

namespace App\Repositories;


use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use JsonException;

class ProductRepository extends BaseRepository
{
    /**
     * Время кеширования данных в секундах
     */
    public const CACHE_TTL = 3600; // 1 час

    /**
     * Получить все товары с пагинацией
     * @throws JsonException
     */
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $cacheKey = 'products_' . md5(json_encode($filters, JSON_THROW_ON_ERROR)) . '_' . $perPage . '_' . request('page', 1);

        return Cache::remember($cacheKey, self::CACHE_TTL, static function () use ($perPage, $filters) {
            $query = Product::with('category');

            //TODO: Применяем упрощенный вариант фильтрации(Да да не SOLID)
            if (isset($filters['category_id'])) {
                $query->where('category_id', $filters['category_id']);
            }

            if (isset($filters['name'])) {
                $query->where('name', 'like', '%' . $filters['name'] . '%');
            }

            if (isset($filters['price_from'])) {
                $query->where('price', '>=', $filters['price_from']);
            }

            if (isset($filters['price_to'])) {
                $query->where('price', '<=', $filters['price_to']);
            }

            return $query->paginate($perPage);
        });
    }

    public function find(int $id): ?Product
    {
        return Cache::remember('product_' . $id, self::CACHE_TTL,
            static fn() => Product::with('category')->find($id));
    }

    public function create(array $attributes): Product
    {
        $product = Product::create($attributes);
        $this->clearCache();
        return $product;
    }

    /**
     * Обновить товар
     */
    public function update(Product|Model $model, array $attributes): ?Product
    {
        $data = array_filter($attributes, static function ($value) {
            return !is_null($value);
        });

        $model->update($data);
        $this->clearCache($model->id);

        return $model->fresh();
    }

    /**
     * Удалить товар
     */
    public function delete(Product|Model $model): bool
    {
        $result = $model->delete();
        $this->clearCache($model->id);

        return $result;
    }

    /**
     * Очистить кеш товаров
     */
    private function clearCache(int $id = null): void
    {
        if ($id) {
            Cache::forget('product_' . $id);
        }

        // Очищаем все кеши для списков товаров
        $keys = Cache::get('product_list_cache_keys', []);
        foreach ($keys as $key) {
            Cache::forget($key);
        }

        // Сбрасываем список ключей
        Cache::put('product_list_cache_keys', [], self::CACHE_TTL);
    }
}
