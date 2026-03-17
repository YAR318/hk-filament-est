<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('whatsapp_messages', function (Blueprint $table) {
            $table->foreignId('assigned_to')->nullable()->after('raw_data')->constrained('users')->nullOnDelete();
            $table->enum('status', ['pending', 'assigned', 'in_progress', 'resolved', 'closed'])->default('pending')->after('assigned_to');
            $table->text('response')->nullable()->after('status');
            $table->timestamp('responded_at')->nullable()->after('response');
        });
    }

    public function down(): void
    {
        Schema::table('whatsapp_messages', function (Blueprint $table) {
            $table->dropForeign(['assigned_to']);
            $table->dropColumn(['assigned_to', 'status', 'response', 'responded_at']);
        });
    }
};
