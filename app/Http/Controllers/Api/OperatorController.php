<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Operator;
use Illuminate\Http\Request;

class OperatorController extends Controller
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
        $user = Operator::where('phone_number', $cleanNumber)->first();

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

        // Verificar si puede tomar más chats (adaptado para el nuevo sistema de operadores)
        if (!$user->canTakeMoreChats()) {
            return response()->json([
                'authorized' => false,
                'reason' => 'Operador no disponible para nuevos chats'
            ]);
        }

        // Usuario autorizado
        return response()->json([
            'authorized' => true,
            'user' => [
                'name' => $user->name,
                'phone_number' => $user->phone_number,
                'email' => $user->email,
                'max_concurrent_chats' => $user->max_concurrent_chats,
                'current_chats_count' => $user->current_chats_count,
                'role' => $user->role,
                'status' => $user->status
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

        $user = Operator::where('phone_number', $cleanNumber)->first();

        if ($user) {
            $user->last_activity_at = now();
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
