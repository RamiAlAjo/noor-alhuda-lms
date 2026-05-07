<?php

namespace Database\Factories;

use App\Models\MedicalRecord;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MedicalRecordFactory extends Factory
{
    protected $model = MedicalRecord::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'blood_type' => fake()->randomElement(['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-']),
            'chronic_diseases' => fake()->optional()->sentence(),
            'allergies' => fake()->optional()->sentence(),
            'medications' => fake()->optional()->sentence(),
            'emergency_contact_name' => fake()->name(),
            'emergency_contact_phone' => fake()->phoneNumber(),
            'emergency_contact_relation' => fake()->randomElement(['Father', 'Mother', 'Sibling', 'Spouse', 'Other']),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
