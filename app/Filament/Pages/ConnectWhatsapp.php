<?php

namespace App\Filament\Pages;

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

    /**
     * Verificar si el usuario puede acceder a esta página.
     */
    public static function canAccess(): bool
    {
        return auth()->user()->can('ver_guia_whatsapp');
    }

    public function getTitle(): string|Htmlable
    {
        return 'Guía de Conexión a Whatsapp';
    }
}