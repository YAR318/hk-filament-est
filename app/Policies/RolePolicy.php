<?php

namespace App\Policies;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Auth\Access\HandlesAuthorization;

class RolePolicy
{
    use HandlesAuthorization;

    /**
     * Solo admin puede gestionar roles
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'super_admin']);
    }

    public function view(User $user, Role $role): bool
    {
        return in_array($user->role, ['admin', 'super_admin']);
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'super_admin']);
    }

    public function update(User $user, Role $role): bool
    {
        return in_array($user->role, ['admin', 'super_admin']);
    }

    public function delete(User $user, Role $role): bool
    {
        return in_array($user->role, ['admin', 'super_admin']);
    }

    public function deleteAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'super_admin']);
    }
}
