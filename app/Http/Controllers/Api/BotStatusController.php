<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChatConversation;
use Illuminate\Http\Request;

class BotStatusController extends Controller
{
    /**
     * Verificar si el bot debe responder a este número
     * 
     * @param string $phone
     * @return \Illuminate\Http\JsonResponse
     */
    public function check(string $phone)
    {
        // Limpiar número
        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);

        $conversation = ChatConversation::where('phone_number', $cleanPhone)->first();

        if (!$conversation) {
            // Si no existe conversación, por defecto el bot está activo (o depende de lógica negocio)
            return response()->json([
                'active' => true,
                'reason' => 'new_conversation'
            ]);
        }

        return response()->json([
            'active' => (bool)$conversation->is_bot_active,
            'assigned_to' => $conversation->assigned_to,
            'reason' => $conversation->is_bot_active ? 'bot_enabled' : 'bot_disabled'
        ]);
    }
}