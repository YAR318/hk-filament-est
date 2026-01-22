<?php

namespace App\Services;

use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Models\WhatsappMessage;

class ChatHistoryService
{
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
        ?int $whatsappMessageId = null
    ): ChatMessage {
        $conversation = $this->getOrCreateConversation($phoneNumber);

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
    ): ChatMessage {
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
    ): ChatMessage {
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
        return ChatConversation::with(['messages' => function ($query) {
            $query->latest('sent_at')->limit(50);
        }])->where('phone_number', $phoneNumber)->first();
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
