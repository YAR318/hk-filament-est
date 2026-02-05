<?php

namespace App\Filament\Resources\BlockedPhoneResource\Pages;

use App\Filament\Resources\BlockedPhoneResource;
use Filament\Resources\Pages\EditRecord;

class EditBlockedPhone extends EditRecord
{
    protected static string $resource = BlockedPhoneResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
