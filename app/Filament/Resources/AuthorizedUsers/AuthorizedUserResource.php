<?php

namespace App\Filament\Resources\AuthorizedUsers;

use App\Filament\Resources\AuthorizedUsers\Pages\CreateAuthorizedUser;
use App\Filament\Resources\AuthorizedUsers\Pages\EditAuthorizedUser;
use App\Filament\Resources\AuthorizedUsers\Pages\ListAuthorizedUsers;
use App\Filament\Resources\AuthorizedUsers\Schemas\AuthorizedUserForm;
use App\Filament\Resources\AuthorizedUsers\Tables\AuthorizedUsersTable;
use App\Models\AuthorizedUser;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AuthorizedUserResource extends Resource
{
    protected static ?string $model = AuthorizedUser::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return AuthorizedUserForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AuthorizedUsersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAuthorizedUsers::route('/'),
            'create' => CreateAuthorizedUser::route('/create'),
            'edit' => EditAuthorizedUser::route('/{record}/edit'),
        ];
    }
}
