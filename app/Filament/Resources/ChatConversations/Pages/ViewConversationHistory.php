<?php

namespace App\Filament\Resources\ChatConversations\Pages;

use App\Filament\Resources\ChatConversations\ChatConversationResource;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use App\Models\ChatMessage;

class ViewConversationHistory extends Page
{
    use InteractsWithRecord;

    protected static string $resource = ChatConversationResource::class;

    protected static ?string $title = 'Historial de Conversación';

    protected string $view = 'filament.resources.chat-conversations.pages.view-conversation-history';

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);
    }

    public function getMessages()
    {
        return ChatMessage::where('conversation_id', $this->record->id)
            ->orderBy('sent_at', 'asc')
            ->get();
    }

    public string $newMessage = '';

    public function sendMessage(\App\Services\EvolutionService $whatsappService): void
    {
        $this->newMessage = trim($this->newMessage);

        if (empty($this->newMessage)) {
            return;
        }

        try {
            // 1. Enviar a través de la API
            $result = $whatsappService->sendMessage($this->record->phone_number, $this->newMessage);

            if ($result) {
                // 2. Guardar en base de datos local
                ChatMessage::create([
                    'conversation_id' => $this->record->id,
                    'role' => 'assistant',
                    'content' => $this->newMessage,
                    'sent_at' => now(),
                    // 'whatsapp_message_id' => $result['key']['id'] ?? null, // Si la API devuelve ID
                ]);

                // 3. Actualizar timestamp de conversación
                $this->record->update([
                    'last_message_at' => now(),
                    'last_human_response_at' => now(),
                ]);

                $this->newMessage = '';

                // Notificar éxito
                \Filament\Notifications\Notification::make()
                    ->title('Mensaje enviado')
                    ->success()
                    ->send();
            }
            else {
                \Filament\Notifications\Notification::make()
                    ->title('Error al enviar mensaje')
                    ->body('No se pudo conectar con la API de WhatsApp.')
                    ->danger()
                    ->send();
            }
        }
        catch (\Exception $e) {
            \Filament\Notifications\Notification::make()
                ->title('Error inesperado')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function getHeading(): string
    {
        return 'Conversación con ' . ($this->record->contact_name ?? $this->record->phone_number);
    }
}