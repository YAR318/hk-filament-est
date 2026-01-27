<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ChatHistoryService;
use Illuminate\Http\Request;

class ChatHistoryController extends Controller
{
    protected ChatHistoryService $chatHistoryService;

    public function __construct(ChatHistoryService $chatHistoryService)
    {
        $this->chatHistoryService = $chatHistoryService;
    }

    /**
     * Obtener historial de conversación para LLM
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getHistory(Request $request)
    {
        $phoneNumber = $request->input('phone_number');
        $limit = $request->input('limit', 20);

        if (!$phoneNumber) {
            return response()->json([
                'success' => false,
                'message' => 'phone_number es requerido'
            ], 400);
        }

        $history = $this->chatHistoryService->getHistoryForLLM($phoneNumber, $limit);

        return response()->json([
            'success' => true,
            'phone_number' => $phoneNumber,
            'messages_count' => count($history),
            'history' => $history
        ]);
    }

    /**
     * Guardar respuesta del asistente
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveAssistantResponse(Request $request)
    {
        $phoneNumber = $request->input('phone_number');
        $response = $request->input('response');
        $metadata = $request->input('metadata', []);

        if (!$phoneNumber || !$response) {
            return response()->json([
                'success' => false,
                'message' => 'phone_number y response son requeridos'
            ], 400);
        }

        $message = $this->chatHistoryService->addAssistantMessage(
            $phoneNumber,
            $response,
            $metadata
        );

        return response()->json([
            'success' => true,
            'message' => 'Respuesta guardada',
            'data' => [
                'id' => $message->id,
                'conversation_id' => $message->conversation_id,
            ]
        ]);
    }

    /**
     * Obtener historial de chat por número de teléfono (GET)
     * Endpoint simplificado para n8n workflow
     * 
     * @param string $phone
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getHistoryByPhone(string $phone, Request $request)
    {
        $limit = $request->query('limit', 20);

        // Limpiar número de teléfono
        $phoneClean = preg_replace('/\D/', '', $phone);

        $history = $this->chatHistoryService->getHistoryForLLM($phoneClean, $limit);

        // Formatear para n8n workflow
        $messages = collect($history)->map(function ($item) {
            return [
                'direction' => $item['role'] === 'user' ? 'incoming' : 'outgoing',
                'message_content' => $item['content'],
                'timestamp' => $item['timestamp'] ?? now()->toDateTimeString()
            ];
        })->values()->toArray();

        return response()->json([
            'success' => true,
            'phone_number' => $phoneClean,
            'messages_count' => count($messages),
            'messages' => $messages
        ]);
    }
}
