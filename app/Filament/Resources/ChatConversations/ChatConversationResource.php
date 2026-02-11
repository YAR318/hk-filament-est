<?php

namespace App\Filament\Resources\ChatConversations;

use App\Filament\Resources\ChatConversations\Pages\CreateChatConversation;
use App\Filament\Resources\ChatConversations\Pages\EditChatConversation;
use App\Filament\Resources\ChatConversations\Pages\ListChatConversations;
use App\Filament\Resources\ChatConversations\Pages;
use App\Filament\Resources\ChatConversations\Schemas\ChatConversationForm;
use App\Filament\Resources\ChatConversations\Tables\ChatConversationsTable;
use App\Models\ChatConversation;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class ChatConversationResource extends Resource
{
    protected static ?string $model = ChatConversation::class;

    protected static ?string $recordTitleAttribute = 'phone_number';

    public static function getNavigationIcon(): string|BackedEnum|null
    {
        return 'heroicon-o-chat-bubble-left-right';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Atención';
    }

    public static function getNavigationSort(): ?int
    {
        return 1;
    }

    public static function getNavigationLabel(): string
    {
        return 'Conversaciones';
    }

    public static function getModelLabel(): string
    {
        return 'Conversación';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Conversaciones';
    }

    public static function form(Schema $schema): Schema
    {
        return ChatConversationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ChatConversationsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListChatConversations::route('/'),
            'create' => CreateChatConversation::route('/create'),
            'edit' => EditChatConversation::route('/{record}/edit'),
            'view-history' => Pages\ViewConversationHistory::route('/{record}/history'),
        ];
    }
}