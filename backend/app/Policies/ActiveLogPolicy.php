<?php

namespace App\Policies;

use App\Models\ActiveLog;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ActiveLogPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasPermission('view_any_active_log');
    }

    public function view(User $user, ActiveLog $activeLog): bool
    {
        return $user->hasPermission('view_active_log') || $user->id === $activeLog->user_id;
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('create_active_log');
    }

    public function update(User $user, ActiveLog $activeLog): bool
    {
        return $user->hasPermission('update_active_log') || $user->id === $activeLog->user_id;
    }

    public function delete(User $user, ActiveLog $activeLog): bool
    {
        return $user->hasPermission('delete_active_log') || $user->id === $activeLog->user_id;
    }

    public function restore(User $user, ActiveLog $activeLog): bool
    {
        return $user->hasPermission('restore_active_log') || $user->id === $activeLog->user_id;
    }

    public function forceDelete(User $user, ActiveLog $activeLog): bool
    {
        return $user->hasPermission('force_delete_active_log') || $user->id === $activeLog->user_id;
    }
}
