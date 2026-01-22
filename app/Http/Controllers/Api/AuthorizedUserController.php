<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AuthorizedUser;
use Illuminate\Http\Request;

class AuthorizedUserController extends Controller
{
    /**
     * Verificar si un usuario está autorizado para usar el bot
     */
    public function check($phoneNumber)
    {
        // Limpiar el número (remover @s.whatsapp.net si viene)
        $cleanNumber = str_replace('@s.whatsapp.net', '', $phoneNumber);
        $cleanNumber = preg_replace('/[^0-9]/', '', $cleanNumber);

        // Buscar usuario autorizado
        $user = AuthorizedUser::where('phone_number', $cleanNumber)->first();

        // Si no existe, no está autorizado
        if (!$user) {
            return response()->json([
                'authorized' => false,
                'reason' => 'Usuario no registrado'
            ]);
        }

        // Verificar si está activo
        if (!$user->is_active) {
            return response()->json([
                'authorized' => false,
                'reason' => 'Usuario inactivo'
            ]);
        }

        // Verificar límites usando el método del modelo
        if (!$user->canSendMessage()) {
            return response()->json([
                'authorized' => false,
                'reason' => 'Límite de mensajes alcanzado'
            ]);
        }

        // Usuario autorizado
        return response()->json([
            'authorized' => true,
            'user' => [
                'name' => $user->name,
                'phone_number' => $user->phone_number,
                'company' => $user->company,
                'daily_limit' => $user->daily_message_limit,
                'hourly_limit' => $user->hourly_message_limit
            ]
        ]);
    }

    /**
     * Actualizar timestamp del último mensaje
     */
    public function updateLastMessage(Request $request)
    {
        $phoneNumber = $request->input('phone_number');
        
        // Limpiar el número
        $cleanNumber = preg_replace('/[^0-9]/', '', $phoneNumber);

        $user = AuthorizedUser::where('phone_number', $cleanNumber)->first();

        if ($user) {
            $user->last_message_at = now();
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Timestamp actualizado'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Usuario no encontrado'
        ], 404);
    }
}
