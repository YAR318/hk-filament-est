<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('chat_conversations', 'is_bot_active')) {
            Schema::table('chat_conversations', function (Blueprint $table) {
                $table->boolean('is_bot_active')->default(true)->after('status');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('chat_conversations', 'is_bot_active')) {
            Schema::table('chat_conversations', function (Blueprint $table) {
                $table->dropColumn('is_bot_active');
            });
        }
    }
};