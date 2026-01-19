<?php

namespace Tests\Feature\Patients;

use App\Models\Patient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeletePatientTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_deletes_a_patient()
    {
        $patient = Patient::factory()->create();

        $response = $this->deleteJson("/api/patients/{$patient->id}");

        $response->assertNoContent();

        $this->assertDatabaseMissing('patients', [
            'id' => $patient->id,
        ]);
    }

    /** @test */
    public function deleting_a_non_existing_patient_returns_404()
    {
        $response = $this->deleteJson('/api/patients/999');

        $response->assertStatus(404);
    }
}
