<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppSetting extends Model
{
    protected $fillable = ['key', 'value', 'description'];

    /**
     * Obtener un valor de configuración
     */
    public static function get(string $key, $default = null): ?string
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    /**
     * Establecer un valor de configuración
     */
    public static function set(string $key, ?string $value): void
    {
        static::updateOrCreate(
        ['key' => $key],
        ['value' => $value]
        );
    }

    /**
     * Obtener el correo del administrador/encargado
     */
    public static function getAdminEmail(): ?string
    {
        return static::get('admin_email');
    }

    /**
     * Obtener hora de inicio laboral
     */
    public static function getWorkStartHour(): int
    {
        return (int)static::get('work_start_hour', '9');
    }

    /**
     * Obtener hora de fin laboral
     */
    public static function getWorkEndHour(): int
    {
        return (int)static::get('work_end_hour', '17');
    }

    /**
     * Obtener días laborales como array
     */
    public static function getWorkDays(): array
    {
        $days = static::get('work_days', '1,2,3,4,5');
        return array_map('intval', explode(',', $days));
    }
}