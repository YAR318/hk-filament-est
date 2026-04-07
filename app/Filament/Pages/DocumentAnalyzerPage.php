<?php

namespace App\Filament\Pages;

use App\Models\DocumentAnalysis;
use App\Services\DocumentAnalysisService;
use App\Services\DocumentParserService;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;

class DocumentAnalyzerPage extends Page
{
    use WithFileUploads;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-magnifying-glass';
    protected static ?string $navigationLabel = 'Analizar Documentos';
    protected static ?string $title = 'Analizador Inteligente de Documentos';
    protected static ?int $navigationSort = 40;

    protected string $view = 'filament.pages.document-analyzer';

    public static function getNavigationGroup(): ?string
    {
        return 'Herramientas IA';
    }

    // Upload
    public $uploadedFile = null;

    // Current analysis
    public ?int $currentAnalysisId = null;
    public ?string $currentTitle = null;
    public ?string $currentSummary = null;
    public ?array $currentKeyPoints = null;
    public ?string $currentFileType = null;
    public ?string $currentFileSize = null;
    public ?int $currentTextLength = null;

    // Chat
    public string $chatQuestion = '';
    public array $chatMessages = [];

    // State
    public bool $isAnalyzing = false;
    public bool $isChatting = false;

    /**
     * Al subir un archivo, procesarlo automáticamente
     */
    public function updatedUploadedFile(): void
    {
        $this->validate([
            'uploadedFile' => 'required|file|max:10240|mimes:pdf,docx,doc,txt',
        ]);

        $this->analyzeFile();
    }

    /**
     * Procesar el archivo subido
     */
    public function analyzeFile(): void
    {
        if (!$this->uploadedFile) return;

        $this->isAnalyzing = true;

        try {
            $file = $this->uploadedFile;
            $originalName = $file->getClientOriginalName();
            $extension = strtolower($file->getClientOriginalExtension());
            $fileSize = $file->getSize();

            // Guardar archivo
            $storedPath = $file->store('document-analyses', 'public');
            $fullPath = Storage::disk('public')->path($storedPath);

            // Extraer texto usando el servicio existente
            $parser = new DocumentParserService();
            $extractedText = $parser->extractText($fullPath);

            if (empty(trim($extractedText))) {
                throw new \RuntimeException('No se pudo extraer texto del documento. El archivo podría estar vacío o ser una imagen escaneada.');
            }

            // Generar resumen con IA
            $analysisService = new DocumentAnalysisService();
            $result = $analysisService->generateSummary($extractedText);

            // Guardar en BD
            $analysis = DocumentAnalysis::create([
                'user_id' => auth()->id(),
                'title' => pathinfo($originalName, PATHINFO_FILENAME),
                'original_filename' => $originalName,
                'file_path' => $storedPath,
                'file_type' => $extension,
                'file_size' => $fileSize,
                'extracted_text' => $extractedText,
                'summary' => $result['summary'],
                'key_points' => $result['key_points'],
                'chat_history' => [],
                'status' => 'completed',
            ]);

            // Cargar en la UI
            $this->loadAnalysis($analysis);

            Notification::make()
                ->title('Documento analizado exitosamente')
                ->body("Se extrajeron " . number_format(mb_strlen($extractedText)) . " caracteres y se generó el resumen.")
                ->success()
                ->send();

        } catch (\Exception $e) {
            Notification::make()
                ->title('Error al analizar documento')
                ->body($e->getMessage())
                ->danger()
                ->send();
        } finally {
            $this->isAnalyzing = false;
            $this->uploadedFile = null;
        }
    }

    /**
     * Enviar pregunta al chat con el documento
     */
    public function sendChatMessage(): void
    {
        $question = trim($this->chatQuestion);
        if (empty($question) || !$this->currentAnalysisId) return;

        $this->isChatting = true;
        $this->chatQuestion = '';

        try {
            $analysis = DocumentAnalysis::findOrFail($this->currentAnalysisId);

            // Agregar pregunta al historial local
            $this->chatMessages[] = [
                'role' => 'user',
                'content' => $question,
            ];

            // Enviar a la IA
            $service = new DocumentAnalysisService();
            $response = $service->chatWithDocument(
                $analysis->extracted_text,
                $question,
                $this->chatMessages
            );

            // Agregar respuesta
            $this->chatMessages[] = [
                'role' => 'assistant',
                'content' => $response,
            ];

            // Persistir historial en BD
            $analysis->update(['chat_history' => $this->chatMessages]);

        } catch (\Exception $e) {
            $this->chatMessages[] = [
                'role' => 'assistant',
                'content' => 'Error: ' . $e->getMessage(),
            ];
        } finally {
            $this->isChatting = false;
        }
    }

    /**
     * Cargar un análisis previo del historial
     */
    public function loadHistoric(int $id): void
    {
        $analysis = DocumentAnalysis::where('user_id', auth()->id())
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
        $analysis = DocumentAnalysis::where('user_id', auth()->id())
            ->where('id', $id)
            ->first();

        if ($analysis) {
            // Eliminar archivo
            if ($analysis->file_path && Storage::disk('public')->exists($analysis->file_path)) {
                Storage::disk('public')->delete($analysis->file_path);
            }

            $analysis->delete();

            // Si era el actual, limpiar
            if ($this->currentAnalysisId === $id) {
                $this->resetCurrent();
            }

            Notification::make()
                ->title('Documento eliminado')
                ->success()
                ->send();
        }
    }

    /**
     * Limpiar chat actual
     */
    public function clearChat(): void
    {
        $this->chatMessages = [];
        if ($this->currentAnalysisId) {
            DocumentAnalysis::where('id', $this->currentAnalysisId)
                ->update(['chat_history' => []]);
        }
    }

    /**
     * Historial de documentos analizados
     */
    public function getHistoryProperty(): \Illuminate\Database\Eloquent\Collection
    {
        return DocumentAnalysis::where('user_id', auth()->id())
            ->where('status', 'completed')
            ->orderByDesc('created_at')
            ->limit(20)
            ->get();
    }

    /**
     * Cargar un análisis en la vista
     */
    protected function loadAnalysis(DocumentAnalysis $analysis): void
    {
        $this->currentAnalysisId = $analysis->id;
        $this->currentTitle = $analysis->title;
        $this->currentSummary = $analysis->summary;
        $this->currentKeyPoints = $analysis->key_points;
        $this->currentFileType = $analysis->file_type;
        $this->currentFileSize = $analysis->formatted_size;
        $this->currentTextLength = mb_strlen($analysis->extracted_text);
        $this->chatMessages = $analysis->chat_history ?? [];
    }

    /**
     * Limpiar estado actual
     */
    protected function resetCurrent(): void
    {
        $this->currentAnalysisId = null;
        $this->currentTitle = null;
        $this->currentSummary = null;
        $this->currentKeyPoints = null;
        $this->currentFileType = null;
        $this->currentFileSize = null;
        $this->currentTextLength = null;
        $this->chatMessages = [];
    }
}
