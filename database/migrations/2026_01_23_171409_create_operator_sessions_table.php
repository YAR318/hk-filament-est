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
        Schema::create('operator_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operator_id')->constrained('operators')->onDelete('cascade');
            $table->timestamp('session_start')->useCurrent();
            $table->timestamp('session_end')->nullable();
            $table->integer('messages_sent')->default(0);
            $table->integer('conversations_handled')->default(0);
            $table->integer('avg_response_time_seconds')->nullable();
            $table->timestamps();

            // Índices para optimización
            $table->index(['operator_id', 'session_start'], 'idx_operator_session');
            $table->index('session_start', 'idx_session_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('operator_sessions');
    }
};
