<?php

namespace Database\Factories;

use App\Models\Assessment;
use App\Models\Question;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Question>
 */
class QuestionFactory extends Factory
{
    protected $model = Question::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'assessment_id' => Assessment::factory(),
            'question_text' => $this->faker->sentence(10),
            'question_text_ar' => $this->faker->sentence(10),
            'question_type' => $this->faker->randomElement(['multiple_choice', 'true_false', 'short_answer', 'essay']),
            'options' => json_encode([
                'A' => $this->faker->sentence(3),
                'B' => $this->faker->sentence(3),
                'C' => $this->faker->sentence(3),
                'D' => $this->faker->sentence(3),
            ]),
            'correct_answer' => 'A',
            'points' => $this->faker->numberBetween(1, 10),
            'order' => $this->faker->numberBetween(1, 20),
        ];
    }

    /**
     * Indicate that the question is multiple choice.
     */
    public function multipleChoice(): static
    {
        return $this->state(fn (array $attributes) => [
            'question_type' => 'multiple_choice',
            'options' => json_encode([
                'A' => $this->faker->sentence(3),
                'B' => $this->faker->sentence(3),
                'C' => $this->faker->sentence(3),
                'D' => $this->faker->sentence(3),
            ]),
        ]);
    }

    /**
     * Indicate that the question is true/false.
     */
    public function trueFalse(): static
    {
        return $this->state(fn (array $attributes) => [
            'question_type' => 'true_false',
            'options' => json_encode(['True' => 'True', 'False' => 'False']),
            'correct_answer' => $this->faker->randomElement(['True', 'False']),
        ]);
    }

    /**
     * Indicate that the question is short answer.
     */
    public function shortAnswer(): static
    {
        return $this->state(fn (array $attributes) => [
            'question_type' => 'short_answer',
            'options' => null,
            'correct_answer' => $this->faker->sentence(5),
        ]);
    }

    /**
     * Indicate that the question is essay.
     */
    public function essay(): static
    {
        return $this->state(fn (array $attributes) => [
            'question_type' => 'essay',
            'options' => null,
            'correct_answer' => null,
        ]);
    }
}
