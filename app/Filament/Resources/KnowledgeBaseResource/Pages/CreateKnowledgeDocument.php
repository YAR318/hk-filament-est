<?php

namespace App\Filament\Resources\KnowledgeBaseResource\Pages;

use App\Filament\Resources\KnowledgeBaseResource;
use App\Services\DocumentParserService;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Storage;

class CreateKnowledgeDocument extends CreateRecord
{
    protected static string $resource = KnowledgeBaseResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $filePath = $data['file_path'] ?? null;

        if ($filePath) {
            $fullPath = Storage::disk('public')->path($filePath);
            $data['file_type'] = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
            $data['file_size'] = file_exists($fullPath) ? filesize($fullPath) : 0;

            // Extract text before saving
            try {
                $parser = new DocumentParserService();
                $data['extracted_text'] = $parser->extractText($fullPath);
            } catch (\Exception $e) {
                $data['extracted_text'] = null;
            }
        } else {
            $data['file_type'] = 'unknown';
            $data['file_size'] = 0;
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        $record = $this->record;

        if ($record->extracted_text) {
            Notification::make()
                ->title('Texto extraído correctamente')
                ->body('Se extrajeron ' . str_word_count($record->extracted_text) . ' palabras del documento.')
                ->success()
                ->send();
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
