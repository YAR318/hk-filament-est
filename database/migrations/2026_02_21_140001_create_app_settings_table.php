<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('app_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // Valores iniciales
        DB::table('app_settings')->insert([
            [
                'key' => 'admin_email',
                'value' => '',
                'description' => 'Correo del encargado para recibir notificaciones de citas',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'work_start_hour',
                'value' => '9',
                'description' => 'Hora de inicio de jornada laboral (formato 24h)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'work_end_hour',
                'value' => '17',
                'description' => 'Hora de fin de jornada laboral (formato 24h)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'work_days',
                'value' => '1,2,3,4,5',
                'description' => 'Días laborales (1=Lunes ... 7=Domingo)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('app_settings');
    }
};