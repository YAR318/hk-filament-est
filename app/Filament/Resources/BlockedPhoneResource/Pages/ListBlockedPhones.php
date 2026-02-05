<?php

namespace App\Filament\Resources\BlockedPhoneResource\Pages;

use App\Filament\Resources\BlockedPhoneResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBlockedPhones extends ListRecords
{
    protected static string $resource = BlockedPhoneResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Bloquear Número')
                ->icon('heroicon-o-plus-circle'),
        ];
    }
}
