<?php

namespace Database\Factories;

use App\Models\CalendarEvent;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CalendarEventFactory extends Factory
{
    protected $model = CalendarEvent::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => fake()->sentence(3),
            'description' => fake()->optional()->sentence(),
            'start_date' => fake()->date('Y-m-d'),
            'start_time' => fake()->time('H:i:s'),
            'end_date' => fake()->date('Y-m-d'),
            'end_time' => fake()->time('H:i:s'),
            'is_all_day' => fake()->boolean(30),
            'color' => fake()->randomElement(['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6']),
            'location' => fake()->optional()->address(),
            'event_type' => fake()->randomElement(['personal', 'exam', 'assignment', 'class', 'meeting', 'other']),
            'reminder_enabled' => fake()->boolean(50),
            'reminder_minutes' => fake()->randomElement([15, 30, 60, 120]),
        ];
    }

    public function allDay(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_all_day' => true,
            'start_time' => null,
            'end_time' => null,
        ]);
    }
}
