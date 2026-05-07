<?php

namespace Database\Factories;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Notification>
 */
class NotificationFactory extends Factory
{
    protected $model = Notification::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'type' => $this->faker->randomElement(['info', 'success', 'warning', 'error', 'announcement']),
            'title' => $this->faker->sentence(4),
            'content' => $this->faker->paragraph(),
            'link' => $this->faker->optional()->url(),
            'is_read' => $this->faker->boolean(30),
            'read_at' => $this->faker->optional()->dateTimeBetween('-1 week', 'now'),
            'data' => [],
        ];
    }

    /**
     * Indicate that the notification is unread.
     */
    public function unread(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_read' => false,
            'read_at' => null,
        ]);
    }

    /**
     * Indicate that the notification is read.
     */
    public function read(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_read' => true,
            'read_at' => $this->faker->dateTimeBetween('-1 week', 'now'),
        ]);
    }

    /**
     * Indicate that the notification is an announcement.
     */
    public function announcement(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'announcement',
            'title' => 'New Announcement: '.$this->faker->sentence(3),
        ]);
    }

    /**
     * Indicate that the notification is about a grade.
     */
    public function grade(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'success',
            'title' => 'Grade Posted',
            'content' => 'Your grade for '.$this->faker->word().' has been posted.',
        ]);
    }

    /**
     * Indicate that the notification is about an assignment.
     */
    public function assignment(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'info',
            'title' => 'New Assignment',
            'content' => 'A new assignment has been posted in your course.',
        ]);
    }

    /**
     * Indicate that the notification is about attendance.
     */
    public function attendance(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'warning',
            'title' => 'Attendance Alert',
            'content' => 'You have a new attendance record.',
        ]);
    }

    /**
     * Indicate that the notification is about a payment.
     */
    public function payment(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'info',
            'title' => 'Payment Update',
            'content' => 'Your payment status has been updated.',
        ]);
    }
}
