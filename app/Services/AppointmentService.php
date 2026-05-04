<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\AppSetting;
use App\Mail\AppointmentConfirmed;
use App\Mail\AppointmentCancelled;
use App\Mail\AppointmentRescheduled;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class AppointmentService
{
    protected GoogleCalendarService $calendarService;
    protected WhatsAppService $whatsAppService;

    public function __construct(GoogleCalendarService $calendarService, WhatsAppService $whatsAppService)
    {
        $this->calendarService = $calendarService;
        $this->whatsAppService = $whatsAppService;
    }

    /**
     * Verificar si un horario está disponible
     */
    public function isAvailable(Carbon $dateTime): bool
    {
        $startHour = AppSetting::getWorkStartHour();
        $endHour = AppSetting::getWorkEndHour();
        $workDays = AppSetting::getWorkDays();

        // Verificar día laboral (Carbon: 1=Lunes, 7=Domingo)
        if (!in_array($dateTime->dayOfWeekIso, $workDays)) {
            return false;
        }

        // Verificar rango horario
        if ($dateTime->hour < $startHour || $dateTime->hour >= $endHour) {
            return false;
        }

        // Verificar que no haya conflicto con otra cita
        $conflict = Appointment::where('status', Appointment::STATUS_SCHEDULED)
            ->where('scheduled_at', '>=', $dateTime->copy()->subMinutes(59))
            ->where('scheduled_at', '<=', $dateTime->copy()->addMinutes(59))
            ->exists();

        return !$conflict;
    }

    /**
     * Obtener horarios disponibles para una fecha
     */
    public function getAvailableSlots(Carbon $date): array
    {
        $startHour = AppSetting::getWorkStartHour();
        $endHour = AppSetting::getWorkEndHour();
        $workDays = AppSetting::getWorkDays();

        // Si no es día laboral, no hay horarios
        if (!in_array($date->dayOfWeekIso, $workDays)) {
            return [];
        }

        $slots = [];
        for ($hour = $startHour; $hour < $endHour; $hour++) {
            foreach ([0, 30] as $minutes) {
                $slotTime = $date->copy()->setTime($hour, $minutes);

                // Solo mostrar horarios futuros
                if ($slotTime->isPast()) {
                    continue;
                }

                if ($this->isAvailable($slotTime)) {
                    $slots[] = $slotTime->format('H:i');
                }
            }
        }

        return $slots;
    }

    /**
     * Crear una nueva cita
     */
    public function create(array $data): array
    {
        $scheduledAt = Carbon::parse($data['scheduled_at']);

        // Auto-expirar citas pasadas que siguen como "scheduled"
        Appointment::markMissedAppointments();

        // Verificar si el cliente ya tiene una cita activa
        $existingAppointment = Appointment::byPhone($data['client_phone'])
            ->upcoming()
            ->first();

        if ($existingAppointment) {
            return [
                'appointment' => $existingAppointment,
                'already_exists' => true,
                'message' => 'Ya tienes una cita programada para el '
                    . $existingAppointment->scheduled_at->format('d/m/Y H:i')
                    . '. Si deseas cambiar la hora, puedes reagendar o cancelar tu cita actual.',
            ];
        }

        if (!$this->isAvailable($scheduledAt)) {
            throw new \InvalidArgumentException(
                'El horario seleccionado no está disponible. Solo se aceptan citas de '
                . AppSetting::getWorkStartHour() . ':00 a '
                . AppSetting::getWorkEndHour() . ':00, de lunes a viernes.'
            );
        }

        // Crear cita en BD
        $appointment = Appointment::create([
            'client_name' => $data['client_name'],
            'client_phone' => $data['client_phone'],
            'client_email' => $data['client_email'] ?? null,
            'scheduled_at' => $scheduledAt,
            'duration_minutes' => $data['duration_minutes'] ?? 30,
            'notes' => $data['notes'] ?? null,
            'status' => Appointment::STATUS_SCHEDULED,
        ]);

        // Crear evento en Google Calendar + Meet (Deshabilitado: se hará desde n8n)

        // Enviar notificaciones
        $this->sendConfirmationNotifications($appointment);

        Log::info('Cita creada exitosamente', [
            'appointment_id' => $appointment->id,
            'client_phone' => $appointment->client_phone,
            'scheduled_at' => $appointment->scheduled_at->toDateTimeString(),
        ]);

        return [
            'appointment' => $appointment,
            'already_exists' => false,
            'message' => 'Cita creada exitosamente',
        ];
    }

    /**
     * Cancelar una cita
     */
    public function cancel(int $appointmentId, ?string $phone = null): Appointment
    {
        $query = Appointment::where('id', $appointmentId);
        if ($phone) {
            $query->where('client_phone', $phone);
        }

        $appointment = $query->firstOrFail();

        if (!$appointment->isCancellable()) {
            throw new \InvalidArgumentException('Esta cita no se puede cancelar.');
        }

        // Cancelar en Google Calendar (Deshabilitado: se hará desde n8n)

        $appointment->update(['status' => Appointment::STATUS_CANCELLED]);

        // Enviar notificaciones de cancelación
        $this->sendCancellationNotifications($appointment);

        Log::info('Cita cancelada', ['appointment_id' => $appointment->id]);

        return $appointment;
    }

    /**
     * Reagendar una cita
     */
    public function reschedule(int $appointmentId, string $newDateTime, ?string $phone = null): Appointment
    {
        $query = Appointment::where('id', $appointmentId);
        if ($phone) {
            $query->where('client_phone', $phone);
        }

        $appointment = $query->firstOrFail();

        if (!$appointment->isReschedulable()) {
            throw new \InvalidArgumentException('Esta cita no se puede reagendar.');
        }

        $newScheduledAt = Carbon::parse($newDateTime);

        if (!$this->isAvailable($newScheduledAt)) {
            throw new \InvalidArgumentException(
                'El nuevo horario no está disponible.'
            );
        }

        $oldDateTime = $appointment->scheduled_at->copy();
        $appointment->update([
            'scheduled_at' => $newScheduledAt,
            'status' => Appointment::STATUS_SCHEDULED,
            'reminder_24h_sent' => false,
            'reminder_1h_sent' => false,
        ]);

        // Actualizar en Google Calendar (Deshabilitado: se hará desde n8n)

        // Enviar notificaciones
        $this->sendRescheduleNotifications($appointment, $oldDateTime);

        Log::info('Cita reagendada', [
            'appointment_id' => $appointment->id,
            'old_date' => $oldDateTime->toDateTimeString(),
            'new_date' => $newScheduledAt->toDateTimeString(),
        ]);

        return $appointment;
    }

    /**
     * Sincronizar un evento de Google Calendar
     */
    public function syncCalendar(int $appointmentId, string $googleEventId, ?string $meetLink): Appointment
    {
        $appointment = Appointment::findOrFail($appointmentId);

        $appointment->update([
            'google_event_id' => $googleEventId,
            'meet_link' => $meetLink,
        ]);

        Log::info('Calendario sincronizado', [
            'appointment_id' => $appointment->id,
            'google_event_id' => $googleEventId,
        ]);

        return $appointment;
    }

    /**
     * Obtener la próxima cita de un cliente por teléfono
     */
    public function getNextAppointmentByPhone(string $phone): ?Appointment
    {
        return Appointment::byPhone($phone)
            ->upcoming()
            ->orderBy('scheduled_at')
            ->first();
    }

    /**
     * Enviar notificaciones de confirmación
     */
    protected function sendConfirmationNotifications(Appointment $appointment): void
    {
        // Email al encargado
        $adminEmail = AppSetting::getAdminEmail();
        if ($adminEmail) {
            try {
                Mail::to($adminEmail)->send(new AppointmentConfirmed($appointment));
            } catch (\Exception $e) {
                Log::warning('Error enviando email de confirmación al encargado', ['error' => $e->getMessage()]);
            }
        }

        // Email al cliente
        if ($appointment->client_email) {
            try {
                Mail::to($appointment->client_email)->send(new AppointmentConfirmed($appointment));
            } catch (\Exception $e) {
                Log::warning('Error enviando email de confirmación al cliente', ['error' => $e->getMessage()]);
            }
        }

        // WhatsApp al cliente (desactivado: n8n ya envía la confirmación)
        // $this->sendWhatsAppConfirmation($appointment);
    }

    /**
     * Enviar notificaciones de cancelación
     */
    protected function sendCancellationNotifications(Appointment $appointment): void
    {
        $adminEmail = AppSetting::getAdminEmail();
        if ($adminEmail) {
            try {
                Mail::to($adminEmail)->send(new AppointmentCancelled($appointment));
            } catch (\Exception $e) {
                Log::warning('Error enviando email de cancelación', ['error' => $e->getMessage()]);
            }
        }

        if ($appointment->client_email) {
            try {
                Mail::to($appointment->client_email)->send(new AppointmentCancelled($appointment));
            } catch (\Exception $e) {
                Log::warning('Error enviando email de cancelación al cliente', ['error' => $e->getMessage()]);
            }
        }

        // WhatsApp
        $this->sendWhatsAppMessage(
            $appointment->client_phone,
            "❌ Tu cita del {$appointment->scheduled_at->format('d/m/Y H:i')} ha sido cancelada."
        );
    }

    /**
     * Enviar notificaciones de reagendar
     */
    protected function sendRescheduleNotifications(Appointment $appointment, Carbon $oldDateTime): void
    {
        $adminEmail = AppSetting::getAdminEmail();
        if ($adminEmail) {
            try {
                Mail::to($adminEmail)->send(new AppointmentRescheduled($appointment, $oldDateTime));
            } catch (\Exception $e) {
                Log::warning('Error enviando email de reagendar', ['error' => $e->getMessage()]);
            }
        }

        if ($appointment->client_email) {
            try {
                Mail::to($appointment->client_email)->send(new AppointmentRescheduled($appointment, $oldDateTime));
            } catch (\Exception $e) {
                Log::warning('Error enviando email de reagendar al cliente', ['error' => $e->getMessage()]);
            }
        }

        // WhatsApp
        $meetInfo = $appointment->meet_link ? "\n📹 Meet: {$appointment->meet_link}" : '';
        $this->sendWhatsAppMessage(
            $appointment->client_phone,
            "🔄 Tu cita ha sido reagendada.\n"
            . "📅 Antes: {$oldDateTime->format('d/m/Y H:i')}\n"
            . "📅 Nueva fecha: {$appointment->scheduled_at->format('d/m/Y H:i')}"
            . $meetInfo
        );
    }

    /**
     * Enviar confirmación por WhatsApp
     */
    protected function sendWhatsAppConfirmation(Appointment $appointment): void
    {
        $meetInfo = $appointment->meet_link ? "\n📹 Google Meet: {$appointment->meet_link}" : '';
        $message = "✅ ¡Cita confirmada!\n"
            . "📅 Fecha: {$appointment->scheduled_at->format('d/m/Y')}\n"
            . "🕐 Hora: {$appointment->scheduled_at->format('H:i')}\n"
            . "⏱ Duración: {$appointment->duration_minutes} minutos"
            . $meetInfo
            . "\n\nPara cancelar o reagendar, escríbenos por este medio.";

        $this->sendWhatsAppMessage($appointment->client_phone, $message);
    }

    /**
     * Enviar mensaje directo por WhatsApp via Evolution API
     */
    protected function sendWhatsAppMessage(string $phone, string $message): void
    {
        try {
            $this->whatsAppService->sendMessage($phone, $message);
        } catch (\Exception $e) {
            Log::warning('Error enviando mensaje WhatsApp', [
                'phone' => $phone,
                'error' => $e->getMessage(),
            ]);
        }
    }
}