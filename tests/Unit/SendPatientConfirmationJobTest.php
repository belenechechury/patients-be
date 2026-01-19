<?php

namespace Tests\Unit;

use App\Jobs\SendPatientConfirmationJob;
use App\Mail\PatientCreatedMail;
use App\Models\Patient;
use App\Models\PatientNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;
use Throwable;
use Illuminate\Support\Facades\Queue;

class SendPatientConfirmationJobTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_creates_a_pending_notification_when_job_is_dispatched()
    {
        Queue::fake();

        $patient = Patient::factory()->create();

        SendPatientConfirmationJob::dispatch($patient);

        $this->assertDatabaseHas('patient_notifications', [
            'patient_id' => $patient->id,
            'channel' => 'email',
            'status' => 'pending',
        ]);

        Queue::assertPushed(SendPatientConfirmationJob::class);
    }

    /** @test */
    public function it_marks_notification_as_sent_when_email_is_sent_successfully()
    {
        Mail::fake();

        $patient = Patient::factory()->create();

        $job = new SendPatientConfirmationJob($patient);
        $job->handle();

        $this->assertDatabaseHas('patient_notifications', [
            'patient_id' => $patient->id,
            'channel' => 'email',
            'status' => 'sent',
        ]);

        $notification = PatientNotification::where('patient_id', $patient->id)->first();

        $this->assertNotNull($notification->sent_at);

        Mail::assertSent(PatientCreatedMail::class);
    }

    /** @test */
    public function it_marks_notification_as_failed_when_email_sending_fails()
    {
        Mail::fake();

        $patient = Patient::factory()->create();

        $job = new SendPatientConfirmationJob($patient);

        try {
            throw new \Exception('SMTP connection failed');
        } catch (Throwable $exception) {
            $job->failed($exception);
        }

        $this->assertDatabaseHas('patient_notifications', [
            'patient_id' => $patient->id,
            'channel' => 'email',
            'status' => 'failed',
        ]);

        $notification = PatientNotification::where('patient_id', $patient->id)->first();

        $this->assertEquals(
            'SMTP connection failed',
            $notification->error_message
        );
    }
}
