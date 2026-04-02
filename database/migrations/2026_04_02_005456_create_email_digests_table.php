<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_digests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('digest_date');
            $table->integer('emails_count')->default(0);
            $table->longText('summary')->nullable();
            $table->json('emails_data')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'digest_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_digests');
    }
};
