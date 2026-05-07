<?php

namespace Database\Factories;

use App\Models\Reminder;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReminderFactory extends Factory
{
    protected $model = Reminder::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => fake()->sentence(4),
            'description' => fake()->optional()->sentence(),
            'remind_at' => fake()->dateTimeBetween('now', '+1 week'),
            'is_read' => false,
            'type' => fake()->randomElement(['general', 'assignment', 'exam', 'announcement']),
        ];
    }

    public function read(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_read' => true,
        ]);
    }

    public function exam(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'exam',
        ]);
    }
}
