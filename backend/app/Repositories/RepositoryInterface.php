<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

interface RepositoryInterface
{
    public function all(): Collection;

    public function paginate(int $perPage = 15, array $filters = ['*']): LengthAwarePaginator;

    public function create(array $attributes): Model;

    public function update(Model $model, array $attributes): ?Model;

    public function delete(Model $model): bool;

    public function find(int $id): ?Model;

    public function findOrFail(int $id): Model;

    public function findBy(string $field, $value): ?Model;

    public function getModel(): Model;
}
