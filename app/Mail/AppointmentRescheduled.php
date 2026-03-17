<?php

namespace App\Mail;

use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AppointmentRescheduled extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Appointment $appointment,
        public Carbon $oldDateTime
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🔄 Cita Reagendada - ' . $this->appointment->scheduled_at->format('d/m/Y H:i'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.appointment-rescheduled',
        );
    }
}