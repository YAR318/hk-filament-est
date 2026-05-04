<?php

namespace App\Mail;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AppointmentReminder extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Appointment $appointment,
        public string $reminderType = '24h' // '24h' o '1h'
    ) {}

    public function envelope(): Envelope
    {
        $timeLabel = $this->reminderType === '1h' ? '1 hora' : '24 horas';
        return new Envelope(
            subject: "⏰ Recordatorio: Cita en {$timeLabel} - {$this->appointment->scheduled_at->format('d/m/Y H:i')}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.appointment-reminder',
        );
    }
}