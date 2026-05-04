<?php

namespace App\Filament\Resources\WhatsappMessages;

use App\Filament\Resources\WhatsappMessages\Pages\CreateWhatsappMessage;
use App\Filament\Resources\WhatsappMessages\Pages\EditWhatsappMessage;
use App\Filament\Resources\WhatsappMessages\Pages\ListWhatsappMessages;
use App\Filament\Resources\WhatsappMessages\Schemas\WhatsappMessageForm;
use App\Filament\Resources\WhatsappMessages\Tables\WhatsappMessagesTable;
use App\Models\WhatsappMessage;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class WhatsappMessageResource extends Resource
{
    protected static ?string $model = WhatsappMessage::class;

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $recordTitleAttribute = 'message_body';

    public static function getNavigationIcon(): string|BackedEnum|null
    {
        return 'heroicon-o-chat-bubble-bottom-center-text';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Sistema';
    }

    public static function getNavigationSort(): ?int
    {
        return 99;
    }

    public static function getNavigationLabel(): string
    {
        return 'Logs de WhatsApp';
    }

    public static function form(Schema $schema): Schema
    {
        return WhatsappMessageForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WhatsappMessagesTable::configure($table);
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
            'index' => ListWhatsappMessages::route('/'),
            'create' => CreateWhatsappMessage::route('/create'),
            'edit' => EditWhatsappMessage::route('/{record}/edit'),
        ];
    }
}
