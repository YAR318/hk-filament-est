<?php

namespace App\Filament\Resources\AuthorizedUsers\Pages;

use App\Filament\Resources\AuthorizedUsers\AuthorizedUserResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAuthorizedUser extends EditRecord
{
    protected static string $resource = AuthorizedUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
