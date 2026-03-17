<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained('chat_conversations')->cascadeOnDelete();
            $table->foreignId('whatsapp_message_id')->nullable()->constrained('whatsapp_messages')->nullOnDelete();
            $table->enum('role', ['user', 'assistant', 'system']); // user=cliente, assistant=bot, system=notificaciones
            $table->text('content');
            $table->json('metadata')->nullable(); // Para tokens, modelo usado, etc.
            $table->timestamp('sent_at');
            $table->timestamps();
            
            $table->index(['conversation_id', 'sent_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_messages');
    }
};
