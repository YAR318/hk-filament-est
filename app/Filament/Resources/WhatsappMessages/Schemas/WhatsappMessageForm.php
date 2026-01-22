<?php

namespace App\Filament\Resources\WhatsappMessages\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Section;
use Filament\Schemas\Schema;
use App\Models\User;

class WhatsappMessageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información del Mensaje')
                    ->schema([
                        TextInput::make('from_number')
                            ->label('De')
                            ->disabled(),
                        
                        TextInput::make('to_number')
                            ->label('Para')
                            ->disabled(),
                        
                        Textarea::make('message_body')
                            ->label('Mensaje')
                            ->disabled()
                            ->rows(3)
                            ->columnSpanFull(),
                        
                        TextInput::make('message_type')
                            ->label('Tipo')
                            ->disabled(),
                        
                        TextInput::make('instance_name')
                            ->label('Instancia')
                            ->disabled(),
                        
                        DateTimePicker::make('received_at')
                            ->label('Recibido')
                            ->disabled(),
                    ])
                    ->columns(2),
                
                Section::make('Gestión')
                    ->schema([
                        Select::make('status')
                            ->label('Estado')
                            ->options([
                                'pending' => 'Pendiente',
                                'assigned' => 'Asignado',
                                'in_progress' => 'En Progreso',
                                'resolved' => 'Resuelto',
                                'closed' => 'Cerrado',
                            ])
                            ->required()
                            ->default('pending'),
                        
                        Select::make('assigned_to')
                            ->label('Asignar a')
                            ->options(User::whereHas('roles', function ($query) {
                                $query->whereIn('name', ['operador', 'supervisor', 'admin']);
                            })->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->nullable(),
                        
                        Textarea::make('response')
                            ->label('Respuesta')
                            ->rows(4)
                            ->columnSpanFull()
                            ->helperText('Escribe aquí la respuesta al mensaje'),
                        
                        DateTimePicker::make('responded_at')
                            ->label('Fecha de respuesta')
                            ->nullable(),
                    ])
                    ->columns(2),
            ]);
    }
}
