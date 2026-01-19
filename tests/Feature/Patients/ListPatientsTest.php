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
            ])
            ->assertJsonStructure([
                'data',
                'links' => ['first', 'last', 'prev', 'next'],
                'meta' => ['current_page', 'last_page', 'per_page', 'total'],
            ]);
    }

    /** @test */
    public function it_lists_patients_paginated()
    {
        Patient::factory()->count(15)->create();

        $response = $this->getJson('/api/patients?page=1&pageSize=10');

        $response->assertOk()
            ->assertJsonCount(10, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'first_name',
                        'last_name',
                        'email',
                        'country_iso',
                        'phone_number',
                        'document_image_path',
                        'created_at',
                    ],
                ],
                'links' => ['first', 'last', 'prev', 'next'],
                'meta' => ['current_page', 'last_page', 'per_page', 'total'],
            ]);
    }

    /** @test */
    public function it_can_search_patients_by_name()
    {
        Patient::factory()->create([
            'first_name' => 'Alice',
            'last_name' => 'Smith',
        ]);

        Patient::factory()->create([
            'first_name' => 'Bob',
            'last_name' => 'Johnson',
        ]);

        $response = $this->getJson('/api/patients?search=alice');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment([
                'first_name' => 'Alice',
                'last_name' => 'Smith',
            ]);
    }
}
