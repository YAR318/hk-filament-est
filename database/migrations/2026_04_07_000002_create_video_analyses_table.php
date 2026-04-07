<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('video_analyses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('original_filename');
            $table->string('file_path');
            $table->string('audio_path')->nullable();
            $table->string('file_type', 10); // mp4, webm, mov, mp3, wav
            $table->unsignedBigInteger('file_size');
            $table->longText('transcription')->nullable();
            $table->longText('summary')->nullable();
            $table->json('key_decisions')->nullable();
            $table->json('tasks')->nullable();
            $table->integer('duration_seconds')->nullable();
            $table->enum('status', ['uploading', 'extracting_audio', 'transcribing', 'analyzing', 'completed', 'failed'])->default('uploading');
            $table->text('error_message')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('video_analyses');
    }
};
