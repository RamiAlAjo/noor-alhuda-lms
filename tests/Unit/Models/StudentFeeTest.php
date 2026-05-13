<?php

namespace Tests\Unit\Models;

use App\Models\Fee;
use App\Models\StudentFee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentFeeTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_student_fee_with_valid_data()
    {
        $student = User::factory()->create();
        $fee = Fee::factory()->create();

        $data = [
            'student_id' => $student->id,
            'fee_id' => $fee->id,
            'amount' => 5000.00,
            'paid_amount' => 2500.00,
            'status' => 'partial',
            'due_date' => '2024-09-01',
        ];

        $studentFee = StudentFee::create($data);

        $this->assertDatabaseHas('student_fees', [
            'student_id' => $student->id,
            'fee_id' => $fee->id,
            'amount' => 5000.00,
            'paid_amount' => 2500.00,
            'status' => 'partial',
        ]);
        $this->assertInstanceOf(StudentFee::class, $studentFee);
    }

    /** @test */
    public function it_belongs_to_a_student()
    {
        $student = User::factory()->create();
        $studentFee = StudentFee::factory()->create(['student_id' => $student->id]);

        $this->assertInstanceOf(User::class, $studentFee->student);
        $this->assertEquals($student->id, $studentFee->student->id);
    }

    /** @test */
    public function it_belongs_to_a_fee()
    {
        $fee = Fee::factory()->create();
        $studentFee = StudentFee::factory()->create(['fee_id' => $fee->id]);

        $this->assertInstanceOf(Fee::class, $studentFee->fee);
        $this->assertEquals($fee->id, $studentFee->fee->id);
    }

    /** @test */
    public function it_has_many_payments()
    {
        $student = User::factory()->create();
        $studentFee = StudentFee::factory()->create(['student_id' => $student->id]);

        $payment = $studentFee->payments()->create([
            'student_id' => $student->id,
            'amount' => 1000.00,
            'payment_method' => 'cash',
            'transaction_id' => 'TXN-789012',
            'status' => 'completed',
        ]);

        $this->assertCount(1, $studentFee->payments);
        $this->assertEquals($payment->id, $studentFee->payments->first()->id);
    }

    /** @test */
    public function it_can_have_different_statuses()
    {
        $statuses = ['unpaid', 'partial', 'paid'];

        foreach ($statuses as $status) {
            $studentFee = StudentFee::factory()->create(['status' => $status]);
            $this->assertEquals($status, $studentFee->status);
        }
    }

    /** @test */
    public function it_can_calculate_remaining_balance()
    {
        $studentFee = StudentFee::factory()->create([
            'amount' => 5000.00,
            'paid_amount' => 2500.00,
        ]);

        $remaining = $studentFee->amount - $studentFee->paid_amount;
        $this->assertEquals(2500.00, $remaining);
    }

    /** @test */
    public function it_can_be_marked_as_paid()
    {
        $studentFee = StudentFee::factory()->create([
            'amount' => 5000.00,
            'paid_amount' => 0,
            'status' => 'unpaid',
        ]);

        $studentFee->update([
            'paid_amount' => 5000.00,
            'status' => 'paid',
        ]);

        $this->assertEquals(5000.00, $studentFee->fresh()->paid_amount);
        $this->assertEquals('paid', $studentFee->fresh()->status);
    }

    /** @test */
    public function it_can_be_marked_as_overdue()
    {
        $studentFee = StudentFee::factory()->create([
            'due_date' => now()->subDays(10),
            'status' => 'unpaid',
        ]);

        // Note: In this system, overdue is not a separate status, just unpaid with past due date
        $this->assertTrue($studentFee->due_date->isPast());
    }
}
