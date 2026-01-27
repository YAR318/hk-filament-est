<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(255),

                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),

                TextInput::make('password')
                    ->label('Contraseña')
                    ->password()
                    ->required(fn(string $context): bool => $context === 'create')
                    ->dehydrated(fn($state) => filled($state))
                    ->minLength(8)
                    ->maxLength(255)
                    ->helperText('Mínimo 8 caracteres. Dejar vacío para mantener la contraseña actual.'),

                Select::make('role')
                    ->label('Tipo de Acceso (Sistema)')
                    ->options([
                        'admin' => 'Administrador (Acceso Total)',
                        'agent' => 'Agente (Acceso Limitado)',
                        'user' => 'Usuario Cliente (Sin Acceso)',
                        'super_admin' => 'Super Admin',
                    ])
                    ->required()
                    ->default('user')
                    ->native(false),

                Select::make('roles')
                    ->label('Roles y Permisos (Detallado)')
                    ->relationship('roles', 'name')
                    ->preload()
                    ->searchable()
                    ->required()
                    ->helperText('Selecciona el rol del usuario'),

                DateTimePicker::make('email_verified_at')
                    ->label('Email verificado')
                    ->toggleable(isToggledHiddenByDefault: true),
            ]);
    }
}
