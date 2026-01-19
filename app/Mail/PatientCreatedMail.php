<?php

namespace App\Mail;

use App\Models\Patient;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PatientCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Patient $patient)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Registro de paciente confirmado'
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.patients.created',
            with: [
                'patient' => $this->patient,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
