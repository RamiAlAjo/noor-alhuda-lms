<?php

namespace Database\Factories;

use App\Models\Assessment;
use App\Models\AssessmentType;
use App\Models\CourseOffering;
use Illuminate\Database\Eloquent\Factories\Factory;

class AssessmentFactory extends Factory
{
    protected $model = Assessment::class;

    public function definition(): array
    {
        return [
            'course_offering_id' => CourseOffering::factory(),
            'assessment_type_id' => AssessmentType::factory(),
            'title' => fake()->randomElement([
                'Quiz 1',
                'Quiz 2',
                'Midterm Exam',
                'Assignment 1',
                'Assignment 2',
                'Project',
                'Final Exam',
            ]),
            'description' => fake()->optional()->paragraph(),
            'max_grade' => fake()->randomElement([10, 20, 25, 30, 50, 100]),
            'weight' => fake()->randomElement([5, 10, 15, 20, 25, 30]),
            'due_date' => fake()->dateTimeBetween('now', '+1 month'),
            'is_published' => fake()->boolean(70),
            'quiz_type' => 'none',
        ];
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_published' => true,
        ]);
    }

    public function preQuiz(): static
    {
        return $this->state(fn (array $attributes) => [
            'quiz_type' => 'pre_quiz',
            'is_published' => true,
        ]);
    }

    public function postQuiz(): static
    {
        return $this->state(fn (array $attributes) => [
            'quiz_type' => 'post_quiz',
            'is_published' => true,
        ]);
    }

    public function timed(int $minutes = 60): static
    {
        return $this->state(fn (array $attributes) => [
            'time_limit_minutes' => $minutes,
        ]);
    }
}
