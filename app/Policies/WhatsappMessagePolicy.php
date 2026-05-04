<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WhatsappMessage;
use Illuminate\Auth\Access\HandlesAuthorization;

class WhatsappMessagePolicy
{
    use HandlesAuthorization;

    /**
     * Verificar si el usuario puede ver la lista de mensajes.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('ver_mensajes') || $user->can('ver_todos_mensajes');
    }

    /**
     * Verificar si el usuario puede ver un mensaje.
     */
    public function view(User $user, WhatsappMessage $message): bool
    {
        return $user->can('ver_mensajes') || $user->can('ver_todos_mensajes');
    }

    /**
     * Verificar si el usuario puede crear mensajes (internamente).
     */
    public function create(User $user): bool
    {
        return $user->can('responder_mensajes');
    }

    /**
     * Verificar si el usuario puede actualizar un mensaje (responder).
     */
    public function update(User $user, WhatsappMessage $message): bool
    {
        return $user->can('responder_mensajes');
    }

    /**
     * Verificar si el usuario puede eliminar un mensaje.
     */
    public function delete(User $user, WhatsappMessage $message): bool
    {
        return $user->can('eliminar_mensajes');
    }

    /**
     * Verificar si el usuario puede eliminar múltiples mensajes.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('eliminar_mensajes');
    }
}