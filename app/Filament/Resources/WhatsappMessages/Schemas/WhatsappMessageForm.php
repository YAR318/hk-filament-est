<?php

namespace App\Filament\Resources\WhatsappMessages\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class WhatsappMessageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('instance_name'),
                TextInput::make('remote_id'),
                Textarea::make('message_text')
                    ->columnSpanFull(),
                TextInput::make('message_type'),
            ]);
    }
}
