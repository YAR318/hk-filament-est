<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WhatsappMessage;
use App\Services\ChatHistoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class MessageController extends Controller
{
    protected ChatHistoryService $chatHistoryService;

    public function __construct(ChatHistoryService $chatHistoryService)
    {
        $this->chatHistoryService = $chatHistoryService;
    }

    /**
     * Recibe mensajes de WhatsApp desde n8n
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            // Log del request completo para debugging
            Log::info('Mensaje recibido desde n8n', [
                'body' => $request->all(),
                'headers' => $request->headers->all()
            ]);

            // Validar datos requeridos
            $validator = Validator::make($request->all(), [
                'source' => 'required|string|in:evolution,meta',
                'message_id' => 'required|string',
                'from_number' => 'required|string',
                'message_body' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos inválidos',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Crear el mensaje en la base de datos
            $message = WhatsappMessage::create([
                'source' => $request->input('source', 'meta'),
                'message_id' => $request->input('message_id'),
                'from_number' => $request->input('from_number'),
                'to_number' => $request->input('to_number'),
                'message_body' => $request->input('message_body'),
                'message_type' => $request->input('message_type', 'text'),
                'instance_name' => $request->input('instance_name'),
                'raw_data' => $request->all(),
                'received_at' => now(),
            ]);

            // Guardar en historial de chat
            Log::info('Intentando guardar en historial de chat', [
                'phone' => $message->from_number,
                'content' => $message->message_body
            ]);
            
            $chatMessage = $this->chatHistoryService->linkWhatsappMessage($message);
            
            Log::info('Mensaje guardado en historial', [
                'chat_message_id' => $chatMessage->id,
                'role' => $chatMessage->role
            ]);

            Log::info('Mensaje guardado exitosamente', [
                'id' => $message->id,
                'phone' => $message->from_number
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Mensaje guardado correctamente',
                'data' => [
                    'id' => $message->id,
                    'message_id' => $message->message_id,
                    'from' => $message->from_number,
                ]
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error al guardar mensaje de WhatsApp', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al procesar el mensaje',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}