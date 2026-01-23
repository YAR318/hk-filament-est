<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('chat_conversations', function (Blueprint $table) {
            // Campos de asignación de operador
            $table->unsignedBigInteger('assigned_to')->nullable()->after('status');
            $table->enum('priority', ['baja', 'media', 'alta', 'urgente'])->default('media')->after('assigned_to');
            $table->enum('mode', ['ai', 'human', 'hybrid'])->default('ai')->after('priority');
            
            // Campos de timestamp para seguimiento
            $table->timestamp('last_human_response_at')->nullable()->after('mode');
            $table->integer('response_time_seconds')->nullable()->after('last_human_response_at');
            $table->timestamp('escalated_at')->nullable()->after('response_time_seconds');
            $table->timestamp('resolved_at')->nullable()->after('escalated_at');
            
            // Foreign key constraint
            $table->foreign('assigned_to')->references('id')->on('operators')->onDelete('set null');
            
            // Índices para optimización
            $table->index('assigned_to');
            $table->index('priority');
            $table->index('mode');
            $table->index(['status', 'assigned_to']);
        });
        
        // Para SQLite, necesitamos recrear la tabla para cambiar el enum
        if (DB::getDriverName() === 'sqlite') {
            // En SQLite, simplemente agregamos los nuevos valores al enum existente
            // Los nuevos valores se manejarán a nivel de aplicación
        } else {
            // Para MySQL, podemos usar MODIFY COLUMN
            DB::statement("ALTER TABLE chat_conversations MODIFY COLUMN status ENUM('active', 'archived', 'blocked', 'nuevo', 'en_proceso', 'resuelto', 'escalado') DEFAULT 'nuevo'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chat_conversations', function (Blueprint $table) {
            // Eliminar foreign key constraint
            $table->dropForeign(['assigned_to']);
            
            // Eliminar índices
            $table->dropIndex(['assigned_to']);
            $table->dropIndex(['priority']);
            $table->dropIndex(['mode']);
            $table->dropIndex(['status', 'assigned_to']);
            
            // Eliminar columnas agregadas
            $table->dropColumn([
                'assigned_to',
                'priority', 
                'mode',
                'last_human_response_at',
                'response_time_seconds',
                'escalated_at',
                'resolved_at'
            ]);
        });
        
        // Restaurar enum original de status solo en MySQL
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE chat_conversations MODIFY COLUMN status ENUM('active', 'archived', 'blocked') DEFAULT 'active'");
        }
    }
};
