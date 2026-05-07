<?php

namespace Database\Factories;

use App\Models\Fee;
use App\Models\Major;
use Illuminate\Database\Eloquent\Factories\Factory;

class FeeFactory extends Factory
{
    protected $model = Fee::class;

    public function definition(): array
    {
        return [
            'semester_id' => \App\Models\Semester::factory(),
            'major_id' => Major::factory(),
            'name' => fake()->randomElement([
                'Tuition Fee',
                'Registration Fee',
                'Laboratory Fee',
                'Library Fee',
                'Sports Fee',
                'Exam Fee',
            ]),
            'name_ar' => fake()->randomElement([
                'رسوم tuition',
                'رسوم التسجيل',
                'رسوم المختبر',
                'رسوم المكتبة',
                'رسوم الرياضية',
                'رسوم الامتحان',
            ]),
            'fee_type' => fake()->randomElement(['tuition', 'registration', 'library', 'lab', 'sports', 'exam']),
            'amount' => fake()->randomFloat(2, 50, 5000),
            'academic_year' => date('Y').'-'.(date('Y') + 1),
            'due_date' => fake()->date('Y-m-d', '2026-12-31'),
            'description' => fake()->optional()->sentence(),
            'is_active' => true,
        ];
    }
}
