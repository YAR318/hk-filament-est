<?php

namespace App\Filament\Resources\Roles\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\CheckboxList;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Hidden;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission;

class RoleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detalles del Rol')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nombre del Rol')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->placeholder('Ej: admin, supervisor'),
                    ]),

                Section::make('Permisos de Acceso')
                    ->description('Gestiona los permisos asignados a este rol agrupados por módulo.')
                    ->schema([
                        // Campo oculto que maneja la relación real con la base de datos
                        CheckboxList::make('permissions')
                            ->relationship('permissions', 'name')
                            ->hidden()
                            ->live()
                            ->afterStateHydrated(function ($component, $state, $set) {
                                // Cuando se cargan los permisos desde la BD, distribuirlos a los grupos visuales
                                if (!is_array($state)) return;
                                
                                // Convertir IDs a Nombres para que coincida con los keys de los grupos
                                $permissionNames = Permission::whereIn('id', $state)->pluck('name')->toArray();
                                
                                $groups = static::getPermissionGroups();
                                foreach ($groups as $groupName => $permissions) {
                                    $groupState = array_intersect($permissionNames, array_keys($permissions));
                                    $set('permissions_' . $groupName, $groupState);
                                }
                            }),

                        Grid::make(2)
                            ->schema(function () {
                                $groups = static::getPermissionGroups();
                                $components = [];

                                foreach ($groups as $groupName => $permissions) {
                                    $components[] = Section::make(ucfirst($groupName))
                                        ->schema([
                                            CheckboxList::make('permissions_' . $groupName)
                                                ->label('')
                                                ->options($permissions)
                                                ->live()
                                                ->dehydrated(false)
                                                ->afterStateUpdated(function ($state, $set, $get) {
                                                    // Recolectar todos los permisos seleccionados de todos los grupos
                                                    $allSelected = [];
                                                    $groups = static::getPermissionGroups();
                                                    
                                                    foreach (array_keys($groups) as $gName) {
                                                        $selected = $get('permissions_' . $gName) ?? [];
                                                        $allSelected = array_merge($allSelected, $selected);
                                                    }
                                                    
                                                    // Convertir Nombres a IDs para guardar en la relación
                                                    $ids = Permission::whereIn('name', $allSelected)->pluck('id')->toArray();
                                                    
                                                    // Actualizar el campo principal
                                                    $set('permissions', $ids);
                                                })
                                                ->bulkToggleable()
                                        ])
                                        ->collapsible();
                                }

                                return $components;
                            }),
                    ]),
            ]);
    }

    protected static function getPermissionGroups(): array
    {
        return [
            'mensajes' => [
                'ver_mensajes' => 'Ver mis mensajes',
                'ver_todos_mensajes' => 'Ver todos los mensajes',
                'responder_mensajes' => 'Responder mensajes',
                'asignar_mensajes' => 'Asignar mensajes',
                'eliminar_mensajes' => 'Eliminar mensajes',
            ],
            'usuarios' => [
                'ver_usuarios' => 'Ver usuarios',
                'crear_usuarios' => 'Crear usuarios',
                'editar_usuarios' => 'Editar usuarios',
                'eliminar_usuarios' => 'Eliminar usuarios',
            ],
            'configuracion' => [
                'ver_configuracion' => 'Ver configuración',
                'editar_configuracion' => 'Editar configuración',
                'acceder_panel' => 'Acceder al panel administrativo',
                'ver_guia_whatsapp' => 'Ver guía de conexión Whatsapp',
            ],
            'roles' => [
                'gestionar_roles' => 'Gestionar roles',
                'gestionar_permisos' => 'Gestionar permisos',
            ],
            'reportes' => [
                'ver_reportes' => 'Ver reportes',
                'exportar_reportes' => 'Exportar reportes',
            ],
            'telefonos_bloqueados' => [
                'ver_telefonos_bloqueados' => 'Ver teléfonos bloqueados',
                'bloquear_telefonos' => 'Bloquear teléfonos',
                'desbloquear_telefonos' => 'Desbloquear teléfonos',
            ],
        ];
    }
}