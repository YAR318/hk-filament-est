<?php

namespace App\Filament\Pages;

use App\Jobs\ProcessVideoAnalysis;
use App\Models\VideoAnalysis;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;

class VideoAnalyzerPage extends Page
{
    use WithFileUploads;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-video-camera';
    protected static ?string $navigationLabel = 'Analizar Videos';
    protected static ?string $title = 'Analizador de Reuniones y Videos';
    protected static ?int $navigationSort = 41;

    protected string $view = 'filament.pages.video-analyzer';

    public static function getNavigationGroup(): ?string
    {
        return 'Herramientas IA';
    }

    // Upload
    public $uploadedVideo = null;

    // Current analysis
    public ?int $currentAnalysisId = null;
    public ?string $currentTitle = null;
    public ?string $currentSummary = null;
    public ?array $currentDecisions = null;
    public ?array $currentTasks = null;
    public ?string $currentTranscription = null;
    public ?string $currentStatus = null;
    public ?string $currentStatusLabel = null;
    public ?string $currentFileSize = null;
    public ?string $currentDuration = null;
    public ?string $currentFileType = null;

    /**
     * Al subir un archivo, procesarlo
     */
    public function updatedUploadedVideo(): void
    {
        $this->validate([
            'uploadedVideo' => 'required|file|max:512000|mimes:mp4,webm,mov,avi,mkv,mp3,wav,ogg,m4a',
        ]);

        $this->processVideo();
    }

    /**
     * Subir el video y despachar el Job de procesamiento
     */
    public function processVideo(): void
    {
        if (!$this->uploadedVideo) return;

        try {
            $file = $this->uploadedVideo;
            $originalName = $file->getClientOriginalName();
            $extension = strtolower($file->getClientOriginalExtension());
            $fileSize = $file->getSize();

            // Guardar archivo
            $storedPath = $file->store('video-analyses', 'public');

            // Crear registro en BD
            $analysis = VideoAnalysis::create([
                'user_id' => auth()->id(),
                'title' => pathinfo($originalName, PATHINFO_FILENAME),
                'original_filename' => $originalName,
                'file_path' => $storedPath,
                'file_type' => $extension,
                'file_size' => $fileSize,
                'status' => 'uploading',
            ]);

            // Cargar en la UI inmediatamente
            $this->loadAnalysis($analysis);

            // Despachar job asíncrono
            ProcessVideoAnalysis::dispatch($analysis->id);

            Notification::make()
                ->title('Video recibido')
                ->body('El procesamiento ha comenzado. Se transcribira y analizara automaticamente.')
                ->success()
                ->send();

        } catch (\Exception $e) {
            Notification::make()
                ->title('Error al subir video')
                ->body($e->getMessage())
                ->danger()
                ->send();
        } finally {
            $this->uploadedVideo = null;
        }
    }

    /**
     * Refrescar el estado del análisis actual (polling)
     */
    public function refreshStatus(): void
    {
        if (!$this->currentAnalysisId) return;

        $analysis = VideoAnalysis::find($this->currentAnalysisId);
        if ($analysis) {
            $this->loadAnalysis($analysis);
        }
    }

    /**
     * Toggle de tarea completada
     */
    public function toggleTask(int $index): void
    {
        if (!$this->currentAnalysisId || !$this->currentTasks) return;

        $analysis = VideoAnalysis::find($this->currentAnalysisId);
        if (!$analysis) return;

        $tasks = $analysis->tasks;
        if (isset($tasks[$index])) {
            $tasks[$index]['done'] = !($tasks[$index]['done'] ?? false);
            $analysis->update(['tasks' => $tasks]);
            $this->currentTasks = $tasks;
        }
    }

    /**
     * Cargar un análisis previo del historial
     */
    public function loadHistoric(int $id): void
    {
        $analysis = VideoAnalysis::where('user_id', auth()->id())
            ->where('id', $id)
            ->first();

        if ($analysis) {
            $this->loadAnalysis($analysis);
        }
    }

    /**
     * Eliminar un análisis
     */
    public function deleteAnalysis(int $id): void
    {
        $analysis = VideoAnalysis::where('user_id', auth()->id())
            ->where('id', $id)
            ->first();

        if ($analysis) {
            // Eliminar archivos
            if ($analysis->file_path && Storage::disk('public')->exists($analysis->file_path)) {
                Storage::disk('public')->delete($analysis->file_path);
            }
            if ($analysis->audio_path && Storage::disk('public')->exists($analysis->audio_path)) {
                Storage::disk('public')->delete($analysis->audio_path);
            }

            $analysis->delete();

            if ($this->currentAnalysisId === $id) {
                $this->resetCurrent();
            }

            Notification::make()
                ->title('Video eliminado')
                ->success()
                ->send();
        }
    }

    /**
     * Historial de videos analizados
     */
    public function getHistoryProperty(): \Illuminate\Database\Eloquent\Collection
    {
        return VideoAnalysis::where('user_id', auth()->id())
            ->orderByDesc('created_at')
            ->limit(20)
            ->get();
    }

    /**
     * Cargar un análisis en la vista
     */
    protected function loadAnalysis(VideoAnalysis $analysis): void
    {
        $this->currentAnalysisId = $analysis->id;
        $this->currentTitle = $analysis->title;
        $this->currentSummary = $analysis->summary;
        $this->currentDecisions = $analysis->key_decisions;
        $this->currentTasks = $analysis->tasks;
        $this->currentTranscription = $analysis->transcription;
        $this->currentStatus = $analysis->status;
        $this->currentStatusLabel = $analysis->status_label;
        $this->currentFileSize = $analysis->formatted_size;
        $this->currentDuration = $analysis->formatted_duration;
        $this->currentFileType = $analysis->file_type;
    }

    /**
     * Limpiar estado actual
     */
    protected function resetCurrent(): void
    {
        $this->currentAnalysisId = null;
        $this->currentTitle = null;
        $this->currentSummary = null;
        $this->currentDecisions = null;
        $this->currentTasks = null;
        $this->currentTranscription = null;
        $this->currentStatus = null;
        $this->currentStatusLabel = null;
        $this->currentFileSize = null;
        $this->currentDuration = null;
        $this->currentFileType = null;
    }
}
