<?php

namespace Database\Factories;

use App\Models\Assessment;
use App\Models\Grade;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class GradeFactory extends Factory
{
    protected $model = Grade::class;

    public function definition(): array
    {
        return [
            'assessment_id' => Assessment::factory(),
            'user_id' => User::factory(),
            'marks_obtained' => fake()->randomFloat(2, 0, 100),
            'feedback' => fake()->optional()->sentence(),
            'graded_at' => fake()->dateTimeBetween('-1 month', 'now'),
            'graded_by' => User::factory(),
        ];
    }
}
