<?php

namespace App\Policies;

use App\Models\User;
use App\Models\BlockedPhone;
use Illuminate\Auth\Access\HandlesAuthorization;

class BlockedPhonePolicy
{
    use HandlesAuthorization;

    /**
     * Verificar si el usuario puede ver la lista de teléfonos bloqueados.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('ver_telefonos_bloqueados');
    }

    /**
     * Verificar si el usuario puede ver un teléfono bloqueado específico.
     */
    public function view(User $user, BlockedPhone $blockedPhone): bool
    {
        return $user->can('ver_telefonos_bloqueados');
    }

    /**
     * Verificar si el usuario puede crear (bloquear) teléfonos.
     */
    public function create(User $user): bool
    {
        return $user->can('bloquear_telefonos');
    }

    /**
     * Verificar si el usuario puede actualizar un registro (aunque usualmente solo se crean/eliminan).
     */
    public function update(User $user, BlockedPhone $blockedPhone): bool
    {
        return $user->can('bloquear_telefonos');
    }

    /**
     * Verificar si el usuario puede eliminar (desbloquear) un teléfono.
     */
    public function delete(User $user, BlockedPhone $blockedPhone): bool
    {
        return $user->can('desbloquear_telefonos');
    }

    /**
     * Verificar si el usuario puede eliminar múltiples teléfonos.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('desbloquear_telefonos');
    }
}