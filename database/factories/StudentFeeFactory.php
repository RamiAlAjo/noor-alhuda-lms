<?php

namespace Database\Factories;

use App\Models\Fee;
use App\Models\StudentFee;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudentFeeFactory extends Factory
{
    protected $model = StudentFee::class;

    public function definition(): array
    {
        return [
            'student_id' => User::factory(),
            'fee_id' => Fee::factory(),
            'amount' => $this->faker->randomFloat(2, 100, 10000),
            'paid_amount' => 0,
            'status' => $this->faker->randomElement(['unpaid', 'partial', 'paid']),
            'due_date' => $this->faker->dateTimeBetween('now', '+1 year'),
        ];
    }

    public function unpaid(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'unpaid',
            'paid_amount' => 0,
        ]);
    }

    public function partial(): static
    {
        return $this->state(function (array $attributes) {
            $amount = $attributes['amount'] ?? $this->faker->randomFloat(2, 100, 10000);
            $paid = $this->faker->randomFloat(2, 1, $amount - 1);

            return [
                'status' => 'partial',
                'paid_amount' => $paid,
            ];
        });
    }

    public function paid(): static
    {
        return $this->state(function (array $attributes) {
            $amount = $attributes['amount'] ?? $this->faker->randomFloat(2, 100, 10000);

            return [
                'status' => 'paid',
                'paid_amount' => $amount,
            ];
        });
    }
};