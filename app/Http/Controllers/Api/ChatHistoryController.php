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
        \Illuminate\Support\Facades\Log::info('processIncomingMessage request:', $request->all());

        // Extraer datos del request
        $phoneNumber = $request->input('phone_number');
        $messageBody = $request->input('message_body');
        $messageId = $request->input('message_id');
        $remoteJid = $request->input('remote_jid', '');
        $fromMe = $request->input('from_me', false);
        $userName = $request->input('user_name', 'Cliente');
        $instanceName = $request->input('instance_name', 'HunabkuBot');
        $channel = $request->input('channel', 'evolution');

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

        // Asegurar que la conversación tenga el canal correcto
        $this->chatHistoryService->getOrCreateConversation($phoneClean, $userName, $channel, $instanceName);

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
            'instance_name' => $instanceName,
            'channel' => $channel,
            'history' => $history
        ]);
    }

    /**
     * Escalar conversación a un operador humano
     * La IA llama este endpoint cuando detecta que necesita intervención humana
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function escalateToHuman(Request $request)
    {
        $phoneNumber = $request->input('phone_number');
        $reason = $request->input('reason', 'Solicitud de atención humana');
        $contactName = $request->input('contact_name', 'Cliente');

        if (!$phoneNumber) {
            return response()->json([
                'success' => false,
                'message' => 'phone_number es requerido'
            ], 400);
        }

        $phoneClean = preg_replace('/\D/', '', $phoneNumber);

        // Obtener o crear la conversación
        $conversation = $this->chatHistoryService->getOrCreateConversation($phoneClean, $contactName);

        // Buscar operador disponible con menos carga
        $operator = \App\Models\Operator::findAvailableOperator();

        if (!$operator) {
            // No hay operadores disponibles
            $conversation->update([
                'escalated_at' => now(),
                'priority' => 'alta',
            ]);

            // Guardar mensaje de sistema
            $this->chatHistoryService->addSystemMessage(
                $phoneClean,
                "⚠️ Escalación solicitada pero no hay operadores disponibles. Motivo: {$reason}"
            );

            return response()->json([
                'success' => true,
                'escalated' => false,
                'reason' => 'no_operators_available',
                'message' => 'No hay operadores disponibles en este momento. Su consulta quedará registrada y un asesor le contactará pronto.'
            ]);
        }

        // Asignar conversación al operador
        $operator->assignConversation($conversation);

        // Desactivar el bot para esta conversación
        $conversation->update([
            'is_bot_active' => false,
            'escalated_at' => now(),
            'priority' => 'alta',
        ]);

        // Guardar mensaje de sistema
        $this->chatHistoryService->addSystemMessage(
            $phoneClean,
            "🔔 Conversación escalada a {$operator->name}. Motivo: {$reason}"
        );

        // Enviar notificación al panel de Filament del operador
        if ($operator->user) {
            \Filament\Notifications\Notification::make()
                ->title('Nueva Conversación Asignada')
                ->body("Cliente: {$contactName}\nMotivo: {$reason}")
                ->success()
                ->sendToDatabase($operator->user);
        }

        return response()->json([
            'success' => true,
            'escalated' => true,
            'operator' => [
                'name' => $operator->name,
                'phone_number' => $operator->phone_number,
                'email' => $operator->email,
            ],
            'message' => "Conversación asignada a {$operator->name}."
        ]);
    }
}
