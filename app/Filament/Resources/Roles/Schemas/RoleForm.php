<?php

namespace App\Filament\Resources\Roles\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\TextInput;
use Filament\Schemas\Components\CheckboxList;
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
                    ->descriptions([
                        'view_messages' => 'Ver mensajes asignados',
                        'view_all_messages' => 'Ver todos los mensajes',
                        'reply_messages' => 'Responder mensajes',
                        'assign_messages' => 'Asignar mensajes a operadores',
                        'delete_messages' => 'Eliminar mensajes',
                        'view_users' => 'Ver usuarios',
                        'create_users' => 'Crear usuarios',
                        'edit_users' => 'Editar usuarios',
                        'delete_users' => 'Eliminar usuarios',
                        'manage_roles' => 'Gestionar roles',
                        'manage_permissions' => 'Gestionar permisos',
                        'view_settings' => 'Ver configuración',
                        'edit_settings' => 'Editar configuración',
                        'view_reports' => 'Ver reportes',
                        'export_reports' => 'Exportar reportes',
                    ]),
            ]);
    }
}
