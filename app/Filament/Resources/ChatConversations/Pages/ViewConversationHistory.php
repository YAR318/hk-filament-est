<?php

namespace App\Filament\Resources\ChatConversations\Pages;

use App\Filament\Resources\ChatConversations\ChatConversationResource;
use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Models\Operator;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;

class ViewConversationHistory extends Page
{
    use InteractsWithRecord;

    protected static string $resource = ChatConversationResource::class;

    protected static ?string $title = 'Detalle de Conversación';

    protected string $view = 'filament.resources.chat-conversations.pages.view-conversation-history';

    public string $newMessage = '';
    public ?int $selectedOperator = null;

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

    /**
     * Check if the current user can send messages in this conversation
     */
    public function canSendMessages(): bool
    {
        $user = auth()->user();

        // Admin and Supervisor can always send
        if ($user->can('ver_todas_conversaciones')) {
            return true;
        }

        // Operator can only send if the chat is assigned to them
        $operator = Operator::where('email', $user->email)->first();
        if ($operator && $this->record->assigned_to === $operator->id) {
            return true;
        }

        return false;
    }

    /**
     * Check if operator can take this chat
     */
    public function canTakeChat(): bool
    {
        $user = auth()->user();
        return $user->can('tomar_conversaciones') && is_null($this->record->assigned_to);
    }

    /**
     * Check if user can close this chat
     */
    public function canCloseChat(): bool
    {
        $user = auth()->user();

        if ($user->can('ver_todas_conversaciones')) {
            return true;
        }

        if ($user->can('cerrar_conversaciones')) {
            $operator = Operator::where('email', $user->email)->first();
            return $operator && $this->record->assigned_to === $operator->id;
        }

        return false;
    }

    /**
     * Check if user is admin/supervisor
     */
    public function isManager(): bool
    {
        return auth()->user()->can('ver_todas_conversaciones');
    }

    /**
     * Operator takes the chat
     */
    public function takeChat(): void
    {
        $user = auth()->user();

        if (!$user->can('tomar_conversaciones')) {
            return;
        }

        $operator = Operator::where('email', $user->email)->first();

        if (!$operator) {
            \Filament\Notifications\Notification::make()
                ->title('Error')
                ->body('No se encontró tu perfil de operador.')
                ->danger()
                ->send();
            return;
        }

        // Validar max_concurrent_chats
        $currentChats = ChatConversation::where('assigned_to', $operator->id)
            ->where('status', 'en_proceso')
            ->count();

        if ($currentChats >= $operator->max_concurrent_chats) {
            \Filament\Notifications\Notification::make()
                ->title('Límite alcanzado')
                ->body("Ya tienes {$currentChats} conversaciones activas. Tu límite es {$operator->max_concurrent_chats}.")
                ->warning()
                ->send();
            return;
        }

        $this->record->update([
            'assigned_to' => $operator->id,
            'is_bot_active' => false,
            'status' => 'en_proceso',
        ]);

        $this->record->refresh();

        \Filament\Notifications\Notification::make()
            ->title('Chat tomado')
            ->body('Ahora eres responsable de esta conversación. Puedes empezar a responder.')
            ->success()
            ->send();
    }

    /**
     * Close/resolve the chat
     */
    public function closeChat(): void
    {
        if (!$this->canCloseChat()) {
            return;
        }

        $this->record->update([
            'status' => 'resuelto',
            'assigned_to' => null,
            'is_bot_active' => true,
            'resolved_at' => now(),
        ]);

        $this->record->refresh();

        \Filament\Notifications\Notification::make()
            ->title('Chat cerrado')
            ->body('La conversación fue resuelta y el bot se reactivó.')
            ->success()
            ->send();
    }

