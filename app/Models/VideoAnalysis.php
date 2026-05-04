<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VideoAnalysis extends Model
{
    protected $table = 'video_analyses';

    protected $fillable = [
        'user_id',
        'title',
        'original_filename',
        'file_path',
        'audio_path',
        'file_type',
        'file_size',
        'transcription',
        'summary',
        'key_decisions',
        'tasks',
        'duration_seconds',
        'status',
        'error_message',
    ];

    protected $casts = [
        'key_decisions' => 'array',
        'tasks' => 'array',
        'file_size' => 'integer',
        'duration_seconds' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Tamaño formateado
     */
    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->file_size;
        if ($bytes < 1024) return $bytes . ' B';
        if ($bytes < 1048576) return round($bytes / 1024, 1) . ' KB';
        if ($bytes < 1073741824) return round($bytes / 1048576, 1) . ' MB';
        return round($bytes / 1073741824, 1) . ' GB';
    }

    /**
     * Duración formateada
     */
    public function getFormattedDurationAttribute(): string
    {
        if (!$this->duration_seconds) return '--';

        $hours = floor($this->duration_seconds / 3600);
        $minutes = floor(($this->duration_seconds % 3600) / 60);
        $seconds = $this->duration_seconds % 60;

        if ($hours > 0) {
            return sprintf('%dh %02dm', $hours, $minutes);
        }
        return sprintf('%dm %02ds', $minutes, $seconds);
    }

    /**
     * Texto del status en español
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'uploading' => 'Subiendo archivo',
            'extracting_audio' => 'Extrayendo audio',
            'transcribing' => 'Transcribiendo',
            'analyzing' => 'Analizando con IA',
            'completed' => 'Completado',
            'failed' => 'Error',
            default => $this->status,
        };
    }

    /**
     * Color del status
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'completed' => '#22c55e',
            'failed' => '#ef4444',
            default => '#f59e0b',
        };
    }

    /**
     * Verificar si es un archivo de audio directo
     */
    public function isAudioFile(): bool
    {
        return in_array($this->file_type, ['mp3', 'wav', 'ogg', 'webm', 'm4a']);
    }
}
