<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Operator;
use Illuminate\Auth\Access\HandlesAuthorization;

class OperatorPolicy
{
    use HandlesAuthorization;

    /**
     * Supervisor y admin pueden ver operadores
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'supervisor', 'super_admin']);
    }

    public function view(User $user, Operator $operator): bool
    {
        return in_array($user->role, ['admin', 'supervisor', 'super_admin']);
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'super_admin']);
    }

    public function update(User $user, Operator $operator): bool
    {
        return in_array($user->role, ['admin', 'supervisor', 'super_admin']);
    }

    public function delete(User $user, Operator $operator): bool
    {
        return in_array($user->role, ['admin', 'super_admin']);
    }

    public function deleteAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'super_admin']);
    }
}
