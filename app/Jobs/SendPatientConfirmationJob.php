<?php

namespace App\Jobs;

use App\Mail\PatientCreatedMail;
use App\Models\Patient;
use App\Models\PatientNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendPatientConfirmationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 30;

    protected PatientNotification $notification;

    public function __construct(public Patient $patient)
    {
        $this->notification = PatientNotification::create([
            'patient_id' => $patient->id,
            'channel' => 'email',
            'status' => 'pending',
        ]);
    }

    public function handle(): void
    {
        Mail::to($this->patient->email)
            ->send(new PatientCreatedMail($this->patient));

        $this->notification->update([
            'status' => 'sent',
            'sent_at' => now(),
            'error_message' => null,
        ]);
    }

    public function failed(Throwable $exception): void
    {
        $this->notification->update([
            'status' => 'failed',
            'error_message' => $exception->getMessage(),
        ]);

        Log::error('Falló el envío de email al paciente', [
            'patient_id' => $this->patient->id,
            'notification_id' => $this->notification->id,
            'error' => $exception->getMessage(),
        ]);
    }
}
