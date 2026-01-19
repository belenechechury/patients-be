<?php

namespace Tests\Feature\Patients;

use App\Exceptions\PatientException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class PatientErrorHandlingTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_returns_422_on_validation_errors()
    {
        $response = $this->postJson('/api/patients', []);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors',
            ]);
    }

    /** @test */
    public function it_returns_500_when_service_fails()
    {
        $this->mock(\App\Services\PatientService::class, function ($mock) {
            $mock->shouldReceive('create')
                ->andThrow(new PatientException('Error interno'));
        });

        $response = $this->postJson('/api/patients', [
            'first_name' => 'Ana',
            'last_name' => 'Lopez',
            'email' => 'ana@gmail.com',
            'country_iso' => 'UY',
            'phone_number' => '99112233',
            'document_image' => UploadedFile::fake()->image('doc.jpg'),
        ]);

        $response->assertStatus(500)
            ->assertJson([
                'message' => 'Error interno',
            ]);
    }
}
