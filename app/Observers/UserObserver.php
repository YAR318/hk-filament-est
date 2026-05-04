<?php

namespace App\Observers;

use App\Models\User;
use App\Models\Operator;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        $this->syncOperator($user);
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        // Solo sincronizar si cambió el rol, nombre o email
        if ($user->isDirty('role') || $user->isDirty('name') || $user->isDirty('email')) {
            $this->syncOperator($user);
        }
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        // Si el usuario tenía un operador asociado, eliminarlo también
        Operator::where('email', $user->email)->delete();
    }

    /**
     * Sincronizar User con Operator
     * SOLO los usuarios con rol 'operador' deben estar en la tabla operators
     */
    protected function syncOperator(User $user): void
    {
        // Solo crear/actualizar operador si el rol es exactamente 'operador'
        // Los admins y supervisors NO deben estar en la tabla operators
        if ($user->role === 'operador') {
            // Generar un teléfono único si el usuario no tiene uno
            $phoneNumber = $user->phone ?? 'temp_' . $user->id . '_' . time();

            Operator::updateOrCreate(
                ['email' => $user->email],
                [
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone_number' => $phoneNumber,
                    'role' => 'operador',
                    'is_active' => true,
                    'status' => 'available',
                    'max_concurrent_chats' => 5,
                    'current_chats_count' => 0,
                ]
            );
        } else {
            // Si el rol cambió a algo que no es operador, eliminar de operators
            Operator::where('email', $user->email)->delete();
        }
    }
}
