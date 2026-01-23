<?php

namespace Database\Seeders;

use App\Models\Operator;
use Illuminate\Database\Seeder;

class OperatorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear operadores iniciales basados en los usuarios admin existentes
        $operators = [
            [
                'name' => 'Eduardo Administrador',
                'email' => 'eduardo@gmail.com',
                'phone_number' => '+5213331234567',
                'role' => 'admin',
                'is_active' => true,
                'max_concurrent_chats' => 10,
                'current_chats_count' => 0,
                'status' => 'available',
                'last_activity_at' => now(),
            ],
            [
                'name' => 'Yeremi Administrador',
                'email' => 'yeremi@gmail.com',
                'phone_number' => '+5213339876543',
                'role' => 'admin',
                'is_active' => true,
                'max_concurrent_chats' => 10,
                'current_chats_count' => 0,
                'status' => 'available',
                'last_activity_at' => now(),
            ],
            [
                'name' => 'María Supervisor',
                'email' => 'maria.supervisor@empresa.com',
                'phone_number' => '+5213335551234',
                'role' => 'supervisor',
                'is_active' => true,
                'max_concurrent_chats' => 8,
                'current_chats_count' => 0,
                'status' => 'available',
                'last_activity_at' => now(),
            ],
            [
                'name' => 'Carlos Operador',
                'email' => 'carlos.operador@empresa.com',
                'phone_number' => '+5213337778888',
                'role' => 'operador',
                'is_active' => true,
                'max_concurrent_chats' => 5,
                'current_chats_count' => 0,
                'status' => 'available',
                'last_activity_at' => now(),
            ],
            [
                'name' => 'Ana Operadora',
                'email' => 'ana.operadora@empresa.com',
                'phone_number' => '+5213334445555',
                'role' => 'operador',
                'is_active' => true,
                'max_concurrent_chats' => 5,
                'current_chats_count' => 0,
                'status' => 'busy',
                'last_activity_at' => now()->subMinutes(30),
            ],
            [
                'name' => 'Roberto Operador',
                'email' => 'roberto.operador@empresa.com',
                'phone_number' => '+5213332221111',
                'role' => 'operador',
                'is_active' => false,
                'max_concurrent_chats' => 5,
                'current_chats_count' => 0,
                'status' => 'offline',
                'last_activity_at' => now()->subHours(2),
            ],
        ];

        foreach ($operators as $operatorData) {
            Operator::updateOrCreate(
                ['email' => $operatorData['email']],
                $operatorData
            );
        }

        $this->command->info('✅ Operadores iniciales creados correctamente');
        $this->command->info('👥 6 operadores: 2 admin, 1 supervisor, 3 operadores');
        $this->command->info('📊 Estados: 4 disponibles, 1 ocupado, 1 desconectado');
    }
}