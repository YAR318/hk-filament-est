<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatConversation extends Model
{
    protected $fillable = [
        'phone_number',
        'contact_name',
        'status',
        'last_message_at',
        'metadata',
        'assigned_to',
        'priority',
        'mode',
        'last_human_response_at',
        'response_time_seconds',
        'escalated_at',
        'resolved_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'last_message_at' => 'datetime',
        'last_human_response_at' => 'datetime',
        'escalated_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class, 'conversation_id');
    }

    public function assignedOperator(): BelongsTo
    {
        return $this->belongsTo(Operator::class, 'assigned_to');
    }

    public function latestMessages(int $limit = 10): HasMany
    {
        return $this->messages()->latest('sent_at')->limit($limit);
    }

    /**
     * Obtener historial formateado para LLM
     */
    public function getHistoryForLLM(int $limit = 20): array
    {
        return $this->messages()
            ->latest('sent_at')
            ->limit($limit)
            ->get()
            ->reverse()
            ->map(function ($message) {
                return [
                    'role' => $message->role,
                    'content' => $message->content,
                ];
            })
            ->toArray();
    }
}
