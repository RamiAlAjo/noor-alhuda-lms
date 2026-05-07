<?php

namespace Tests\Unit\Models;

use App\Models\Payment;
use App\Models\StudentFee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_payment_with_valid_data()
    {
        $student = User::factory()->create();
        $studentFee = StudentFee::factory()->create();

        $data = [
            'student_id' => $student->id,
            'student_fee_id' => $studentFee->id,
            'amount' => 1000.00,
            'payment_method' => 'cash',
            'transaction_id' => 'TXN-12345',
            'status' => 'completed',
            'payment_gateway' => 'manual',
            'gateway_transaction_id' => 'GT-12345',
            'notes' => 'Payment received',
        ];

        $payment = Payment::create($data);

        $this->assertDatabaseHas('payments', $data);
        $this->assertInstanceOf(Payment::class, $payment);
    }

    /** @test */
    public function it_belongs_to_a_student()
    {
        $student = User::factory()->create();
        $payment = Payment::factory()->create(['student_id' => $student->id]);

        $this->assertInstanceOf(User::class, $payment->student);
        $this->assertEquals($student->id, $payment->student->id);
    }

    /** @test */
    public function it_belongs_to_a_student_fee()
    {
        $studentFee = StudentFee::factory()->create();
        $payment = Payment::factory()->create(['student_fee_id' => $studentFee->id]);

        $this->assertInstanceOf(StudentFee::class, $payment->studentFee);
        $this->assertEquals($studentFee->id, $payment->studentFee->id);
    }

    /** @test */
    public function it_belongs_to_an_approver()
    {
        $approver = User::factory()->create();
        $payment = Payment::factory()->create([
            'approved_by' => $approver->id,
            'approved_at' => now(),
        ]);

        $this->assertInstanceOf(User::class, $payment->approver);
        $this->assertEquals($approver->id, $payment->approver->id);
    }

    /** @test */
    public function it_can_get_fee_through_student_fee_relationship()
    {
        $student = User::factory()->create();
        $fee = \App\Models\Fee::factory()->create();

        $studentFee = StudentFee::factory()->create([
            'student_id' => $student->id,
            'fee_id' => $fee->id,
        ]);

        $payment = Payment::factory()->create([
            'student_id' => $student->id,
            'student_fee_id' => $studentFee->id,
        ]);

        $this->assertInstanceOf(\App\Models\Fee::class, $payment->fee);
        $this->assertEquals($fee->id, $payment->fee->id);
    }

    /** @test */
    public function it_can_be_approved()
    {
        $approver = User::factory()->create();
        $payment = Payment::factory()->create(['status' => 'pending']);

        $payment->update([
            'status' => 'completed',
            'approved_by' => $approver->id,
            'approved_at' => now(),
        ]);

        $this->assertEquals('completed', $payment->fresh()->status);
        $this->assertEquals($approver->id, $payment->fresh()->approved_by);
        $this->assertNotNull($payment->fresh()->approved_at);
    }

    /** @test */
    public function it_can_have_different_payment_methods()
    {
        $methods = ['cash', 'card', 'bank_transfer', 'paypal', 'stripe'];

        foreach ($methods as $method) {
            $payment = Payment::factory()->create(['payment_method' => $method]);
            $this->assertEquals($method, $payment->payment_method);
        }
    }

    /** @test */
    public function it_can_have_different_statuses()
    {
        $statuses = ['pending', 'completed', 'failed', 'refunded'];

        foreach ($statuses as $status) {
            $payment = Payment::factory()->create(['status' => $status]);
            $this->assertEquals($status, $payment->status);
        }
    }
}