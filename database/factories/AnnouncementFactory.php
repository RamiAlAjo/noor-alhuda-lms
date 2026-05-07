<?php

namespace Database\Factories;

use App\Models\Announcement;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AnnouncementFactory extends Factory
{
    protected $model = Announcement::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => fake()->sentence(4),
            'content' => fake()->paragraphs(2, true),
            'target_type' => fake()->randomElement(['global', 'faculty', 'department', 'course', 'section']),
            'target_faculty_id' => null,
            'target_department_id' => null,
            'target_course_id' => null,
            'target_offering_id' => null,
            'is_published' => fake()->boolean(80),
            'published_at' => fake()->dateTimeBetween('-1 week', 'now'),
        ];
    }

    public function pinned(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_pinned' => true,
        ]);
    }

    public function global(): static
    {
        return $this->state(fn (array $attributes) => [
            'target_type' => 'all',
        ]);
    }
}
