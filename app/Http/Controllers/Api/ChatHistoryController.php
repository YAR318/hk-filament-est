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
     * Para guardar la respuesta del asistente
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
     * Endpoint para n8n workflow
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

    /**
     * Endpoint unificado para procesar mensajes entrantes de n8n
     * Valida si el mensaje debe procesarse y devuelve historial
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function processIncomingMessage(Request $request)
    {
        // Extraer datos del request
        $phoneNumber = $request->input('phone_number');
        $messageBody = $request->input('message_body');
        $messageId = $request->input('message_id');
        $remoteJid = $request->input('remote_jid', '');
        $fromMe = $request->input('from_me', false);
        $userName = $request->input('user_name', 'Cliente');

        // Validación básica
        if (!$phoneNumber || !$messageBody) {
            return response()->json([
                'process' => false,
                'reason' => 'phone_number y message_body son requeridos'
            ], 400);
        }

        // Limpiar número de teléfono
        $phoneClean = preg_replace('/\D/', '', $phoneNumber);

        // Validar si debe procesarse
        $validation = $this->chatHistoryService->shouldProcessMessage($phoneClean, [
            'remoteJid' => $remoteJid,
            'fromMe' => $fromMe,
        ]);

        // Si no debe procesarse, devolver razón
        if (!$validation['process']) {
            return response()->json([
                'process' => false,
                'reason' => $validation['reason']
            ]);
        }

        // Guardar mensaje del usuario
        $this->chatHistoryService->addUserMessage(
            $phoneClean,
            $messageBody,
            null,
            $userName
        );

        // Obtener historial para LLM
        $history = $this->chatHistoryService->getHistoryForLLM($phoneClean, 10);

        return response()->json([
            'process' => true,
            'phone' => $phoneClean,
            'user_name' => $userName,
            'remote_jid' => $remoteJid,
            'history' => $history
        ]);
    }
}
