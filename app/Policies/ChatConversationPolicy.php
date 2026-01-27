<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ChatConversation;
use Illuminate\Auth\Access\HandlesAuthorization;

class ChatConversationPolicy
{
    use HandlesAuthorization;

    /**
     * Todos los roles del panel pueden ver conversaciones
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'supervisor', 'operador', 'super_admin']);
    }

    public function view(User $user, ChatConversation $conversation): bool
    {
        return in_array($user->role, ['admin', 'supervisor', 'operador', 'super_admin']);
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'supervisor', 'super_admin']);
    }

    public function update(User $user, ChatConversation $conversation): bool
    {
        return in_array($user->role, ['admin', 'supervisor', 'operador', 'super_admin']);
    }

    public function delete(User $user, ChatConversation $conversation): bool
    {
        return in_array($user->role, ['admin', 'super_admin']);
    }

    public function deleteAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'super_admin']);
    }
}
