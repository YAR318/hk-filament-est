<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\AppSetting;

return new class extends Migration {
    public function up(): void
    {
        // Agregar campo whatsapp_channel a appointments
        Schema::table('appointments', function (Blueprint $table) {
            $table->string('whatsapp_channel', 20)->default('evolution')->after('meet_link');
        });

        // Agregar settings de Meta API y proveedor por defecto
        $settings = [
            ['key' => 'whatsapp_provider', 'value' => 'evolution'],
            ['key' => 'meta_phone_id', 'value' => ''],
            ['key' => 'meta_access_token', 'value' => ''],
            ['key' => 'meta_verify_token', 'value' => ''],
        ];

        foreach ($settings as $setting) {
            AppSetting::firstOrCreate(
            ['key' => $setting['key']],
            ['value' => $setting['value']]
            );
        }
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn('whatsapp_channel');
        });

        AppSetting::whereIn('key', [
            'whatsapp_provider',
            'meta_phone_id',
            'meta_access_token',
            'meta_verify_token',
        ])->delete();
    }
};