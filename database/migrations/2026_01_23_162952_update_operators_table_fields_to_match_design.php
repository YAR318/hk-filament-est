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
        Schema::table('operators', function (Blueprint $table) {
            // Update email field to be NOT NULL and UNIQUE
            $table->string('email')->nullable(false)->unique()->change();
            
            // Update phone_number field to be VARCHAR(20) and ensure it's UNIQUE
            $table->string('phone_number', 20)->unique()->change();
            
            // Update name field to be NOT NULL
            $table->string('name')->nullable(false)->change();
            
            // Remove old fields that are no longer needed
            $table->dropColumn([
                'company',
                'notes', 
                'daily_message_limit',
                'hourly_message_limit'
            ]);
            
            // Add missing indexes for optimization
            $table->index('phone_number');
            $table->index('email');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('operators', function (Blueprint $table) {
            // Remove indexes
            $table->dropIndex(['operators_phone_number_index']);
            $table->dropIndex(['operators_email_index']);
            $table->dropIndex(['operators_is_active_index']);
            
            // Add back old fields
            $table->string('company')->nullable()->after('email');
            $table->text('notes')->nullable()->after('company');
            $table->integer('daily_message_limit')->default(50)->after('is_active');
            $table->integer('hourly_message_limit')->default(10)->after('daily_message_limit');
            
            // Revert field changes
            $table->string('email')->nullable()->change();
            $table->string('phone_number')->change(); // Back to default 255
            $table->string('name')->nullable()->change();
        });
    }
};
