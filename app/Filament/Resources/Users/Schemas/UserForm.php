<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rules\Password;
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
                    ->maxLength(255)
                    ->regex('/^[\pL\s\-\'\.]+$/u')
                    ->helperText('Solo letras, espacios y guiones'),

                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255)
                    ->helperText('Correo electrónico único del usuario'),

                TextInput::make('password')
                    ->label('Contraseña')
                    ->password()
                    ->required(fn(string $context): bool => $context === 'create')
                    ->hiddenOn('edit')
                    ->dehydrated(fn($state) => filled($state))
                    ->minLength(8)
                    ->maxLength(255)
                    ->rule(Password::min(8)->mixedCase()->numbers())
                    ->confirmed()
                    ->helperText('Mínimo 8 caracteres, una mayúscula, una minúscula y un número'),

                TextInput::make('password_confirmation')
                    ->label('Confirmar Contraseña')
                    ->password()
                    ->required(fn(string $context): bool => $context === 'create')
                    ->hiddenOn('edit')
                    ->dehydrated(false),

                Select::make('role')
                    ->label('Tipo de Acceso (Sistema)')
                    ->options(fn () => Role::all()->pluck('name', 'name')->toArray() + ['user' => 'Usuario Cliente (Sin Acceso Panel)'])
                    ->required()
                    ->default('user')
                    ->native(false)
                    ->helperText('Define el nivel de acceso al panel de administración'),

                \Filament\Forms\Components\Toggle::make('email_verified_at')
                    ->label('Email Verificado')
                    ->onColor('success')
                    ->offColor('danger')
                    ->formatStateUsing(fn($state) => $state !== null)
                    ->dehydrateStateUsing(fn($state) => $state ? now() : null),
            ]);
    }
}