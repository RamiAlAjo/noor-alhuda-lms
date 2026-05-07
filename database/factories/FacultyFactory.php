<?php

namespace Database\Factories;

use App\Models\Faculty;
use Illuminate\Database\Eloquent\Factories\Factory;

class FacultyFactory extends Factory
{
    protected $model = Faculty::class;

    public function definition(): array
    {
        return [
            'name' => fake()->randomElement([
                'Faculty of Science',
                'Faculty of Engineering',
                'Faculty of Arts',
                'Faculty of Business',
                'Faculty of Medicine',
                'Faculty of Law',
            ]),
            'name_ar' => fake()->randomElement([
                'كلية العلوم',
                'كلية الهندسة',
                'كلية الآداب',
                'كلية الأعمال',
                'كلية الطب',
                'كلية القانون',
            ]),
            'code' => strtoupper(fake()->unique()->lexify('???')),
            'description' => fake()->paragraph(),
            'dean_name' => fake()->name(),
            'email' => fake()->companyEmail(),
            'phone' => fake()->phoneNumber(),
            'is_active' => true,
        ];
    }
}
