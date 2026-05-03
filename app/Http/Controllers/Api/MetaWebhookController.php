<?php

namespace App\Http\Controllers\Api;

use App\Models\AppSetting;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Webhook controller para Meta WhatsApp Cloud API.
 * Maneja la verificación del webhook y la recepción de mensajes entrantes.
 * Reenvía mensajes válidos a n8n para procesamiento con IA.
 */
class MetaWebhookController
{
    /**
     * Verificación del webhook (GET).
     * Meta envía un challenge que debemos devolver para confirmar el webhook.
     */
    public function verify(Request $request): mixed
    {
        $verifyToken = AppSetting::get('meta_verify_token', '');

        $mode = $request->query('hub_mode');
        $token = $request->query('hub_verify_token');
        $challenge = $request->query('hub_challenge');

        if ($mode === 'subscribe' && $token === $verifyToken) {
            Log::info('Meta Webhook: Verificacion exitosa');
            return response($challenge, 200)->header('Content-Type', 'text/plain');
        }

        Log::warning('Meta Webhook: Verificacion fallida', [
            'mode' => $mode,
            'token_match' => $token === $verifyToken,
        ]);

        return response()->json(['error' => 'Verificacion fallida'], 403);
    }

    /**
     * Recibir mensajes entrantes (POST).
     * Meta envía notificaciones cuando un usuario envía un mensaje.
     * Extrae los datos y los reenvía a n8n para procesamiento.
     */
    public function receive(Request $request): JsonResponse
    {
        $body = $request->all();

        Log::info('Meta Webhook: Payload recibido', ['body' => $body]);

        // Verificar que es una notificación de WhatsApp
        if (!isset($body['entry'][0]['changes'][0]['value'])) {
            return response()->json(['status' => 'ignored'], 200);
        }

        $value = $body['entry'][0]['changes'][0]['value'];

        // Solo procesar mensajes (no status updates)
        if (!isset($value['messages'][0])) {
            return response()->json(['status' => 'no_message'], 200);
        }

        $message = $value['messages'][0];
        $contact = $value['contacts'][0] ?? null;

        $phoneNumber = $message['from'] ?? null;
        $messageText = $message['text']['body'] ?? null;
        $messageType = $message['type'] ?? 'unknown';
        $messageId = $message['id'] ?? '';
        $contactName = $contact['profile']['name'] ?? 'Sin nombre';

        if (!$phoneNumber || !$messageText) {
            Log::info('Meta Webhook: Mensaje incompleto o no texto', [
                'type' => $messageType,
                'has_phone' => !empty($phoneNumber),
            ]);
            return response()->json(['status' => 'incomplete'], 200);
        }

        Log::info('Meta Webhook: Reenviando a n8n', [
            'from' => $phoneNumber,
            'name' => $contactName,
            'text' => $messageText,
        ]);

        // Reenviar el payload completo a n8n para procesamiento
        try {
            // URL del n8n configurable via .env, por defecto apunta a la red interna de docker local
            $n8nWebhookUrl = env('N8N_WEBHOOK_URL', 'http://n8n:5678') . '/webhook/whatsapp-meta';

            $n8nResponse = Http::timeout(10)->post($n8nWebhookUrl, [
                'phone_number' => $phoneNumber,
                'message_body' => $messageText,
                'message_id' => $messageId,
                'user_name' => $contactName,
                'message_type' => $messageType,
                'channel' => 'meta',
                'raw_payload' => $body,
            ]);

            Log::info('Meta Webhook: n8n respondió', [
                'status' => $n8nResponse->status(),
            ]);
        } catch (\Exception $e) {
            Log::error('Meta Webhook: Error reenviando a n8n', [
                'error' => $e->getMessage(),
            ]);
            // Siempre retornar 200 a Meta para que no reintente
        }

        return response()->json([
            'status' => 'forwarded',
            'from' => $phoneNumber,
        ], 200);
    }
}