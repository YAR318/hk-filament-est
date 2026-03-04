<?php

namespace App\Jobs;

use App\Models\Appointment;
use App\Models\AppSetting;
use App\Mail\AppointmentReminder;
use App\Services\WhatsAppService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendAppointmentReminders implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(WhatsAppService $whatsAppService): void
    {
        // Recordatorios de 24h
        $appointments24h = Appointment::needsReminder24h()->get();
        foreach ($appointments24h as $appointment) {
            $this->sendReminder($appointment, '24h', $whatsAppService);
            $appointment->update(['reminder_24h_sent' => true]);
        }

        // Recordatorios de 8h
        $appointments8h = Appointment::needsReminder8h()->get();
        foreach ($appointments8h as $appointment) {
            $this->sendReminder($appointment, '8h', $whatsAppService);
            $appointment->update(['reminder_8h_sent' => true]);
        }

        // Recordatorios de 1h
        $appointments1h = Appointment::needsReminder1h()->get();
        foreach ($appointments1h as $appointment) {
            $this->sendReminder($appointment, '1h', $whatsAppService);
            $appointment->update(['reminder_1h_sent' => true]);
        }

        $total = $appointments24h->count() + $appointments8h->count() + $appointments1h->count();
        if ($total > 0) {
            Log::info("Recordatorios enviados: {$total}", [
                '24h' => $appointments24h->count(),
                '8h' => $appointments8h->count(),
                '1h' => $appointments1h->count(),
            ]);
        }
    }

    protected function sendReminder(Appointment $appointment, string $type, WhatsAppService $whatsAppService): void
    {
        $timeLabel = match ($type) {
            '1h' => '1 hora',
            '8h' => '8 horas',
            default => '24 horas',
        };

        // Email al encargado
        $adminEmail = AppSetting::getAdminEmail();
        if ($adminEmail) {
            try {
                Mail::to($adminEmail)->send(new AppointmentReminder($appointment, $type));
            } catch (\Exception $e) {
                Log::warning("Error enviando recordatorio email ({$type}) al encargado", [
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // Email al cliente
        if ($appointment->client_email) {
            try {
                Mail::to($appointment->client_email)->send(new AppointmentReminder($appointment, $type));
            } catch (\Exception $e) {
                Log::warning("Error enviando recordatorio email ({$type}) al cliente", [
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // WhatsApp al cliente
        try {
            $meetInfo = $appointment->meet_link ? "\n Meet: {$appointment->meet_link}" : '';
            $whatsAppService->sendMessage(
                $appointment->client_phone,
                " Recordatorio: Tienes una cita en {$timeLabel}.\n"
                . " {$appointment->scheduled_at->format('d/m/Y')}\n"
                . " {$appointment->scheduled_at->format('H:i')}"
                . $meetInfo
            );
        } catch (\Exception $e) {
            Log::warning("Error enviando recordatorio WhatsApp ({$type})", [
                'phone' => $appointment->client_phone,
                'error' => $e->getMessage(),
            ]);
        }
    }
}