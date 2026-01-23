<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Operator extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'email', 
        'phone_number',
        'role',
        'is_active',
        'max_concurrent_chats',
        'current_chats_count',
        'status',
        'last_activity_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_activity_at' => 'datetime',
    ];

    /**
     * Relación con conversaciones asignadas
     */
    public function assignedConversations(): HasMany
    {
        return $this->hasMany(ChatConversation::class, 'assigned_to');
    }

    /**
     * Relación con mensajes enviados como operador
     */
    public function operatorMessages(): HasMany
    {
        return $this->hasMany(ChatMessage::class, 'operator_id');
    }

    /**
     * Relación con mensajes del usuario (compatibilidad hacia atrás)
     */
    public function messages(): HasMany
    {
        return $this->hasMany(WhatsappMessage::class, 'from_number', 'phone_number');
    }

    /**
     * Relación con sesiones del operador
     */
    public function sessions(): HasMany
    {
        return $this->hasMany(OperatorSession::class);
    }

    /**
     * Verificar si el operador puede tomar más chats
     */
    public function canTakeMoreChats(): bool
    {
        return $this->is_active && 
               $this->status === 'available' && 
               $this->current_chats_count < $this->max_concurrent_chats;
    }

    /**
     * Asignar una conversación al operador
     */
    public function assignConversation(ChatConversation $conversation): bool
    {
        if (!$this->canTakeMoreChats()) {
            return false;
        }

        $conversation->update([
            'assigned_to' => $this->id,
            'status' => 'en_proceso',
            'mode' => 'human'
        ]);

        $this->increment('current_chats_count');
        $this->touch('last_activity_at');

        return true;
    }

    /**
     * Liberar una conversación del operador
     */
    public function releaseConversation(ChatConversation $conversation): void
    {
        $conversation->update([
            'assigned_to' => null,
            'status' => 'nuevo',
            'mode' => 'ai'
        ]);

        $this->decrement('current_chats_count');
    }

    /**
     * Scopes para consultas comunes
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_active', true)
                    ->where('status', 'available');
    }

    public function scopeCanTakeChats($query)
    {
        return $query->available()
                    ->whereRaw('current_chats_count < max_concurrent_chats');
    }

    /**
     * Obtener operador disponible con menos carga
     */
    public static function findAvailableOperator(): ?self
    {
        return self::canTakeChats()
                   ->orderBy('current_chats_count', 'asc')
                   ->first();
    }
}