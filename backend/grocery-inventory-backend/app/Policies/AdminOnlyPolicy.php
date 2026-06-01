<?php

namespace App\Policies;

use App\Models\User;

abstract class AdminOnlyPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        return $user->isAdministrator() ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return false;
    }

    public function view(User $user, object $model): bool
    {
        return false;
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, object $model): bool
    {
        return false;
    }

    public function delete(User $user, object $model): bool
    {
        return false;
    }

    public function restore(User $user, object $model): bool
    {
        return false;
    }

    public function forceDelete(User $user, object $model): bool
    {
        return false;
    }

    public function viewMovements(User $user, object $model): bool
    {
        return false;
    }
}
