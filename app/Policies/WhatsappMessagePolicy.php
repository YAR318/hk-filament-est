<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WhatsappMessage;
use Illuminate\Auth\Access\HandlesAuthorization;

class WhatsappMessagePolicy
{
    use HandlesAuthorization;

    /**
     * Supervisor y admin pueden ver logs de WhatsApp
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'supervisor', 'super_admin']);
    }

    public function view(User $user, WhatsappMessage $message): bool
    {
        return in_array($user->role, ['admin', 'supervisor', 'super_admin']);
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'super_admin']);
    }

    public function update(User $user, WhatsappMessage $message): bool
    {
        return in_array($user->role, ['admin', 'super_admin']);
    }

    public function delete(User $user, WhatsappMessage $message): bool
    {
        return in_array($user->role, ['admin', 'super_admin']);
    }

    public function deleteAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'super_admin']);
    }
}
