<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->string('client_name');
            $table->string('client_phone');
            $table->string('client_email')->nullable();
            $table->dateTime('scheduled_at');
            $table->integer('duration_minutes')->default(30);
            $table->string('status')->default('scheduled'); // scheduled, cancelled, completed, rescheduled
            $table->string('google_event_id')->nullable();
            $table->string('meet_link')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('reminder_24h_sent')->default(false);
            $table->boolean('reminder_1h_sent')->default(false);
            $table->timestamps();

            $table->index('client_phone');
            $table->index('scheduled_at');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};