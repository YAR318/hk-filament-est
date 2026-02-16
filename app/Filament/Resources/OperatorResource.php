<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OperatorResource\Pages;
use App\Models\Operator;
use App\Models\User;
use Spatie\Permission\Models\Role;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Support\Enums\FontWeight;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;

class OperatorResource extends Resource
{
    protected static ?string $model = Operator::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationGroup(): ?string
    {
        return 'Atención';
    }

    public static function getNavigationSort(): ?int
    {
        return 2;
    }

    public static function getNavigationIcon(): string|BackedEnum|null
    {
        return 'heroicon-o-users';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Información Personal')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre Completo')
                            ->required()
                            ->maxLength(255)
                            ->regex('/^[\pL\s\-\'\.]+ $/u')
                            ->helperText('Solo letras, espacios y guiones'),

                        Forms\Components\TextInput::make('email')
                            ->label('Correo Electrónico')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->helperText('Email único para identificación y notificaciones'),

                        Forms\Components\TextInput::make('phone_number')
                            ->label('Número de Teléfono')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(20)
                            ->placeholder('5217531672288')
                            ->helperText('Formato E.164: código de país + número, sin + ni espacios')
                            ->rule('regex:/^\+?[1-9]\d{7,14}$/'),
                    ])
                    ->columns(2),

                Section::make('Configuración del Operador')
                    ->schema([
                        Forms\Components\Select::make('role')
                            ->label('Rol')
                            ->options(
                                Role::where('name', '!=', 'admin')->pluck('name', 'name')
                            )
                            ->default('operador')
                            ->required()
                            ->helperText('Define los permisos y responsabilidades del operador'),

                        Forms\Components\Select::make('status')
                            ->label('Estado Actual')
                            ->options([
                                'available' => 'Disponible',
                                'busy' => 'Ocupado',
                                'away' => 'Ausente',
                                'offline' => 'Desconectado'
                            ])
                            ->default('offline')
                            ->required()
                            ->helperText('Estado actual del operador en el sistema'),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Operador Activo')
                            ->default(true)
                            ->helperText('Solo los operadores activos pueden recibir conversaciones asignadas'),

                        Forms\Components\TextInput::make('max_concurrent_chats')
                            ->label('Máximo Chats Simultáneos')
                            ->numeric()
                            ->default(5)
                            ->minValue(1)
                            ->maxValue(20)
                            ->required()
                            ->helperText('Límite de conversaciones que puede manejar simultáneamente'),
                    ])
                    ->columns(2),

                Section::make('Información del Sistema')
                    ->schema([
                        Forms\Components\TextInput::make('current_chats_count')
                            ->label('Chats Activos Actuales')
                            ->numeric()
                            ->disabled()
                            ->dehydrated(false)
                            ->helperText('Número actual de conversaciones asignadas (solo lectura)')
                            ->visible(fn($record) => $record !== null),

                        Forms\Components\DateTimePicker::make('last_activity_at')
                            ->label('Última Actividad')
                            ->disabled()
                            ->dehydrated(false)
                            ->helperText('Fecha y hora de la última actividad registrada (solo lectura)')
                            ->visible(fn($record) => $record !== null),
                    ])
                    ->columns(2)
                    ->visible(fn($record) => $record !== null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Medium),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Email copiado')
                    ->copyMessageDuration(1500),

                Tables\Columns\TextColumn::make('phone_number')
                    ->label('Teléfono')
                    ->searchable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('role')
                    ->label('Rol')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'admin' => 'danger',
                        'supervisor' => 'warning',
                        'operador' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'admin' => 'Administrador',
                        'supervisor' => 'Supervisor',
                        'operador' => 'Operador',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'available' => 'success',
                        'busy' => 'warning',
                        'away' => 'info',
                        'offline' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'available' => 'Disponible',
                        'busy' => 'Ocupado',
                        'away' => 'Ausente',
                        'offline' => 'Desconectado',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('current_chats_count')
                    ->label('Chats Activos')
                    ->badge()
                    ->color('primary')
                    ->suffix(fn(Operator $record): string => " / {$record->max_concurrent_chats}"),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                Tables\Columns\TextColumn::make('last_activity_at')
                    ->label('Última Actividad')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->placeholder('Nunca'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->label('Rol')
                    ->options([
                        'admin' => 'Administrador',
                        'supervisor' => 'Supervisor',
                        'operador' => 'Operador'
                    ]),

                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'available' => 'Disponible',
                        'busy' => 'Ocupado',
                        'away' => 'Ausente',
                        'offline' => 'Desconectado'
                    ]),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Activo')
                    ->placeholder('Todos')
                    ->trueLabel('Solo activos')
                    ->falseLabel('Solo inactivos'),
            ])
            ->recordActions([
                Action::make('change_status')
                    ->label('Cambiar Estado')
                    ->icon('heroicon-o-arrow-path')
                    ->color('info')
                    ->form([
                        Forms\Components\Select::make('status')
                            ->label('Nuevo Estado')
                            ->options([
                                'available' => 'Disponible',
                                'busy' => 'Ocupado',
                                'away' => 'Ausente',
                                'offline' => 'Desconectado'
                            ])
                            ->required()
                    ])
                    ->action(function (Operator $record, array $data) {
                        $record->update([
                            'status' => $data['status'],
                            'last_activity_at' => now()
                        ]);
                    })
                    ->successNotificationTitle('Estado actualizado correctamente'),

                EditAction::make(),

                DeleteAction::make()
                    ->requiresConfirmation(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->requiresConfirmation(),

                    BulkAction::make('activate')
                        ->label('Activar Seleccionados')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(function ($records) {
                            $records->each(fn(Operator $record) => $record->update(['is_active' => true]));
                        })
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion(),

                    BulkAction::make('deactivate')
                        ->label('Desactivar Seleccionados')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(function ($records) {
                            $records->each(fn(Operator $record) => $record->update(['is_active' => false]));
                        })
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListOperators::route('/'),
            'create' => Pages\CreateOperator::route('/create'),
            'edit' => Pages\EditOperator::route('/{record}/edit'),
        ];
    }
}