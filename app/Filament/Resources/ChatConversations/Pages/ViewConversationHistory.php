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

    public function getHeading(): string
    {
        return 'Conversación con ' . ($this->record->contact_name ?? $this->record->phone_number);
    }
}
