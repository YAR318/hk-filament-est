<?php

namespace App\Filament\Resources\ChatConversations\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ChatConversationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información del Contacto')
                    ->schema([
                        TextInput::make('phone_number')
                            ->label('Número de teléfono')
                            ->tel()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->placeholder('+52 123 456 7890'),
                        
                        TextInput::make('contact_name')
                            ->label('Nombre del contacto')
                            ->placeholder('Ej: Juan Pérez'),
                        
                        Select::make('status')
                            ->label('Estado')
                            ->options([
                                'active' => 'Activa',
                                'archived' => 'Archivada',
                                'blocked' => 'Bloqueada',
                            ])
                            ->default('active')
                            ->required(),
                        
                        DateTimePicker::make('last_message_at')
                            ->label('Último mensaje')
                            ->disabled(),
                    ])
                    ->columns(2),
                
                Section::make('Información Adicional')
                    ->schema([
                        Textarea::make('metadata')
                            ->label('Metadatos (JSON)')
                            ->rows(3)
                            ->placeholder('{"nota": "Cliente VIP"}')
                            ->helperText('Información adicional en formato JSON'),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}
