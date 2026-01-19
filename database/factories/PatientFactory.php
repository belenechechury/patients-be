<?php

namespace Database\Factories;

use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Patient>
 */
class PatientFactory extends Factory
{
    protected $model = Patient::class;

    public function definition(): array
    {
        return [
            'first_name' => 'Juan',
            'last_name' => 'Perez',
            'email' => $this->faker->unique()->userName . '@gmail.com',
            'country_iso' => 'UY',
            'phone_number' => '99123456',
            'document_image_path' => 'documents/test.jpg',
        ];
    }
}
