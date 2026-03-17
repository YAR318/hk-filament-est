<?php

namespace App\Filament\Resources\OperatorResource\Pages;

use App\Filament\Resources\OperatorResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOperator extends EditRecord
{
    protected static string $resource = OperatorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->requiresConfirmation(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Operador actualizado correctamente';
    }
}