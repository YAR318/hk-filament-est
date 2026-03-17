<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Verificar si el usuario puede ver la lista de usuarios.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('ver_usuarios');
    }

    /**
     * Verificar si el usuario puede ver un usuario específico.
     */
    public function view(User $user, User $model): bool
    {
        return $user->can('ver_usuarios');
    }

    /**
     * Verificar si el usuario puede crear usuarios.
     */
    public function create(User $user): bool
    {
        return $user->can('crear_usuarios');
    }

    /**
     * Verificar si el usuario puede actualizar un usuario.
     */
    public function update(User $user, User $model): bool
    {
        return $user->can('editar_usuarios');
    }

    /**
     * Verificar si el usuario puede eliminar un usuario.
     */
    public function delete(User $user, User $model): bool
    {
        return $user->can('eliminar_usuarios');
    }

    /**
     * Verificar si el usuario puede eliminar múltiples usuarios.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('eliminar_usuarios');
    }
}