<?php

namespace App\Filament\Resources\AuthorizedUsers\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class AuthorizedUserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('phone_number')
                    ->tel()
                    ->required(),
                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email(),
                TextInput::make('company'),
                Textarea::make('notes')
                    ->columnSpanFull(),
                Toggle::make('is_active')
                    ->required(),
                TextInput::make('daily_message_limit')
                    ->required()
                    ->numeric()
                    ->default(50),
                TextInput::make('hourly_message_limit')
                    ->required()
                    ->numeric()
                    ->default(10),
                DateTimePicker::make('last_message_at'),
            ]);
    }
}
