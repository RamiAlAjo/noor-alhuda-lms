<?php

namespace Tests\Unit\Models;

use App\Models\Fee;
use App\Models\Major;
use App\Models\StudentFee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FeeTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_fee_with_valid_data()
    {
        $major = Major::factory()->create();

        $data = [
            'name' => 'Tuition Fee',
            'name_ar' => 'رسوم الدراسة',
            'fee_type' => 'tuition',
            'amount' => 5000.00,
            'target' => 'all',
            'major_id' => $major->id,
            'academic_year' => '2024-2025',
            'due_date' => '2024-09-01',
            'description' => 'Annual tuition fee',
            'is_active' => true,
        ];

        $fee = Fee::create($data);

        $this->assertDatabaseHas('fees', [
            'name' => 'Tuition Fee',
            'name_ar' => 'رسوم الدراسة',
            'fee_type' => 'tuition',
            'amount' => 5000,
            'target' => 'all',
            'major_id' => $major->id,
            'academic_year' => '2024-2025',
            'description' => 'Annual tuition fee',
            'is_active' => 1, // Cast as integer
        ]);
        $this->assertInstanceOf(Fee::class, $fee);
    }

    /** @test */
    public function it_belongs_to_a_major()
    {
        $major = Major::factory()->create();
        $fee = Fee::factory()->create(['major_id' => $major->id]);

        $this->assertInstanceOf(Major::class, $fee->major);
        $this->assertEquals($major->id, $fee->major->id);
    }

    /** @test */
    public function it_has_many_payments_through_student_fees()
    {
        $fee = Fee::factory()->create();
        $user = User::factory()->create();

        // Create student fee and payment
        $studentFee = $fee->studentFees()->create([
            'student_id' => $user->id,
            'amount' => $fee->amount,
            'paid_amount' => 0,
            'status' => 'unpaid',
        ]);

        $payment = $studentFee->payments()->create([
            'student_id' => $user->id,
            'amount' => 1000.00,
            'payment_method' => 'cash',
            'transaction_id' => 'TXN-123456',
            'status' => 'completed',
        ]);

        $this->assertCount(1, $fee->studentFees->first()->payments);
        $this->assertEquals($payment->id, $fee->payments->first()->id);
    }

    /** @test */
    public function it_has_many_student_fees()
    {
        $fee = Fee::factory()->create();
        $user = User::factory()->create();

        $studentFee = StudentFee::factory()->create([
            'student_id' => $user->id,
            'fee_id' => $fee->id,
            'status' => 'unpaid',
        ]);

        $this->assertCount(1, $fee->studentFees);
        $this->assertEquals($studentFee->id, $fee->studentFees->first()->id);
    }

    /** @test */
    public function it_can_be_activated_and_deactivated()
    {
        $fee = Fee::factory()->create(['is_active' => true]);
        $this->assertTrue($fee->is_active);

        $fee->update(['is_active' => false]);
        $this->assertFalse($fee->fresh()->is_active);
    }
}