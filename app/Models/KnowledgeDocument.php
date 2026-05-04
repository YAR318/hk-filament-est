<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class KnowledgeDocument extends Model
{
    protected $fillable = [
        'title',
        'file_path',
        'file_type',
        'file_size',
        'extracted_text',
        'is_active',
        'uploaded_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'file_size' => 'integer',
    ];

    /**
     * Solo documentos activos
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Obtener todo el texto de contexto concatenado
     */
    public static function getFullContext(): string
    {
        $documents = static::active()
            ->whereNotNull('extracted_text')
            ->orderBy('created_at')
            ->get();

        if ($documents->isEmpty()) {
            return '';
        }

        return $documents->map(function ($doc) {
            return "--- {$doc->title} ---\n{$doc->extracted_text}";
        })->implode("\n\n");
    }

    /**
     * Tamaño formateado
     */
    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->file_size;
        if ($bytes < 1024) return $bytes . ' B';
        if ($bytes < 1048576) return round($bytes / 1024, 1) . ' KB';
        return round($bytes / 1048576, 1) . ' MB';
    }
}
