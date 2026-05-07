<?php

namespace Database\Factories;

use App\Models\Fee;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $student = User::factory()->create();
        $fee = Fee::factory()->create();

        $studentFee = \App\Models\StudentFee::firstOrCreate(
            ['student_id' => $student->id, 'fee_id' => $fee->id],
            ['amount' => $fee->amount, 'status' => 'unpaid']
        );

        return [
            'student_id' => $student->id,
            'student_fee_id' => $studentFee->id,
            'amount' => $this->faker->numberBetween(50, 500),
            'payment_method' => $this->faker->randomElement(['cash', 'credit_card', 'bank_transfer']),
            'transaction_id' => 'TXN-'.strtoupper($this->faker->unique()->lexify('??????')),
            'status' => $this->faker->randomElement(['pending', 'completed', 'failed']),
            'approved_by' => null,
            'approved_at' => null,
            'notes' => $this->faker->optional()->sentence(),
        ];
    }

    /**
     * Indicate that the payment is approved.
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
            'approved_by' => User::factory(),
            'approved_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ]);
    }

    /**
     * Indicate that the payment is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    /**
     * Indicate that the payment failed.
     */
    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'failed',
        ]);
    }

    /**
     * Indicate that the payment is via credit card.
     */
    public function creditCard(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_method' => 'credit_card',
        ]);
    }

    /**
     * Indicate that the payment is via bank transfer.
     */
    public function bankTransfer(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_method' => 'bank_transfer',
        ]);
    }
}
