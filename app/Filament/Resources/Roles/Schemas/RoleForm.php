<?php

namespace App\Filament\Resources\Roles\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\CheckboxList;
use Spatie\Permission\Models\Permission;

class RoleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nombre del Rol')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255)
                    ->placeholder('Ej: admin, supervisor, operador'),

                CheckboxList::make('permissions')
                    ->label('Permisos')
                    ->relationship('permissions', 'name')
                    ->columns(2)
                    ->searchable()
                    ->bulkToggleable()
                    ->getOptionLabelFromRecordUsing(fn($record) => match ($record->name) {
                        'view_messages' => 'Ver mensajes asignados (Mis chats)',
                        'view_all_messages' => 'Ver todos los mensajes (Global)',
                        'reply_messages' => 'Responder mensajes (Chat)',
                        'assign_messages' => 'Asignar conversaciones a operadores',
                        'delete_messages' => 'Eliminar registros de mensajes',
                        'view_users' => 'Ver lista de usuarios del sistema',
                        'create_users' => 'Crear nuevos usuarios',
                        'edit_users' => 'Editar información de usuarios',
                        'delete_users' => 'Eliminar usuarios',
                        'manage_roles' => 'Crear y editar roles de acceso',
                        'manage_permissions' => 'Modificar permisos avanzados',
                        'view_settings' => 'Ver configuración del sistema',
                        'edit_settings' => 'Modificar configuración global',
                        'view_reports' => 'Visualizar dashboard y reportes',
                        'export_reports' => 'Descargar reportes (Excel/PDF)',
                        default => $record->name,
                    }),
            ]);
    }
}
