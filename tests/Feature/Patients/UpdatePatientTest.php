<?php

namespace Tests\Feature\Patients;

use App\Models\Patient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdatePatientTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_updates_only_the_given_fields()
    {
        $patient = Patient::factory()->create([
            'first_name' => 'Juan',
            'last_name' => 'Perez',
        ]);

        $response = $this->putJson("/api/patients/{$patient->id}", [
            'first_name' => 'Carlos',
        ]);

        $response->assertOk()
            ->assertJsonFragment([
                'first_name' => 'Carlos',
                'last_name' => 'Perez',
            ]);
    }

    /** @test */
    public function it_returns_404_when_updating_non_existing_patient()
    {
        $response = $this->putJson('/api/patients/999', [
            'first_name' => 'Test',
        ]);

        $response->assertStatus(404);
    }
}
