<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\UserSetting;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserSettingFactory extends Factory
{
    protected $model = UserSetting::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'theme' => fake()->randomElement(['light', 'dark', 'modern']),
            'locale' => fake()->randomElement(['en', 'ar']),
            'high_contrast' => false,
            'large_text' => false,
            'dyslexia_font' => false,
            'reduced_motion' => false,
            'grayscale' => false,
            'line_spacing' => 'normal',
            'focus_outline' => true,
        ];
    }

    public function dark(): static
    {
        return $this->state(fn (array $attributes) => [
            'theme' => 'dark',
        ]);
    }

    public function arabic(): static
    {
        return $this->state(fn (array $attributes) => [
            'locale' => 'ar',
        ]);
    }

    public function accessibilityEnabled(): static
    {
        return $this->state(fn (array $attributes) => [
            'high_contrast' => true,
            'large_text' => true,
            'dyslexia_font' => true,
            'reduced_motion' => true,
            'grayscale' => false,
        ]);
    }
}
