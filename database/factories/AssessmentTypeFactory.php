<?php

namespace Database\Factories;

use App\Models\AssessmentType;
use Illuminate\Database\Eloquent\Factories\Factory;

class AssessmentTypeFactory extends Factory
{
    protected $model = AssessmentType::class;

    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(['Quiz', 'Midterm Exam', 'Assignment', 'Project', 'Final Exam']),
            'name_ar' => fake()->randomElement(['اختبار قصير', 'امتحان نصفي', 'واجب', 'مشروع', 'امتحان نهائي']),
            'code' => fake()->unique()->lexify('????'),
            'weight' => fake()->randomElement([5, 10, 15, 20, 25, 30]),
            'is_active' => true,
        ];
    }
}
