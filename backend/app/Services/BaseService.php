<?php

namespace App\Services;

use App\Providers\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class BaseService
{
    public function __construct(protected BaseRepository $repository)
    {
    }

    public function all(): Collection
    {
        return $this->repository->all();
    }

    public function paginate($perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage);
    }

    public function create(array $data): Model
    {
        return $this->repository->create($data);
    }

    public function update($id, array $data): bool
    {
        $model = $this->repository->findOrFail($id);
        return $this->repository->update($model, $data);
    }

    public function delete($id): bool
    {
        $model = $this->repository->findOrFail($id);
        return $this->repository->delete($model);
    }

    public function find($id): ?Model
    {
        return $this->repository->find($id);
    }

    public function findOrFail($id): Model
    {
        return $this->repository->findOrFail($id);
    }
}
