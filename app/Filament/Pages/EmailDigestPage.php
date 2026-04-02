<?php

namespace App\Filament\Pages;

use App\Models\EmailDigest;
use App\Services\EmailDigestService;
use Filament\Pages\Page;
use Filament\Notifications\Notification;

class EmailDigestPage extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-envelope';
    protected static ?string $navigationLabel = 'Resumen de Correos';
    protected static ?string $title = 'Resumen de Correos con IA';
    protected static ?int $navigationSort = 50;

    protected string $view = 'filament.pages.email-digest';

    public ?string $summary = null;
    public ?int $emailsCount = null;
    public ?array $emailsList = null;
    public bool $isLoading = false;
    public ?string $lastGenerated = null;

    public function mount(): void
    {
        // Cargar el último resumen del día si existe
        $todayDigest = EmailDigest::where('user_id', auth()->id())
            ->where('digest_date', now()->toDateString())
            ->first();

        if ($todayDigest) {
            $this->summary = $todayDigest->summary;
            $this->emailsCount = $todayDigest->emails_count;
            $this->emailsList = $todayDigest->emails_data;
            $this->lastGenerated = $todayDigest->updated_at->format('h:i A');
        }
    }

    public function generateDigest(): void
    {
        $this->isLoading = true;

        try {
            $service = new EmailDigestService();

            // Paso 1: Leer correos
            Notification::make()
                ->title('Conectando a Gmail...')
                ->info()
                ->send();

            $emails = $service->fetchTodaysEmails();

            if (empty($emails)) {
                $this->summary = "📭 No se encontraron correos nuevos el día de hoy.";
                $this->emailsCount = 0;
                $this->emailsList = [];
                $this->isLoading = false;

                Notification::make()
                    ->title('No hay correos nuevos hoy')
                    ->warning()
                    ->send();
                return;
            }

            // Paso 2: Generar resumen con IA
            $summary = $service->generateDigest($emails);

            // Paso 3: Guardar en BD
            $digest = EmailDigest::updateOrCreate(
                [
                    'user_id' => auth()->id(),
                    'digest_date' => now()->toDateString(),
                ],
                [
                    'emails_count' => count($emails),
                    'summary' => $summary,
                    'emails_data' => $emails,
                ]
            );

            $this->summary = $summary;
            $this->emailsCount = count($emails);
            $this->emailsList = $emails;
            $this->lastGenerated = now()->format('h:i A');

            Notification::make()
                ->title('Resumen generado exitosamente')
                ->body("Se analizaron " . count($emails) . " correos.")
                ->success()
                ->send();

        } catch (\Exception $e) {
            Notification::make()
                ->title('Error al generar resumen')
                ->body($e->getMessage())
                ->danger()
                ->send();
        } finally {
            $this->isLoading = false;
        }
    }

    public function getHistoryProperty(): \Illuminate\Database\Eloquent\Collection
    {
        return EmailDigest::where('user_id', auth()->id())
            ->orderByDesc('digest_date')
            ->limit(10)
            ->get();
    }

    public function loadHistoricDigest(int $digestId): void
    {
        $digest = EmailDigest::where('user_id', auth()->id())
            ->where('id', $digestId)
            ->first();

        if ($digest) {
            $this->summary = $digest->summary;
            $this->emailsCount = $digest->emails_count;
            $this->emailsList = $digest->emails_data;
            $this->lastGenerated = $digest->updated_at->format('h:i A') . ' (' . $digest->digest_date->format('d/m/Y') . ')';
        }
    }
}
