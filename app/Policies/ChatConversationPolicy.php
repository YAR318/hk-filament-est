<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ChatConversation;
use Illuminate\Auth\Access\HandlesAuthorization;

class ChatConversationPolicy
{
    use HandlesAuthorization;

    /**
     * Verificar si el usuario puede ver la lista de conversaciones.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('ver_mensajes') || $user->can('ver_todos_mensajes');
    }

    /**
     * Verificar si el usuario puede ver una conversación.
     */
    public function view(User $user, ChatConversation $conversation): bool
    {
        return $user->can('ver_mensajes') || $user->can('ver_todos_mensajes');
    }

    /**
     * Verificar si el usuario puede crear conversaciones (iniciar chat).
     */
    public function create(User $user): bool
    {
        return $user->can('responder_mensajes');
    }

    /**
     * Verificar si el usuario puede actualizar una conversación.
     */
    public function update(User $user, ChatConversation $conversation): bool
    {
        return $user->can('responder_mensajes');
    }

    /**
     * Verificar si el usuario puede eliminar una conversación.
     */
    public function delete(User $user, ChatConversation $conversation): bool
    {
        return $user->can('eliminar_mensajes');
    }

    /**
     * Verificar si el usuario puede eliminar múltiples conversaciones.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('eliminar_mensajes');
    }
}