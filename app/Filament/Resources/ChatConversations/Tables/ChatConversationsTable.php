<?php

namespace App\Filament\Resources\ChatConversations\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;

class ChatConversationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('phone_number')
                    ->label('Número')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->icon('heroicon-o-phone'),
                
                TextColumn::make('contact_name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable()
                    ->default('Sin nombre')
                    ->icon('heroicon-o-user'),
                
                TextColumn::make('messages_count')
                    ->label('Mensajes')
                    ->counts('messages')
                    ->badge()
                    ->color('info')
                    ->sortable(),
                
                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'archived' => 'warning',
                        'blocked' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'Activa',
                        'archived' => 'Archivada',
                        'blocked' => 'Bloqueada',
                        default => $state,
                    })
                    ->sortable(),
                
                TextColumn::make('last_message_at')
                    ->label('Último mensaje')
                    ->dateTime()
                    ->sortable()
                    ->since()
                    ->description(fn ($record) => $record->last_message_at?->format('d/m/Y H:i')),
                
                TextColumn::make('created_at')
                    ->label('Creada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'active' => 'Activa',
                        'archived' => 'Archivada',
                        'blocked' => 'Bloqueada',
                    ]),
            ])
            ->recordActions([
                Action::make('view_history')
                    ->label('Ver historial')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->url(fn ($record) => route('filament.admin.resources.chat-conversations.view-history', $record))
                    ->openUrlInNewTab(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('last_message_at', 'desc');
    }
}
