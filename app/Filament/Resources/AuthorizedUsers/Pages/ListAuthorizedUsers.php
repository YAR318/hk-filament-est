<?php

namespace App\Filament\Resources\AuthorizedUsers\Pages;

use App\Filament\Resources\AuthorizedUsers\AuthorizedUserResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAuthorizedUsers extends ListRecords
{
    protected static string $resource = AuthorizedUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
