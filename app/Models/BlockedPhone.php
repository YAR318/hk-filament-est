<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlockedPhone extends Model
{
    use HasFactory;

    protected $fillable = [
        'phone_number',
        'reason',
        'is_blocked',
        'created_by',
    ];

    protected $casts = [
        'is_blocked' => 'boolean',
    ];

    /**
     * Usuario que creó el bloqueo
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Verificar si un número está bloqueado
     */
    public static function isBlocked(string $phoneNumber): bool
    {
        return self::where('phone_number', $phoneNumber)
            ->where('is_blocked', true)
            ->exists();
    }

    /**
     * Normalizar número de teléfono (quitar caracteres especiales)
     */
    public static function normalizePhone(string $phone): string
    {
        return preg_replace('/\D/', '', $phone);
    }
}
