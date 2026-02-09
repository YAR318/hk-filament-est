<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Crear permisos (EN ESPAÑOL)
        $permissions = [
            // Mensajes WhatsApp
            'ver_mensajes',
            'ver_todos_mensajes',
            'responder_mensajes',
            'asignar_mensajes',
            'eliminar_mensajes',

            // Usuarios
            'ver_usuarios',
            'crear_usuarios',
            'editar_usuarios',
            'eliminar_usuarios',

            // Roles y permisos
            'gestionar_roles',
            'gestionar_permisos',

            // Configuración
            'ver_configuracion',
            'editar_configuracion',

            // Reportes
            'ver_reportes',
            'exportar_reportes',

            // Teléfonos Bloqueados
            'ver_telefonos_bloqueados',
            'bloquear_telefonos',
            'desbloquear_telefonos',

            // Sistema
            'acceder_panel',
            'ver_guia_whatsapp',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Crear roles y asignar permisos

        // ROL: Admin - Control total
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::all());

        // ROL: Supervisor - Ver todo, gestionar operadores
        $supervisorRole = Role::firstOrCreate(['name' => 'supervisor']);
        $supervisorRole->givePermissionTo([
            'ver_todos_mensajes',
            'responder_mensajes',
            'asignar_mensajes',
            'ver_usuarios',
            'ver_reportes',
            'exportar_reportes',
            'ver_configuracion',
            'ver_telefonos_bloqueados',
            'bloquear_telefonos',
            'desbloquear_telefonos',
            'acceder_panel',
            'ver_guia_whatsapp',
        ]);

        // ROL: Operador - Responder mensajes asignados
        $operadorRole = Role::firstOrCreate(['name' => 'operador']);
        $operadorRole->givePermissionTo([
            'ver_mensajes',
            'responder_mensajes',
            'acceder_panel',
        ]);

        // Asignar rol admin a usuarios existentes
        $adminUsers = ['eduardo@gmail.com', 'yeremi@gmail.com'];
        foreach ($adminUsers as $email) {
            $user = User::where('email', $email)->first();
            if ($user) {
                $user->assignRole('admin');
            }
        }

        $this->command->info('✅ Roles y permisos creados correctamente');
        $this->command->info('📋 Roles: admin, supervisor, operador');
        $this->command->info('👤 Usuarios admin: ' . implode(', ', $adminUsers));
    }
}