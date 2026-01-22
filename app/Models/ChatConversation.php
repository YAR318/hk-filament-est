<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChatConversation extends Model
{
    protected $fillable = [
        'phone_number',
        'contact_name',
        'status',
        'last_message_at',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'last_message_at' => 'datetime',
    ];

    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class, 'conversation_id');
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
