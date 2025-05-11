<?php

namespace App\Services;

use App\Models\Sale;
use App\Repositories\SaleRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Event;

class SaleService
{
    private SaleRepository $repository;

    public function __construct(SaleRepository $repository)
    {
        $this->repository = $repository;
    }


    public function getSales(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage, $filters);
    }

    /**
     * Получить продажу по ID
     */
    public function getSaleById(int $id): ?Sale
    {
        return $this->repository->findById($id);
    }

    /**
     * Создать новую продажу
     */
    public function createSale(array $data): Sale
    {
        // Если не указано, устанавливаем дату продажи как текущую
        if (!isset($data['sale_date'])) {
            $data['sale_date'] = now();
        }

        // Если не указана сумма, вычисляем на основе цены товара
        if (!isset($data['amount'])) {
            $product = \App\Models\Product::findOrFail($data['product_id']);
            $data['amount'] = $product->price * $data['quantity'];
        }

        $sale = $this->repository->create($data);

        // Отправка события о создании продажи
        Event::dispatch('sale.created', $sale);

        return $sale;
    }

    /**
     * Обновить существующую продажу
     */
    public function updateSale(int $id, array $data): ?Sale
    {
        $sale = $this->repository->update($id, $data);

        if ($sale) {
            // Отправка события об обновлении продажи
            Event::dispatch('sale.updated', $sale);
        }

        return $sale;
    }

    /**
     * Удалить продажу
     */
    public function deleteSale(int $id): bool
    {
        $sale = $this->repository->findById($id);

        if (!$sale) {
            return false;
        }

        $result = $this->repository->delete($id);

        if ($result) {
            // Отправка события об удалении продажи
            Event::dispatch('sale.deleted', $sale);
        }

        return $result;
    }

    /**
     * Получить статистику продаж
     */
    public function getStatistics(array $filters = []): array
    {
        return $this->repository->getStatistics($filters);
    }
}
