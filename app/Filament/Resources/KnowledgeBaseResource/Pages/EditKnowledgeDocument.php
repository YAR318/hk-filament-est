<?php

namespace App\Filament\Resources\KnowledgeBaseResource\Pages;

use App\Filament\Resources\KnowledgeBaseResource;
use App\Services\DocumentParserService;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;

class EditKnowledgeDocument extends EditRecord
{
    protected static string $resource = KnowledgeBaseResource::class;

    protected function afterSave(): void
    {
        $record = $this->record;
        $filePath = $record->file_path;

        if ($filePath && $record->wasChanged('file_path')) {
            $fullPath = Storage::disk('public')->path($filePath);

            // Update file metadata
            $record->file_type = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
            $record->file_size = file_exists($fullPath) ? filesize($fullPath) : 0;

            // Re-extract text
            try {
                $parser = new DocumentParserService();
                $text = $parser->extractText($fullPath);
                $record->extracted_text = $text;

                Notification::make()
                    ->title('Texto re-extraído correctamente')
                    ->body('Se extrajeron ' . str_word_count($text) . ' palabras del nuevo documento.')
                    ->success()
                    ->send();
            } catch (\Exception $e) {
                Notification::make()
                    ->title('Error al extraer texto')
                    ->body($e->getMessage())
                    ->warning()
                    ->send();
            }

            $record->save();
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
