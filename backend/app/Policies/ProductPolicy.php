<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProductPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasPermission('view_any_product');
    }

    public function view(User $user, Product $product): bool
    {
        return $user->hasPermission('view_product') || $user->id === $product->user_id;
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('create_product');
    }

    public function update(User $user, Product $product): bool
    {
        return $user->hasPermission('update_product') || $user->id === $product->user_id;
    }

    public function delete(User $user, Product $product): bool
    {
        return $user->hasPermission('delete_product') || $user->id === $product->user_id;
    }

    public function restore(User $user, Product $product): bool
    {
        return $user->hasPermission('restore_product') || $user->id === $product->user_id;
    }

    public function forceDelete(User $user, Product $product): bool
    {
        return $user->hasPermission('force_delete_product') || $user->id === $product->user_id;
    }
}
