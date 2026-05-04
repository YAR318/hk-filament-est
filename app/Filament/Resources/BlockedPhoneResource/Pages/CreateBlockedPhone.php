<?php

namespace App\Filament\Resources\BlockedPhoneResource\Pages;

use App\Filament\Resources\BlockedPhoneResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateBlockedPhone extends CreateRecord
{
    protected static string $resource = BlockedPhoneResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = Auth::id();
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
