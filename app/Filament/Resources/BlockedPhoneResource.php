<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BlockedPhoneResource\Pages;
use App\Models\BlockedPhone;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Support\Enums\FontWeight;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;

class BlockedPhoneResource extends Resource
{
    protected static ?string $model = BlockedPhone::class;

    protected static ?string $recordTitleAttribute = 'phone_number';

    protected static ?string $navigationLabel = 'Números Bloqueados';

    protected static ?string $pluralModelLabel = 'Números Bloqueados';

    protected static ?string $modelLabel = 'Número Bloqueado';

    public static function getNavigationGroup(): ?string
    {
        return 'Whatsapp';
    }

    public static function getNavigationSort(): ?int
    {
        return 10;
    }

    public static function getNavigationIcon(): string|BackedEnum|null
    {
        return 'heroicon-o-no-symbol';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Bloquear Número')
                    ->description('Ingresa el número de teléfono que deseas bloquear. El bot no responderá a este número.')
                    ->schema([
                        TextInput::make('phone_number')
                            ->label('Número de Teléfono')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(20)
                            ->placeholder('Ejemplo: 5217531672288')
                            ->helperText('Ingresa el número con código de país (sin + ni espacios)'),

                        TextInput::make('reason')
                            ->label('Motivo del Bloqueo (Opcional)')
                            ->maxLength(255)
                            ->placeholder('Ej: Spam, mensajes molestos, etc.')
                            ->helperText('Describe por qué bloqueas este número'),

                        Toggle::make('is_blocked')
                            ->label('¿Bloquear este número?')
                            ->default(true)
                            ->helperText('Activo = El bot NO responderá a este número'),
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('phone_number')
                    ->label('Número de Teléfono')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Medium)
                    ->copyable()
                    ->copyMessage('Número copiado'),

                Tables\Columns\TextColumn::make('reason')
                    ->label('Razón')
                    ->searchable()
                    ->placeholder('Sin razón especificada')
                    ->limit(30),

                Tables\Columns\IconColumn::make('is_blocked')
                    ->label('Estado')
                    ->boolean()
                    ->trueIcon('heroicon-o-no-symbol')
                    ->falseIcon('heroicon-o-check-circle')
                    ->trueColor('danger')
                    ->falseColor('success'),

                Tables\Columns\TextColumn::make('creator.name')
                    ->label('Creado por')
                    ->placeholder('Sistema')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_blocked')
                    ->label('Estado')
                    ->placeholder('Todos')
                    ->trueLabel('Solo bloqueados')
                    ->falseLabel('Solo permitidos'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
                    ->requiresConfirmation(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->requiresConfirmation(),

                    BulkAction::make('block')
                        ->label('Bloquear Seleccionados')
                        ->icon('heroicon-o-no-symbol')
                        ->color('danger')
                        ->action(function ($records) {
                            $records->each(fn(BlockedPhone $record) => $record->update(['is_blocked' => true]));
                        })
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion(),

                    BulkAction::make('unblock')
                        ->label('Desbloquear Seleccionados')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(function ($records) {
                            $records->each(fn(BlockedPhone $record) => $record->update(['is_blocked' => false]));
                        })
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListBlockedPhones::route('/'),
            'create' => Pages\CreateBlockedPhone::route('/create'),
            'edit' => Pages\EditBlockedPhone::route('/{record}/edit'),
        ];
    }
}