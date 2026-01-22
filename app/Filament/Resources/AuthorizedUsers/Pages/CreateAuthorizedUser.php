<?php

namespace App\Filament\Resources\AuthorizedUsers\Pages;

use App\Filament\Resources\AuthorizedUsers\AuthorizedUserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAuthorizedUser extends CreateRecord
{
    protected static string $resource = AuthorizedUserResource::class;
}
