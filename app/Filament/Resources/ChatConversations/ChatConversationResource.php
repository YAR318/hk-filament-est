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
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Actions;

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
        return ChatConversationsTable::configure($table)
            ->actions([
            Actions\EditAction::make(),
            Actions\Action::make('assign_operator')
            ->label('Asignar Operador')
            ->icon('heroicon-o-user-plus')
            ->form([
                \Filament\Forms\Components\Select::make('assigned_to')
                ->label('Operador')
                ->options(\App\Models\Operator::with('user')->get()->pluck('user.name', 'id'))
                ->required(),
            ])
            ->action(function (ChatConversation $record, array $data) {
            $record->update([
                    'assigned_to' => $data['assigned_to'],
                    'is_bot_active' => false, // Desactivar bot al asignar
                ]);

            \Filament\Notifications\Notification::make()
                ->title('Operador asignado')
                ->body('El bot ha sido desactivado para esta conversación.')
                ->success()
                ->send();
        }),
            Actions\Action::make('toggle_bot')
            ->label(fn(ChatConversation $record) => $record->is_bot_active ? 'Desactivar Bot' : 'Activar Bot')
            ->icon(fn(ChatConversation $record) => $record->is_bot_active ? 'heroicon-o-stop' : 'heroicon-o-play')
            ->color(fn(ChatConversation $record) => $record->is_bot_active ? 'danger' : 'success')
            ->action(function (ChatConversation $record) {
            $record->update(['is_bot_active' => !$record->is_bot_active]);

            \Filament\Notifications\Notification::make()
                ->title($record->is_bot_active ? 'Bot activado' : 'Bot desactivado')
                ->success()
                ->send();
        }),
        ]);
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