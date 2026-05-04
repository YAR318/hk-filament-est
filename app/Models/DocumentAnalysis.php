<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentAnalysis extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'original_filename',
        'file_path',
        'file_type',
        'file_size',
        'extracted_text',
        'summary',
        'key_points',
        'chat_history',
        'status',
    ];

    protected $casts = [
        'key_points' => 'array',
        'chat_history' => 'array',
        'file_size' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Tamaño formateado para mostrar en la UI
     */
    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->file_size;
        if ($bytes < 1024) return $bytes . ' B';
        if ($bytes < 1048576) return round($bytes / 1024, 1) . ' KB';
        return round($bytes / 1048576, 1) . ' MB';
    }

    /**
     * Ícono según tipo de archivo
     */
    public function getFileIconAttribute(): string
    {
        return match ($this->file_type) {
            'pdf' => 'heroicon-o-document-text',
            'docx', 'doc' => 'heroicon-o-document',
            'txt' => 'heroicon-o-document-minus',
            default => 'heroicon-o-document',
        };
    }

    /**
     * Color del badge según tipo
     */
    public function getFileColorAttribute(): string
    {
        return match ($this->file_type) {
            'pdf' => '#ef4444',
            'docx', 'doc' => '#3b82f6',
            'txt' => '#22c55e',
            default => '#6b7280',
        };
    }
}
