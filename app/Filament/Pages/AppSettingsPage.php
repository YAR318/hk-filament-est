<?php

namespace App\Filament\Pages;

use App\Models\AppSetting;
use Filament\Pages\Page;
use Filament\Notifications\Notification;

class AppSettingsPage extends Page
{
    protected static ?string $navigationLabel = 'Configuracion';
    protected static ?string $title = 'Configuracion del Sistema';

    protected string $view = 'filament.pages.app-settings';

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-cog-6-tooth';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Gestion';
    }

    public static function getNavigationSort(): ?int
    {
        return 50;
    }

    // Propiedades del formulario - General
    public string $admin_email = '';
    public string $work_start_hour = '9';
    public string $work_end_hour = '17';
    public string $work_days = '1,2,3,4,5';

    // Propiedades del formulario - WhatsApp
    public string $whatsapp_provider = 'evolution';
    public string $meta_phone_id = '';
    public string $meta_access_token = '';
    public string $meta_verify_token = '';

    public function mount(): void
    {
        $this->admin_email = AppSetting::get('admin_email', '') ?? '';
        $this->work_start_hour = AppSetting::get('work_start_hour', '9') ?? '9';
        $this->work_end_hour = AppSetting::get('work_end_hour', '17') ?? '17';
        $this->work_days = AppSetting::get('work_days', '1,2,3,4,5') ?? '1,2,3,4,5';

        $this->whatsapp_provider = AppSetting::get('whatsapp_provider', 'evolution') ?? 'evolution';
        $this->meta_phone_id = AppSetting::get('meta_phone_id', '') ?? '';
        $this->meta_access_token = AppSetting::get('meta_access_token', '') ?? '';
        $this->meta_verify_token = AppSetting::get('meta_verify_token', '') ?? '';
    }

    /**
     * Guardar configuracion general
     */
    public function saveSettings(): void
    {
        $this->validate([
            'admin_email' => 'required|email',
            'work_start_hour' => 'required|integer|min:0|max:23',
            'work_end_hour' => 'required|integer|min:1|max:24|gt:work_start_hour',
            'work_days' => 'required|string',
        ]);

        AppSetting::set('admin_email', $this->admin_email);
        AppSetting::set('work_start_hour', $this->work_start_hour);
        AppSetting::set('work_end_hour', $this->work_end_hour);
        AppSetting::set('work_days', $this->work_days);

        Notification::make()
            ->title('Configuracion guardada')
            ->body('Los cambios se aplicaran de inmediato.')
            ->success()
            ->send();
    }

    /**
     * Guardar configuracion de WhatsApp
     */
    public function saveWhatsAppSettings(): void
    {
        $rules = [
            'whatsapp_provider' => 'required|in:evolution,meta',
        ];

        // Validar credenciales de Meta solo si se selecciona Meta
        if ($this->whatsapp_provider === 'meta') {
            $rules['meta_phone_id'] = 'required|string';
            $rules['meta_access_token'] = 'required|string';
            $rules['meta_verify_token'] = 'required|string';
        }

        $this->validate($rules);

        AppSetting::set('whatsapp_provider', $this->whatsapp_provider);
        AppSetting::set('meta_phone_id', $this->meta_phone_id);
        AppSetting::set('meta_access_token', $this->meta_access_token);
        AppSetting::set('meta_verify_token', $this->meta_verify_token);

        Notification::make()
            ->title('Proveedor WhatsApp actualizado')
            ->body("Proveedor activo: " . strtoupper($this->whatsapp_provider))
            ->success()
            ->send();
    }
}