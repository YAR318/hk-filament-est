<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Operator;
use Illuminate\Auth\Access\HandlesAuthorization;

class OperatorPolicy
{
    use HandlesAuthorization;

    /**
     * Verificar si el usuario puede ver la lista de operadores.
     */
    public function viewAny(User $user): bool
    {
        // Asumiendo que ver usuarios permite ver operadores también
        return $user->can('ver_usuarios');
    }

    /**
     * Verificar si el usuario puede ver un operador específico.
     */
    public function view(User $user, Operator $operator): bool
    {
        return $user->can('ver_usuarios');
    }

    /**
     * Verificar si el usuario puede crear operadores.
     */
    public function create(User $user): bool
    {
        return $user->can('crear_usuarios');
    }

    /**
     * Verificar si el usuario puede actualizar un operador.
     */
    public function update(User $user, Operator $operator): bool
    {
        return $user->can('editar_usuarios');
    }

    /**
     * Verificar si el usuario puede eliminar un operador.
     */
    public function delete(User $user, Operator $operator): bool
    {
        return $user->can('eliminar_usuarios');
    }

    /**
     * Verificar si el usuario puede eliminar múltiples operadores.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('eliminar_usuarios');
    }
}