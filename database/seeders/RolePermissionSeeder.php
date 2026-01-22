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

        // Crear permisos
        $permissions = [
            // Mensajes WhatsApp
            'view_messages',
            'view_all_messages',
            'reply_messages',
            'assign_messages',
            'delete_messages',
            
            // Usuarios
            'view_users',
            'create_users',
            'edit_users',
            'delete_users',
            
            // Roles y permisos
            'manage_roles',
            'manage_permissions',
            
            // Configuración
            'view_settings',
            'edit_settings',
            
            // Reportes
            'view_reports',
            'export_reports',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Crear roles y asignar permisos

        // ROL: Admin - Control total
        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::all());

        // ROL: Supervisor - Ver todo, gestionar operadores
        $supervisorRole = Role::create(['name' => 'supervisor']);
        $supervisorRole->givePermissionTo([
            'view_all_messages',
            'reply_messages',
            'assign_messages',
            'view_users',
            'view_reports',
            'export_reports',
            'view_settings',
        ]);

        // ROL: Operador - Responder mensajes asignados
        $operadorRole = Role::create(['name' => 'operador']);
        $operadorRole->givePermissionTo([
            'view_messages',
            'reply_messages',
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
