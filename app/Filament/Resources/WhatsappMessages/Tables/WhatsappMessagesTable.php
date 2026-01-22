<?php

namespace App\Filament\Resources\WhatsappMessages\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class WhatsappMessagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('from_number')
                    ->label('From')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('message_body')
                    ->label('Message')
                    ->searchable()
                    ->limit(50)
                    ->wrap(),
                TextColumn::make('message_type')
                    ->label('Type')
                    ->badge()
                    ->searchable(),
                TextColumn::make('instance_name')
                    ->label('Instance')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label('Received')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
