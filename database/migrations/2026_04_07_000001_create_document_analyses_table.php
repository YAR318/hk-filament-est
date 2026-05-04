<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_analyses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('original_filename');
            $table->string('file_path');
            $table->string('file_type', 10); // pdf, docx, txt
            $table->unsignedBigInteger('file_size');
            $table->longText('extracted_text');
            $table->longText('summary')->nullable();
            $table->json('key_points')->nullable();
            $table->json('chat_history')->nullable();
            $table->enum('status', ['processing', 'completed', 'failed'])->default('processing');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_analyses');
    }
};
