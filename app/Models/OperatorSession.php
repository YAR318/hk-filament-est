<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OperatorSession extends Model
{
    protected $fillable = [
        'operator_id',
        'session_start',
        'session_end',
        'messages_sent',
        'conversations_handled',
        'avg_response_time_seconds',
    ];

    protected $casts = [
        'session_start' => 'datetime',
        'session_end' => 'datetime',
        'messages_sent' => 'integer',
        'conversations_handled' => 'integer',
        'avg_response_time_seconds' => 'integer',
    ];

    /**
     * Relación con el operador
     */
    public function operator(): BelongsTo
    {
        return $this->belongsTo(Operator::class);
    }

    /**
     * Verificar si la sesión está activa
     */
    public function isActive(): bool
    {
        return is_null($this->session_end);
    }

    /**
     * Finalizar la sesión
     */
    public function endSession(): void
    {
        $this->update(['session_end' => now()]);
    }

    /**
     * Incrementar contador de mensajes enviados
     */
    public function incrementMessagesSent(): void
    {
        $this->increment('messages_sent');
    }

    /**
     * Incrementar contador de conversaciones manejadas
     */
    public function incrementConversationsHandled(): void
    {
        $this->increment('conversations_handled');
    }

    /**
     * Calcular duración de la sesión en minutos
     */
    public function getDurationInMinutes(): ?int
    {
        if (!$this->session_end) {
            return null;
        }

        return $this->session_start->diffInMinutes($this->session_end);
    }

    /**
     * Scope para sesiones activas
     */
    public function scopeActive($query)
    {
        return $query->whereNull('session_end');
    }

    /**
     * Scope para sesiones de un operador específico
     */
    public function scopeForOperator($query, $operatorId)
    {
        return $query->where('operator_id', $operatorId);
    }
}
