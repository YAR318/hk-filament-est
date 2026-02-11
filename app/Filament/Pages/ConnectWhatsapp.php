<?php

namespace App\Filament\Pages;

use App\Services\EvolutionService;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;

class ConnectWhatsapp extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-qr-code';

    protected string $view = 'filament.pages.connect-whatsapp';

    protected static string|\UnitEnum|null $navigationGroup = 'Whatsapp';

    protected static ?string $navigationLabel = 'Conectar Whatsapp';

    protected static ?string $title = 'Conectar Whatsapp';

    protected static ?int $navigationSort = 1;

    public string $connectionState = 'loading';
    public ?string $qrCode = null;
    public ?string $profileName = null;
    public ?string $profilePic = null;
    public ?string $ownerPhone = null;

    /**
     * Verificar si el usuario puede acceder a esta página.
     */
    public static function canAccess(): bool
    {
        return auth()->user()->can('ver_guia_whatsapp');
    }

    public function getTitle(): string|Htmlable
    {
        return 'WhatsApp - Conexión';
    }

    public function mount(): void
    {
        $this->refreshStatus();
    }

    /**
     * Refresh the connection status and instance info
     */
    public function refreshStatus(): void
    {
        try {
            $service = app(EvolutionService::class);

            // Get connection state
            $stateData = $service->getConnectionState();
            $this->connectionState = $stateData['instance']['state'] ?? 'unknown';

            // Get instance info
            $info = $service->getInstanceInfo();
            $instance = $info['instance'] ?? [];
            $this->profileName = $instance['profileName'] ?? null;
            $this->profilePic = $instance['profilePictureUrl'] ?? null;
            $this->ownerPhone = $instance['owner'] ?? null;

            // Clean phone number
            if ($this->ownerPhone) {
                $this->ownerPhone = str_replace('@s.whatsapp.net', '', $this->ownerPhone);
            }

            // If disconnected, try to get QR
            if ($this->connectionState !== 'open') {
                $this->fetchQR();
            }
            else {
                $this->qrCode = null;
            }
        }
        catch (\Exception $e) {
            $this->connectionState = 'error';
            \Illuminate\Support\Facades\Log::error('Error en refreshStatus: ' . $e->getMessage());
        }
    }

    /**
     * Fetch QR code from Evolution API
     */
    public function fetchQR(): void
    {
        $service = app(EvolutionService::class);
        $qrData = $service->getConnectQR();

        // Evolution API returns base64 QR in different formats depending on version
        $this->qrCode = $qrData['base64'] ?? $qrData['qrcode'] ?? null;
    }

    /**
     * Disconnect the instance
     */
    public function disconnect(): void
    {
        $service = app(EvolutionService::class);
        $result = $service->disconnect();

        if ($result) {
            \Filament\Notifications\Notification::make()
                ->title('WhatsApp desconectado')
                ->body('La instancia se ha desvinculado correctamente.')
                ->success()
                ->send();
        }
        else {
            \Filament\Notifications\Notification::make()
                ->title('Error')
                ->body('No se pudo desconectar la instancia.')
                ->danger()
                ->send();
        }

        // Small delay to let the state settle
        sleep(1);
        $this->refreshStatus();
    }

    /**
     * Restart the instance
     */
    public function restartInstance(): void
    {
        $service = app(EvolutionService::class);
        $result = $service->restart();

        if ($result) {
            \Filament\Notifications\Notification::make()
                ->title('Instancia reiniciada')
                ->body('La instancia se está reiniciando...')
                ->success()
                ->send();
        }
        else {
            \Filament\Notifications\Notification::make()
                ->title('Error')
                ->body('No se pudo reiniciar la instancia.')
                ->danger()
                ->send();
        }

        sleep(2);
        $this->refreshStatus();
    }
}