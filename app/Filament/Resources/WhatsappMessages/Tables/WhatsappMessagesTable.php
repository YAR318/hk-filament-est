<?php

namespace App\Filament\Resources\WhatsappMessages\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;

class WhatsappMessagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('from_number')
                    ->label('De')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                
                TextColumn::make('message_body')
                    ->label('Mensaje')
                    ->searchable()
                    ->limit(50)
                    ->wrap()
                    ->tooltip(fn ($record) => $record->message_body),
                
                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'gray',
                        'assigned' => 'warning',
                        'in_progress' => 'info',
                        'resolved' => 'success',
                        'closed' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Pendiente',
                        'assigned' => 'Asignado',
                        'in_progress' => 'En Progreso',
                        'resolved' => 'Resuelto',
                        'closed' => 'Cerrado',
                        default => $state,
                    })
                    ->sortable(),
                
                TextColumn::make('assignedUser.name')
                    ->label('Asignado a')
                    ->searchable()
                    ->sortable()
                    ->default('Sin asignar')
                    ->badge()
                    ->color('primary'),
                
                TextColumn::make('message_type')
                    ->label('Tipo')
                    ->badge()
                    ->searchable()
                    ->toggleable(),
                
                TextColumn::make('instance_name')
                    ->label('Instancia')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('received_at')
                    ->label('Recibido')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                
                TextColumn::make('responded_at')
                    ->label('Respondido')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'pending' => 'Pendiente',
                        'assigned' => 'Asignado',
                        'in_progress' => 'En Progreso',
                        'resolved' => 'Resuelto',
                        'closed' => 'Cerrado',
                    ]),
                
                SelectFilter::make('assigned_to')
                    ->label('Asignado a')
                    ->relationship('assignedUser', 'name')
                    ->preload(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('received_at', 'desc');
    }
}
