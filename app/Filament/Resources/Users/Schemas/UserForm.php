<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use Spatie\Permission\Models\Role;

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
                    ->hiddenOn('edit')
                    ->dehydrated(fn($state) => filled($state))
                    ->minLength(8)
                    ->maxLength(255),

                Select::make('role')
                    ->label('Tipo de Acceso (Sistema)')
                    ->options(fn () => Role::all()->pluck('name', 'name')->toArray() + ['user' => 'Usuario Cliente (Sin Acceso Panel)'])
                    ->required()
                    ->default('user')
                    ->native(false),

                \Filament\Forms\Components\Toggle::make('email_verified_at')
                    ->label('Email Verificado')
                    ->onColor('success')
                    ->offColor('danger')
                    ->formatStateUsing(fn($state) => $state !== null)
                    ->dehydrateStateUsing(fn($state) => $state ? now() : null),
            ]);
    }
}