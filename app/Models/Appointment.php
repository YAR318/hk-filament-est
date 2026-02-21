<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class Appointment extends Model
{
    protected $fillable = [
        'client_name',
        'client_phone',
        'client_email',
        'scheduled_at',
        'duration_minutes',
        'status',
        'google_event_id',
        'meet_link',
        'notes',
        'reminder_24h_sent',
        'reminder_1h_sent',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'reminder_24h_sent' => 'boolean',
        'reminder_1h_sent' => 'boolean',
    ];

    /**
     * Constantes de estado
     */
    const STATUS_SCHEDULED = 'scheduled';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_COMPLETED = 'completed';
    const STATUS_RESCHEDULED = 'rescheduled';

    /**
     * Citas próximas (no canceladas)
     */
    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where('scheduled_at', '>=', now())
            ->where('status', self::STATUS_SCHEDULED);
    }

    /**
     * Buscar por teléfono
     */
    public function scopeByPhone(Builder $query, string $phone): Builder
    {
        return $query->where('client_phone', $phone);
    }

    /**
     * Citas que necesitan recordatorio de 24h
     */
    public function scopeNeedsReminder24h(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_SCHEDULED)
            ->where('reminder_24h_sent', false)
            ->whereBetween('scheduled_at', [
            now()->addHours(23),
            now()->addHours(25),
        ]);
    }

    /**
     * Citas que necesitan recordatorio de 1h
     */
    public function scopeNeedsReminder1h(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_SCHEDULED)
            ->where('reminder_1h_sent', false)
            ->whereBetween('scheduled_at', [
            now()->addMinutes(50),
            now()->addMinutes(70),
        ]);
    }

    /**
     * Verificar si la cita es cancelable
     */
    public function isCancellable(): bool
    {
        return $this->status === self::STATUS_SCHEDULED
            && $this->scheduled_at->isFuture();
    }

    /**
     * Verificar si la cita es reagendable
     */
    public function isReschedulable(): bool
    {
        return $this->isCancellable();
    }
}