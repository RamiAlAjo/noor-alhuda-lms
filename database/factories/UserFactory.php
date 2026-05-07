<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        $name = fake()->name();
        $nameParts = explode(' ', $name);

        return [
            'name' => $name,
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->optional()->phoneNumber(),
            'avatar' => null,
            'status' => 'active',
            'is_active' => true,
            'password' => '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (User $user) {
            $nameParts = explode(' ', $user->name);
            $user->profile()->create([
                'first_name' => $nameParts[0] ?? 'First',
                'last_name' => $nameParts[1] ?? 'Last',
            ]);
        });
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
            'status' => 'inactive',
        ]);
    }

    public function suspended(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
            'status' => 'suspended',
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
            'status' => 'pending',
        ]);
    }

    public function admin(): static
    {
        return $this->afterCreating(function (User $user) {
            $user->assignRole('admin');
        });
    }

    public function teacher(): static
    {
        return $this->afterCreating(function (User $user) {
            $user->assignRole('teacher');
        });
    }

    public function student(): static
    {
        return $this->afterCreating(function (User $user) {
            $user->assignRole('student');
        });
    }

    public function withTwoFactor(): static
    {
        return $this->state(fn (array $attributes) => [
            'two_factor_secret' => 'test-secret',
            'two_factor_recovery_codes' => json_encode(['code1', 'code2']),
        ]);
    }
}
