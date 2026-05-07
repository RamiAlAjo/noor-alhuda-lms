<?php

namespace Database\Factories;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ActivityLogFactory extends Factory
{
    protected $model = ActivityLog::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'action' => fake()->randomElement(['login', 'logout', 'create', 'update', 'delete', 'view', 'export']),
            'entity_type' => fake()->optional()->randomElement(['User', 'Course', 'Enrollment', 'Assessment', 'Grade']),
            'entity_id' => fake()->optional()->numberBetween(1, 100),
            'description' => fake()->sentence(),
            'old_values' => null,
            'new_values' => null,
            'ip_address' => fake()->ipv4(),
            'user_agent' => fake()->userAgent(),
        ];
    }

    public function login(): static
    {
        return $this->state(fn (array $attributes) => [
            'action' => 'login',
            'entity_type' => null,
            'entity_id' => null,
        ]);
    }
}
