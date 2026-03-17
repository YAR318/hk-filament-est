<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AppointmentResource\Pages;
use App\Models\Appointment;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Support\Enums\FontWeight;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;

class AppointmentResource extends Resource
{
    protected static ?string $model = Appointment::class;

    protected static ?string $recordTitleAttribute = 'client_name';

    protected static ?string $navigationLabel = 'Citas';

    protected static ?string $pluralModelLabel = 'Citas';

    protected static ?string $modelLabel = 'Cita';

    public static function getNavigationGroup(): ?string
    {
        return 'Gestión';
    }

    public static function getNavigationSort(): ?int
    {
        return 5;
    }

    public static function getNavigationIcon(): string|BackedEnum|null
    {
        return 'heroicon-o-calendar-days';
    }

    public static function getNavigationBadge(): ?string
    {
        try {
            return static::getModel()::where('status', 'scheduled')
                ->where('scheduled_at', '>=', now())
                ->count() ?: null;
        } catch (\Exception $e) {
            return null;
        }
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Información del Cliente')
                    ->description('Datos de contacto del cliente')
                    ->schema([
                        TextInput::make('client_name')
                            ->label('Nombre del cliente')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Nombre completo'),

                        TextInput::make('client_phone')
                            ->label('Teléfono (WhatsApp)')
                            ->required()
                            ->maxLength(20)
                            ->placeholder('5217531672288')
                            ->rule('regex:/^[0-9]{10,15}$/')
                            ->helperText('Solo dígitos con código de país'),

                        TextInput::make('client_email')
                            ->label('Email (Opcional)')
                            ->email()
                            ->maxLength(255)
                            ->placeholder('cliente@email.com'),
                    ])
                    ->columns(3),

                Section::make('Detalles de la Cita')
                    ->description('Fecha, hora y duración de la cita')
                    ->schema([
                        DateTimePicker::make('scheduled_at')
                            ->label('Fecha y hora')
                            ->required()
                            ->native(false)
                            ->minutesStep(30)
                            ->displayFormat('d/m/Y H:i')
                            ->minDate(now()),

                        Select::make('duration_minutes')
                            ->label('Duración')
                            ->options([
                                15 => '15 minutos',
                                30 => '30 minutos',
                                45 => '45 minutos',
                                60 => '1 hora',
                                90 => '1 hora 30 min',
                                120 => '2 horas',
                            ])
                            ->default(30)
                            ->required(),

                        Select::make('status')
                            ->label('Estado')
                            ->options([
                                'scheduled' => ' Programada',
                                'completed' => ' Completada',
                                'cancelled' => ' Cancelada',
                            ])
                            ->default('scheduled')
                            ->required(),

                        Textarea::make('notes')
                            ->label('Notas')
                            ->maxLength(500)
                            ->placeholder('Notas adicionales sobre la cita')
                            ->columnSpanFull(),
                    ])
                    ->columns(3),

                Section::make('Google Calendar')
                    ->description('Información de integración')
                    ->schema([
                        TextInput::make('google_event_id')
                            ->label('Google Event ID')
                            ->disabled()
                            ->placeholder('Se genera automáticamente'),

                        TextInput::make('meet_link')
                            ->label('Link de Google Meet')
                            ->disabled()
                            ->url()
                            ->placeholder('Se genera automáticamente'),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('client_name')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Medium),

                Tables\Columns\TextColumn::make('client_phone')
                    ->label('Teléfono')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Teléfono copiado'),

                Tables\Columns\TextColumn::make('scheduled_at')
                    ->label('Fecha y Hora')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->color(fn (Appointment $record) =>
                        $record->scheduled_at->isPast() ? 'gray' : 'success'
                    ),

                Tables\Columns\TextColumn::make('duration_minutes')
                    ->label('Duración')
                    ->suffix(' min')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match($state) {
                        'scheduled' => ' Programada',
                        'completed' => ' Completada',
                        'cancelled' => ' Cancelada',
                        'rescheduled' => ' Reagendada',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match($state) {
                        'scheduled' => 'info',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        'rescheduled' => 'warning',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('meet_link')
                    ->label('Meet')
                    ->formatStateUsing(fn (?string $state): string =>
                        $state ? ' Meet Link' : '—'
                    )
                    ->url(fn (?string $state) => $state)
                    ->openUrlInNewTab()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creada')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'scheduled' => 'Programada',
                        'completed' => 'Completada',
                        'cancelled' => 'Cancelada',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('cancel_appointment')
                    ->label('Cancelar')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('¿Cancelar esta cita?')
                    ->modalDescription('Se cancelará el evento en Google Calendar y se notificará al cliente.')
                    ->visible(fn (Appointment $record) => $record->isCancellable())
                    ->action(function (Appointment $record) {
                        app(\App\Services\AppointmentService::class)->cancel($record->id);
                    }),
                DeleteAction::make()
                    ->requiresConfirmation(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->requiresConfirmation(),
                ]),
            ])
            ->defaultSort('scheduled_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAppointments::route('/'),
            'create' => Pages\CreateAppointment::route('/create'),
            'edit' => Pages\EditAppointment::route('/{record}/edit'),
        ];
    }
}