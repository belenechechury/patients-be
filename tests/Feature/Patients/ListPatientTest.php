<?php

namespace Tests\Feature\Patients;

use App\Models\Patient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ListPatientsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_returns_an_empty_list_when_no_patients_exist()
    {
        $response = $this->getJson('/api/patients');

        $response->assertOk()
            ->assertJson([
                'data' => [],
            ]);
    }

    /** @test */
    public function it_lists_patients_paginated()
    {
        Patient::factory()->count(15)->create();

        $response = $this->getJson('/api/patients?per_page=10');

        $response->assertOk()
            ->assertJsonCount(10, 'data')
            ->assertJsonStructure([
                'data',
                'links',
                'meta',
            ]);
    }
}
