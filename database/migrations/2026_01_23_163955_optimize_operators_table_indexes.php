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
            // Add composite index for the most common query pattern: finding available operators
            // This covers: WHERE is_active = true AND status = 'available' ORDER BY current_chats_count
            $table->index(['is_active', 'status', 'current_chats_count'], 'idx_available_operators');
            
            // Add composite index for role-based queries with availability
            // This covers: WHERE role = 'supervisor' AND is_active = true AND status = 'available'
            $table->index(['role', 'is_active', 'status'], 'idx_role_availability');
            
            // Remove redundant single-column indexes that are covered by composite indexes
            // Keep the unique constraints and primary key
            // The existing single indexes will still be useful for other query patterns
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('operators', function (Blueprint $table) {
            // Drop the composite indexes we added
            $table->dropIndex('idx_available_operators');
            $table->dropIndex('idx_role_availability');
        });
    }
};
