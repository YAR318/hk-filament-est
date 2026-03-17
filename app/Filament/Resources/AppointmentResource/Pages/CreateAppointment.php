<?php

namespace App\Filament\Resources\AppointmentResource\Pages;

use App\Filament\Resources\AppointmentResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateAppointment extends CreateRecord
{
    protected static string $resource = AppointmentResource::class;

    protected function afterCreate(): void
    {
        $record = $this->record;

        // Intentar crear evento en Google Calendar
        try {
            $calendarService = app(\App\Services\GoogleCalendarService::class);
            $result = $calendarService->createEvent($record);

            $record->update([
                'google_event_id' => $result['event_id'],
                'meet_link' => $result['meet_link'],
            ]);

            Notification::make()
                ->title('Evento creado en Google Calendar')
                ->body($result['meet_link'] ? "Meet: {$result['meet_link']}" : 'Sin enlace Meet')
                ->success()
                ->send();
        }
        catch (\Exception $e) {
            Notification::make()
                ->title('Cita creada sin Google Calendar')
                ->body('No se pudo conectar con Google Calendar: ' . $e->getMessage())
                ->warning()
                ->send();
        }
    }
}