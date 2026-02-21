<?php

namespace App\Filament\Pages;

use App\Models\AppSetting;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Livewire\Attributes\Validate;

class AppSettingsPage extends Page
{
    protected static ?string $navigationLabel = 'Configuración';
    protected static ?string $title = 'Configuración del Sistema';

    protected string $view = 'filament.pages.app-settings';

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-cog-6-tooth';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Gestión';
    }

    public static function getNavigationSort(): ?int
    {
        return 50;
    }

    // Propiedades del formulario
    public string $admin_email = '';
    public string $work_start_hour = '9';
    public string $work_end_hour = '17';
    public string $work_days = '1,2,3,4,5';

    public function mount(): void
    {
        $this->admin_email = AppSetting::get('admin_email', '') ?? '';
        $this->work_start_hour = AppSetting::get('work_start_hour', '9') ?? '9';
        $this->work_end_hour = AppSetting::get('work_end_hour', '17') ?? '17';
        $this->work_days = AppSetting::get('work_days', '1,2,3,4,5') ?? '1,2,3,4,5';
    }

    /**
     * Guardar configuración
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
            ->title('Configuración guardada')
            ->body('Los cambios se aplicarán de inmediato.')
            ->success()
            ->send();
    }
}