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
        Schema::create('whatsapp_messages', function (Blueprint $table) {
            $table->id();
            $table->string('source')->default('evolution'); // 'evolution' o 'meta'
            $table->string('message_id')->unique(); // ID único del mensaje de WhatsApp
            $table->string('from_number'); // Número que envía
            $table->string('to_number')->nullable(); // Número que recibe
            $table->text('message_body'); // Contenido del mensaje
            $table->string('message_type')->default('text'); // text, image, audio, etc.
            $table->string('instance_name')->nullable(); // Nombre de la instancia Evolution
            $table->json('raw_data')->nullable(); // JSON completo del webhook
            $table->timestamp('received_at'); // Cuándo se recibió
            $table->timestamps();
            
            // Índices para búsquedas rápidas
            $table->index('from_number');
            $table->index('source');
            $table->index('received_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_messages');
    }
};
