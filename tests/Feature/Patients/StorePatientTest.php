<?php

namespace Tests\Feature\Patients;

use App\Jobs\SendPatientConfirmationJob;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StorePatientTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_creates_a_patient_successfully()
    {
        Queue::fake();
        Storage::fake('public');

        $response = $this->postJson('/api/patients', [
            'first_name' => 'Ana',
            'last_name' => 'Lopez',
            'email' => 'ana@gmail.com',
            'country_iso' => 'UY',
            'phone_number' => '99112233',
            'document_image' => UploadedFile::fake()->image('doc.jpg'),
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.email', 'ana@gmail.com');

        Storage::disk('public')->assertExists('documents');

        Queue::assertPushed(SendPatientConfirmationJob::class);
    }

    /** @test */
    public function it_fails_when_payload_is_empty()
    {
        $response = $this->postJson('/api/patients', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'first_name',
                'last_name',
                'email',
                'country_iso',
                'phone_number',
                'document_image',
            ]);
    }

    /** @test */
    public function it_fails_when_email_is_not_unique()
    {
        \App\Models\Patient::factory()->create([
            'email' => 'test@gmail.com',
        ]);

        $response = $this->postJson('/api/patients', [
            'first_name' => 'Juan',
            'last_name' => 'Perez',
            'email' => 'test@gmail.com',
            'country_iso' => 'UY',
            'phone_number' => '99112233',
            'document_image' => UploadedFile::fake()->image('doc.jpg'),
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('email');
    }

    /** @test */
    public function it_fails_when_document_is_not_jpg()
    {
        $response = $this->postJson('/api/patients', [
            'first_name' => 'Juan',
            'last_name' => 'Perez',
            'email' => 'juan@gmail.com',
            'country_iso' => 'UY',
            'phone_number' => '99112233',
            'document_image' => UploadedFile::fake()->create('doc.png'),
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('document_image');
    }
}
