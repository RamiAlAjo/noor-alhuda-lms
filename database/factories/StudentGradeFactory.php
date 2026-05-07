<?php

namespace Database\Factories;

use App\Models\Assessment;
use App\Models\StudentGrade;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StudentGrade>
 */
class StudentGradeFactory extends Factory
{
    protected $model = StudentGrade::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'student_id' => User::factory(),
            'assessment_id' => Assessment::factory(),
            'grade' => $this->faker->randomFloat(2, 0, 100),
            'feedback' => $this->faker->optional()->sentence(),
            'graded_by' => User::factory(),
            'graded_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'submission_path' => $this->faker->optional()->filePath(),
            'submission_text' => $this->faker->optional()->paragraph(),
            'submitted_at' => $this->faker->dateTimeBetween('-2 weeks', 'now'),
            'is_late' => $this->faker->boolean(20),
        ];
    }

    /**
     * Indicate that the grade is an A (90-100).
     */
    public function gradeA(): static
    {
        return $this->state(fn (array $attributes) => [
            'grade' => $this->faker->randomFloat(2, 90, 100),
        ]);
    }

    /**
     * Indicate that the grade is a B (80-89).
     */
    public function gradeB(): static
    {
        return $this->state(fn (array $attributes) => [
            'grade' => $this->faker->randomFloat(2, 80, 89),
        ]);
    }

    /**
     * Indicate that the grade is a C (70-79).
     */
    public function gradeC(): static
    {
        return $this->state(fn (array $attributes) => [
            'grade' => $this->faker->randomFloat(2, 70, 79),
        ]);
    }

    /**
     * Indicate that the grade is a D (60-69).
     */
    public function gradeD(): static
    {
        return $this->state(fn (array $attributes) => [
            'grade' => $this->faker->randomFloat(2, 60, 69),
        ]);
    }

    /**
     * Indicate that the grade is an F (below 60).
     */
    public function gradeF(): static
    {
        return $this->state(fn (array $attributes) => [
            'grade' => $this->faker->randomFloat(2, 0, 59),
        ]);
    }

    /**
     * Indicate that the submission is late.
     */
    public function late(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_late' => true,
        ]);
    }

    /**
     * Indicate that the grade has not been submitted yet.
     */
    public function notSubmitted(): static
    {
        return $this->state(fn (array $attributes) => [
            'submission_path' => null,
            'submission_text' => null,
            'submitted_at' => null,
        ]);
    }
}