    /**
     * Assign an operator (supervisor/admin only)
     */
    public function assignOperator(): void
    {
        $user = auth()->user();

        if (!$user->can('asignar_conversaciones')) {
            return;
        }

        if (!$this->selectedOperator) {
            \Filament\Notifications\Notification::make()
                ->title('Error')
                ->body('Selecciona un operador.')
                ->danger()
                ->send();
            return;
        }

        $this->record->update([
            'assigned_to' => $this->selectedOperator,
            'is_bot_active' => false,
            'status' => 'en_proceso',
        ]);

        $this->record->refresh();

        \Filament\Notifications\Notification::make()
            ->title('Operador asignado')
            ->body('El bot ha sido desactivado para esta conversación.')
            ->success()
            ->send();
    }

    /**
     * Toggle bot on/off (supervisor/admin only)
     */
    public function toggleBot(): void
    {
        $user = auth()->user();

        if (!$user->can('gestionar_bot')) {
            return;
        }

        $this->record->update(['is_bot_active' => !$this->record->is_bot_active]);
        $this->record->refresh();

        \Filament\Notifications\Notification::make()
            ->title($this->record->is_bot_active ? 'Bot activado' : 'Bot desactivado')
            ->success()
            ->send();
    }

    /**
     * Send a message via WhatsApp (routes by channel: evolution or meta)
     */
    public function sendMessage(): void
    {
        if (!$this->canSendMessages()) {
            \Filament\Notifications\Notification::make()
                ->title('Sin permiso')
                ->body('No tienes permiso para enviar mensajes en esta conversación.')
                ->danger()
                ->send();
            return;
        }

        $this->newMessage = strip_tags(trim($this->newMessage));

        if (empty($this->newMessage)) {
            return;
        }

        // Protección anti-pegado masivo
        if (mb_strlen($this->newMessage) > 4096) {
            \Filament\Notifications\Notification::make()
                ->title('Mensaje demasiado largo')
                ->body('El mensaje no puede exceder 4096 caracteres.')
                ->warning()
                ->send();
            return;
        }

        try {
            $success = false;

            // Enrutar por canal
            if ($this->record->channel === 'meta') {
                // Enviar por Meta API
                $metaProvider = new \App\Services\WhatsApp\MetaWhatsAppProvider();

                if (!$metaProvider->isConfigured()) {
                    \Filament\Notifications\Notification::make()
                        ->title('Meta API no configurada')
                        ->body('Configura el Token de Meta y Phone ID en Ajustes de la App.')
                        ->danger()
                        ->send();
                    return;
                }

                $result = $metaProvider->sendMessage($this->record->phone_number, $this->newMessage);
                $success = $result['success'] ?? false;

                if (!$success) {
                    \Filament\Notifications\Notification::make()
                        ->title('Error al enviar por Meta')
                        ->body($result['error'] ?? 'Error desconocido')
                        ->danger()
                        ->send();
                    return;
                }
            } else {
                // Enviar por Evolution API
                $whatsappService = app(\App\Services\EvolutionService::class);
                $result = $whatsappService->sendMessage($this->record->phone_number, $this->newMessage);
                $success = !empty($result);

                if (!$success) {
                    \Filament\Notifications\Notification::make()
                        ->title('Error al enviar mensaje')
                        ->body('No se pudo conectar con la API de WhatsApp.')
                        ->danger()
                        ->send();
                    return;
                }
            }

            // Guardar mensaje en historial
            ChatMessage::create([
                'conversation_id' => $this->record->id,
                'role' => 'assistant',
                'content' => $this->newMessage,
                'sent_at' => now(),
            ]);

            $this->record->update([
                'last_message_at' => now(),
                'last_human_response_at' => now(),
            ]);

            $this->newMessage = '';

            \Filament\Notifications\Notification::make()
                ->title('Mensaje enviado')
                ->success()
                ->send();

        } catch (\Exception $e) {
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

    /**
     * Get available operators for assignment dropdown
     */
    public function getOperatorOptions(): array
    {
        return Operator::with('user')
            ->where('is_active', true)
            ->get()
            ->pluck('user.name', 'id')
            ->filter()
            ->toArray();
    }
}