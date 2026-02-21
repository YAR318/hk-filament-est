<?php

namespace App\Services;

use App\Models\Appointment;
use Google\Client as GoogleClient;
use Google\Service\Calendar;
use Google\Service\Calendar\Event;
use Google\Service\Calendar\EventDateTime;
use Google\Service\Calendar\ConferenceSolutionKey;
use Google\Service\Calendar\CreateConferenceRequest;
use Google\Service\Calendar\ConferenceData;
use Illuminate\Support\Facades\Log;

class GoogleCalendarService
{
    protected ?Calendar $calendarService = null;

    /**
     * Obtener cliente autenticado de Google Calendar
     */
    protected function getCalendarService(): Calendar
    {
        if ($this->calendarService) {
            return $this->calendarService;
        }

        $client = new GoogleClient();
        $client->setApplicationName(config('services.google_calendar.app_name', 'HunabKu Calendar'));

        $credentialsPath = config('services.google_calendar.credentials_path');

        if ($credentialsPath && file_exists($credentialsPath)) {
            $client->setAuthConfig($credentialsPath);
        }
        else {
            Log::warning('Google Calendar: No se encontró archivo de credenciales', [
                'path' => $credentialsPath,
            ]);
            throw new \RuntimeException('Google Calendar credentials not configured');
        }

        $client->addScope(Calendar::CALENDAR);
        $client->addScope(Calendar::CALENDAR_EVENTS);

        $this->calendarService = new Calendar($client);
        return $this->calendarService;
    }

    /**
     * Crear un evento en Google Calendar con Google Meet
     *
     * @return array{event_id: string, meet_link: string|null}
     */
    public function createEvent(Appointment $appointment): array
    {
        try {
            $calendarId = config('services.google_calendar.calendar_id', 'primary');
            $service = $this->getCalendarService();

            $startDateTime = $appointment->scheduled_at->toRfc3339String();
            $endDateTime = $appointment->scheduled_at
                ->addMinutes($appointment->duration_minutes)
                ->toRfc3339String();

            $event = new Event([
                'summary' => "Cita con {$appointment->client_name}",
                'description' => implode("\n", array_filter([
                    "📞 Cliente: {$appointment->client_name}",
                    "📱 Teléfono: {$appointment->client_phone}",
                    $appointment->client_email ? "📧 Email: {$appointment->client_email}" : null,
                    $appointment->notes ? "📝 Notas: {$appointment->notes}" : null,
                ])),
                'start' => [
                    'dateTime' => $startDateTime,
                    'timeZone' => config('app.timezone', 'America/Mexico_City'),
                ],
                'end' => [
                    'dateTime' => $endDateTime,
                    'timeZone' => config('app.timezone', 'America/Mexico_City'),
                ],
                'conferenceData' => [
                    'createRequest' => [
                        'requestId' => uniqid('meet-'),
                        'conferenceSolutionKey' => [
                            'type' => 'hangoutsMeet',
                        ],
                    ],
                ],
                'reminders' => [
                    'useDefault' => false,
                    'overrides' => [
                        ['method' => 'email', 'minutes' => 60],
                        ['method' => 'popup', 'minutes' => 10],
                    ],
                ],
            ]);

            // Agregar attendees si hay email del cliente
            $attendees = [];
            if ($appointment->client_email) {
                $attendees[] = ['email' => $appointment->client_email];
            }

            $adminEmail = \App\Models\AppSetting::getAdminEmail();
            if ($adminEmail) {
                $attendees[] = ['email' => $adminEmail];
            }

            if (!empty($attendees)) {
                $event->setAttendees($attendees);
            }

            $createdEvent = $service->events->insert($calendarId, $event, [
                'conferenceDataVersion' => 1,
                'sendUpdates' => 'all',
            ]);

            $meetLink = null;
            if ($createdEvent->getConferenceData()) {
                $entryPoints = $createdEvent->getConferenceData()->getEntryPoints();
                foreach ($entryPoints as $ep) {
                    if ($ep->getEntryPointType() === 'video') {
                        $meetLink = $ep->getUri();
                        break;
                    }
                }
            }

            Log::info('Google Calendar: Evento creado', [
                'event_id' => $createdEvent->getId(),
                'meet_link' => $meetLink,
                'appointment_id' => $appointment->id,
            ]);

            return [
                'event_id' => $createdEvent->getId(),
                'meet_link' => $meetLink,
            ];
        }
        catch (\Exception $e) {
            Log::error('Google Calendar: Error al crear evento', [
                'error' => $e->getMessage(),
                'appointment_id' => $appointment->id,
            ]);
            throw $e;
        }
    }

    /**
     * Cancelar (eliminar) un evento de Google Calendar
     */
    public function cancelEvent(string $eventId): bool
    {
        try {
            $calendarId = config('services.google_calendar.calendar_id', 'primary');
            $service = $this->getCalendarService();

            $service->events->delete($calendarId, $eventId, [
                'sendUpdates' => 'all',
            ]);

            Log::info('Google Calendar: Evento cancelado', ['event_id' => $eventId]);
            return true;
        }
        catch (\Exception $e) {
            Log::error('Google Calendar: Error al cancelar evento', [
                'event_id' => $eventId,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Actualizar fecha/hora de un evento (reagendar)
     *
     * @return array{event_id: string, meet_link: string|null}
     */
    public function updateEvent(string $eventId, Appointment $appointment): array
    {
        try {
            $calendarId = config('services.google_calendar.calendar_id', 'primary');
            $service = $this->getCalendarService();

            $event = $service->events->get($calendarId, $eventId);

            $startDateTime = $appointment->scheduled_at->toRfc3339String();
            $endDateTime = $appointment->scheduled_at
                ->addMinutes($appointment->duration_minutes)
                ->toRfc3339String();

            $event->getStart()->setDateTime($startDateTime);
            $event->getEnd()->setDateTime($endDateTime);
            $event->setSummary("Cita con {$appointment->client_name} (reagendada)");

            $updatedEvent = $service->events->update($calendarId, $eventId, $event, [
                'sendUpdates' => 'all',
            ]);

            $meetLink = null;
            if ($updatedEvent->getConferenceData()) {
                $entryPoints = $updatedEvent->getConferenceData()->getEntryPoints();
                foreach ($entryPoints as $ep) {
                    if ($ep->getEntryPointType() === 'video') {
                        $meetLink = $ep->getUri();
                        break;
                    }
                }
            }

            Log::info('Google Calendar: Evento actualizado', [
                'event_id' => $updatedEvent->getId(),
                'meet_link' => $meetLink,
            ]);

            return [
                'event_id' => $updatedEvent->getId(),
                'meet_link' => $meetLink,
            ];
        }
        catch (\Exception $e) {
            Log::error('Google Calendar: Error al actualizar evento', [
                'event_id' => $eventId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}