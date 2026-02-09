<?php

namespace App\Policies;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Auth\Access\HandlesAuthorization;

class RolePolicy
{
    use HandlesAuthorization;

    /**
     * Verificar si el usuario puede ver la lista de roles.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('gestionar_roles');
    }

    /**
     * Verificar si el usuario puede ver un rol específico.
     */
    public function view(User $user, Role $role): bool
    {
        return $user->can('gestionar_roles');
    }

    /**
     * Verificar si el usuario puede crear roles.
     */
    public function create(User $user): bool
    {
        return $user->can('gestionar_roles');
    }

    /**
     * Verificar si el usuario puede actualizar un rol.
     */
    public function update(User $user, Role $role): bool
    {
        return $user->can('gestionar_roles');
    }

    /**
     * Verificar si el usuario puede eliminar un rol.
     */
    public function delete(User $user, Role $role): bool
    {
        return $user->can('gestionar_roles');
    }

    /**
     * Verificar si el usuario puede eliminar múltiples roles.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('gestionar_roles');
    }
}