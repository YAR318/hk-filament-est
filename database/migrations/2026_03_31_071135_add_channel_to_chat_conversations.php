<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table("chat_conversations", function (Blueprint $table) {
            $table->string("channel", 20)->default("evolution")->after("contact_name");
            $table->string("instance_name", 50)->nullable()->after("channel");
        });
    }

    public function down(): void
    {
        Schema::table("chat_conversations", function (Blueprint $table) {
            $table->dropColumn(["channel", "instance_name"]);
        });
    }
};
