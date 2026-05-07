<?php

namespace Database\Factories;

use App\Models\Assessment;
use App\Models\Question;
use App\Models\StudentAnswer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StudentAnswer>
 */
class StudentAnswerFactory extends Factory
{
    protected $model = StudentAnswer::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'student_id' => User::factory(),
            'question_id' => Question::factory(),
            'assessment_id' => Assessment::factory(),
            'answer' => $this->faker->randomElement(['A', 'B', 'C', 'D', 'True', 'False']),
            'is_correct' => $this->faker->boolean(50),
            'points_earned' => $this->faker->numberBetween(0, 10),
            'feedback' => $this->faker->optional()->sentence(),
            'submitted_at' => $this->faker->dateTimeBetween('-1 week', 'now'),
        ];
    }

    /**
     * Indicate that the answer is correct.
     */
    public function correct(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_correct' => true,
            'points_earned' => 10,
        ]);
    }

    /**
     * Indicate that the answer is incorrect.
     */
    public function incorrect(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_correct' => false,
            'points_earned' => 0,
        ]);
    }

    /**
     * Indicate that the answer has not been submitted yet.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'answer' => null,
            'submitted_at' => null,
        ]);
    }
}
