<?php

namespace App\Filament\Pages;

use App\Models\EmailDigest;
use Filament\Pages\Page;
use Filament\Notifications\Notification;

class EmailDigestPage extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-envelope';
    protected static ?string $navigationLabel = 'Resumen de Correos';
    protected static ?string $title = 'Resumen de Correos con IA';
    protected static ?int $navigationSort = 42;

    public static function getNavigationGroup(): ?string
    {
        return 'Herramientas IA';
    }

    protected string $view = 'filament.pages.email-digest';

    public ?string $summary = null;
    public ?int $emailsCount = null;
    public ?array $emailsList = null;
    public bool $isLoading = false;
    public ?string $lastGenerated = null;
    public ?string $viewingDate = null;

    public function mount(): void
    {
        $todayDigest = EmailDigest::where('user_id', auth()->id())
            ->where('digest_date', now()->toDateString())
            ->first();

        if ($todayDigest) {
            $this->summary = $todayDigest->summary;
            $this->emailsCount = $todayDigest->emails_count;
            $this->emailsList = $todayDigest->emails_data;
            $this->lastGenerated = $todayDigest->updated_at->format('h:i A');
            $this->viewingDate = $todayDigest->digest_date->format('d/m/Y');
        }
    }

    public function generateDigest(): void
    {
        $this->isLoading = true;

        try {
            Notification::make()
                ->title('Llamando a n8n...')
                ->info()
                ->send();

            // Llamada al webhook local de n8n
            $n8nUrl = env('N8N_WEBHOOK_URL', 'http://n8n:5678') . '/webhook/email-digest';
            $response = \Illuminate\Support\Facades\Http::timeout(60)->post($n8nUrl);

            if (!$response->successful()) {
                throw new \Exception('Error al contactar n8n: ' . $response->status());
            }

            $data = $response->json();

            if (!isset($data['success']) || !$data['success']) {
                throw new \Exception('n8n no devolvió un formato correcto.');
            }

            if ($data['email_count'] == 0) {
                $this->summary = "No se encontraron correos nuevos el dia de hoy.";
                $this->emailsCount = 0;
                $this->emailsList = [];
                $this->viewingDate = now()->format('d/m/Y');
                $this->isLoading = false;

                Notification::make()
                    ->title('No hay correos nuevos hoy')
                    ->warning()
                    ->send();
                return;
            }

            $summary = $data['summary'];
            $emailsCount = $data['email_count'];

            $digest = EmailDigest::updateOrCreate(
                [
                    'user_id' => auth()->id(),
                    'digest_date' => now()->toDateString(),
                ],
                [
                    'emails_count' => $emailsCount,
                    'summary' => $summary,
                    'emails_data' => [], // n8n no devuelve la lista completa para no saturar, podemos dejarlo vacio o ajustarlo después.
                ]
            );

            $this->summary = $summary;
            $this->emailsCount = $emailsCount;
            $this->emailsList = [];
            $this->lastGenerated = now()->format('h:i A');
            $this->viewingDate = now()->format('d/m/Y');

            Notification::make()
                ->title('Resumen generado exitosamente')
                ->body("Se analizaron {$emailsCount} correos.")
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
            $this->lastGenerated = $digest->updated_at->format('h:i A');
            $this->viewingDate = $digest->digest_date->format('d/m/Y');
        }
    }
}
