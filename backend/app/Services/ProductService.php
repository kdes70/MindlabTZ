<?php

namespace App\Services;

use App\Models\Product;
use App\Repositories\ProductRepository;
use App\Repositories\RepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Event;
use JsonException;

class ProductService extends BaseService
{
    public function __construct(protected ProductRepository|RepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    public function getProductById(int $id): ?Product
    {
        return $this->repository->find($id);
    }

    /**
     * Создать новый товар
     */
    public function createProduct(array $data): Product
    {
        $product = $this->repository->create($data);

        // Отправка события о создании товара
        Event::dispatch('product.created', $product);

        return $product;
    }

    /**
     * Обновить существующий товар
     */
    public function updateProduct(int $id, array $data): ?Product
    {
        $product = $this->repository->update($id, $data);

        if ($product) {
            // Отправка события об обновлении товара
            Event::dispatch('product.updated', $product);
        }

        return $product;
    }

    /**
     * Удалить товар
     */
    public function deleteProduct(int $id): bool
    {
        $product = $this->repository->find($id);

        if (!$product) {
            return false;
        }

        $result = $this->repository->delete($id);

        if ($result) {
            // Отправка события об удалении товара
            Event::dispatch('product.deleted', $product);
        }

        return $result;
    }

    /**
     * Импорт товаров из внешнего источника (демонстрация работы с очередями)
     */
    public function importProductsFromExternalSource(string $sourceUrl): void
    {
        // Отправка задачи в очередь
        dispatch(new \App\Jobs\ImportProductsJob($sourceUrl));
    }
}
