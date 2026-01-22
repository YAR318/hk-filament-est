<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuthorizedUser extends Model
{
    protected $fillable = [
        'phone_number',
        'name',
        'email',
        'company',
        'notes',
        'is_active',
        'daily_message_limit',
        'hourly_message_limit',
        'last_message_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_message_at' => 'datetime',
    ];

    /**
     * Verificar si el usuario puede enviar mensajes
     */
    public function canSendMessage(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        // Verificar límite por hora
        $messagesLastHour = WhatsappMessage::where('from_number', $this->phone_number)
            ->where('created_at', '>=', now()->subHour())
            ->count();

        if ($messagesLastHour >= $this->hourly_message_limit) {
            return false;
        }

        // Verificar límite diario
        $messagesToday = WhatsappMessage::where('from_number', $this->phone_number)
            ->whereDate('created_at', today())
            ->count();

        if ($messagesToday >= $this->daily_message_limit) {
            return false;
        }

        return true;
    }

    /**
     * Obtener mensajes del usuario
     */
    public function messages()
    {
        return $this->hasMany(WhatsappMessage::class, 'from_number', 'phone_number');
    }
}

