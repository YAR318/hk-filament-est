<?php

namespace App\Services;

use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Models\WhatsappMessage;
use App\Models\BlockedPhone;

class ChatHistoryService
{
    /**
     * Número del dueño para evitar auto-respuesta (anti-bucle)
     * TODO: Mover a configuración o base de datos
     */
    private const OWNER_JID_FRAGMENT = '7531672288';

    /**
     * Validar si un mensaje debe ser procesado por el bot
     * 
     * @param string $phoneNumber Número de teléfono normalizado
     * @param array $messageData Datos del mensaje (remoteJid, fromMe, etc.)
     * @return array ['process' => bool, 'reason' => string|null]
     */
    public function shouldProcessMessage(string $phoneNumber, array $messageData): array
    {
        $remoteJid = $messageData['remoteJid'] ?? '';
        $fromMe = $messageData['fromMe'] ?? false;

        // 1. Verificar si es mensaje propio (anti-bucle)
        if ($fromMe === true) {
            return ['process' => false, 'reason' => 'Mensaje propio ignorado'];
        }

        // 2. Verificar si es mensaje de grupo (@g.us)
        if (str_contains($remoteJid, '@g.us')) {
            return ['process' => false, 'reason' => 'Mensaje de grupo ignorado'];
        }

        // 3. Verificar si es LID no resoluble
        if (str_contains($remoteJid, '@lid') && empty($phoneNumber)) {
            return ['process' => false, 'reason' => 'LID no resuelto'];
        }

        // 4. Verificar si contiene el número del dueño (anti-bucle)
        if (str_contains($phoneNumber, self::OWNER_JID_FRAGMENT) || str_contains($remoteJid, self::OWNER_JID_FRAGMENT)) {
            return ['process' => false, 'reason' => 'Bucle de dueño detectado'];
        }

        // 5. Verificar si está bloqueado en la base de datos
        if (BlockedPhone::isBlocked($phoneNumber)) {
            return ['process' => false, 'reason' => 'Número bloqueado'];
        }

        // 6. Verificar estado de la conversación y configuración del bot
        $conversation = ChatConversation::where('phone_number', $phoneNumber)->first();

        if ($conversation) {
            if ($conversation->status === 'blocked') {
                return ['process' => false, 'reason' => 'Conversación bloqueada'];
            }

            // Verificar si el bot está activo (false si hay operador asignado o desactivado manual)
            if (!$conversation->is_bot_active) {
                return ['process' => false, 'reason' => 'Bot desactivado'];
            }
        }

        return ['process' => true, 'reason' => null];
    }
    /**
     * Obtener o crear conversación por número telefónico
     */
    public function getOrCreateConversation(string $phoneNumber, ?string $contactName = null): ChatConversation
    {
        return ChatConversation::firstOrCreate(
        ['phone_number' => $phoneNumber],
        [
            'contact_name' => $contactName,
            'status' => 'active',
            'last_message_at' => now(),
        ]
        );
    }

    /**
     * Agregar mensaje del usuario a la conversación
     */
    public function addUserMessage(
        string $phoneNumber,
        string $content,
        ?int $whatsappMessageId = null,
        ?string $contactName = null
        ): ChatMessage
    {
        $conversation = $this->getOrCreateConversation($phoneNumber, $contactName);

        // Actualizar nombre si se proporciona y la conversación no tiene uno
        if ($contactName && !$conversation->contact_name) {
            $conversation->update(['contact_name' => $contactName]);
        }

        $message = ChatMessage::create([
            'conversation_id' => $conversation->id,
            'whatsapp_message_id' => $whatsappMessageId,
            'role' => 'user',
            'content' => $content,
            'sent_at' => now(),
        ]);

        // Actualizar última actividad
        $conversation->update(['last_message_at' => now()]);

        return $message;
    }

    /**
     * Agregar respuesta del asistente (bot)
     */
    public function addAssistantMessage(
        string $phoneNumber,
        string $content,
        ?array $metadata = null
        ): ChatMessage
    {
        $conversation = $this->getOrCreateConversation($phoneNumber);

        $message = ChatMessage::create([
            'conversation_id' => $conversation->id,
            'role' => 'assistant',
            'content' => $content,
            'metadata' => $metadata,
            'sent_at' => now(),
        ]);

        $conversation->update(['last_message_at' => now()]);

        return $message;
    }

    /**
     * Agregar mensaje del sistema (notificaciones)
     */
    public function addSystemMessage(
        string $phoneNumber,
        string $content
        ): ChatMessage
    {
        $conversation = $this->getOrCreateConversation($phoneNumber);

        return ChatMessage::create([
            'conversation_id' => $conversation->id,
            'role' => 'system',
            'content' => $content,
            'sent_at' => now(),
        ]);
    }

    /**
     * Obtener historial formateado para LLM
     */
    public function getHistoryForLLM(string $phoneNumber, int $limit = 20): array
    {
        $conversation = ChatConversation::where('phone_number', $phoneNumber)->first();

        if (!$conversation) {
            return [];
        }

        return $conversation->getHistoryForLLM($limit);
    }

    /**
     * Obtener conversación con mensajes
     */
    public function getConversation(string $phoneNumber): ?ChatConversation
    {
        return ChatConversation::with([
            'messages' => function ($query) {
            $query->latest('sent_at')->limit(50);
        }
        ])->where('phone_number', $phoneNumber)->first();
    }

    /**
     * Archivar conversación
     */
    public function archiveConversation(string $phoneNumber): bool
    {
        $conversation = ChatConversation::where('phone_number', $phoneNumber)->first();

        if ($conversation) {
            return $conversation->update(['status' => 'archived']);
        }

        return false;
    }

    /**
     * Bloquear conversación
     */
    public function blockConversation(string $phoneNumber): bool
    {
        $conversation = ChatConversation::where('phone_number', $phoneNumber)->first();

        if ($conversation) {
            return $conversation->update(['status' => 'blocked']);
        }

        return false;
    }

    /**
     * Vincular mensaje de WhatsApp con historial de chat
     */
    public function linkWhatsappMessage(WhatsappMessage $whatsappMessage): ChatMessage
    {
        return $this->addUserMessage(
            $whatsappMessage->from_number,
            $whatsappMessage->message_body,
            $whatsappMessage->id
        );
    }
}