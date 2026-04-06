<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->string('color', 20)->default('gray')->after('guard_name');
        });

        // Asignar colores por defecto a los roles existentes
        DB::table('roles')->where('name', 'admin')->update(['color' => 'danger']);
        DB::table('roles')->where('name', 'supervisor')->update(['color' => 'warning']);
        DB::table('roles')->where('name', 'operador')->update(['color' => 'success']);
    }

    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn('color');
        });
    }
};
