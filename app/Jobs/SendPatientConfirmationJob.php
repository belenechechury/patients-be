<?php

namespace App\Jobs;

use App\Mail\PatientCreatedMail;
use App\Models\Patient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendPatientConfirmationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Patient $patient)
    {
    }

    public function handle(): void
    {
        Mail::to($this->patient->email)
            ->send(new PatientCreatedMail($this->patient));
    }
}
