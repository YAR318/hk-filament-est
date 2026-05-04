<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Renombrar la tabla
        Schema::rename('authorized_users', 'operators');
        
        // Agregar nuevos campos
        Schema::table('operators', function (Blueprint $table) {
            // Cambiar campo notes por role
            $table->enum('role', ['admin', 'supervisor', 'operador'])->default('operador')->after('email');
            
            // Agregar nuevos campos para sistema de operadores
            $table->integer('max_concurrent_chats')->default(5)->after('hourly_message_limit');
            $table->integer('current_chats_count')->default(0)->after('max_concurrent_chats');
            $table->enum('status', ['available', 'busy', 'away', 'offline'])->default('offline')->after('current_chats_count');
            $table->timestamp('last_activity_at')->nullable()->after('last_message_at');
            
            // Agregar índices para optimización
            $table->index('role');
            $table->index('status');
            $table->index(['is_active', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remover campos agregados
        Schema::table('operators', function (Blueprint $table) {
            $table->dropIndex(['operators_role_index']);
            $table->dropIndex(['operators_status_index']);
            $table->dropIndex(['operators_is_active_status_index']);
            
            $table->dropColumn([
                'role',
                'max_concurrent_chats',
                'current_chats_count', 
                'status',
                'last_activity_at'
            ]);
        });
        
        // Renombrar de vuelta
        Schema::rename('operators', 'authorized_users');
    }
};