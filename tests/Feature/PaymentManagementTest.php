<?php

namespace Tests\Feature;

use App\Models\Fee;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentManagementTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles if they don't exist
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'teacher', 'guard_name' => 'web']);
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'student', 'guard_name' => 'web']);

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');
    }

    /** @test */
    public function admin_can_view_payments_list()
    {
        $response = $this->actingAs($this->admin)
            ->get('/admin/payments');

        $response->assertStatus(200);
        $response->assertViewIs('pages.admin.payments.index');
    }

    /** @test */
    public function admin_can_create_payment()
    {
        $student = User::factory()->create();
        $student->assignRole('student');

        $fee = Fee::factory()->create();
        $studentFee = \App\Models\StudentFee::create([
            'student_id' => $student->id,
            'fee_id' => $fee->id,
            'amount' => 100.00,
            'status' => 'unpaid',
        ]);

        $paymentData = [
            'student_id' => $student->id,
            'fee_id' => $fee->id,
            'amount' => 100.00,
            'payment_method' => 'cash',
            'transaction_id' => 'TXN-12345',
            'notes' => 'Test payment',
        ];

        $response = $this->actingAs($this->admin)
            ->post('/admin/payments', $paymentData);

        $response->assertRedirect();
        $this->assertDatabaseHas('payments', [
            'student_id' => $student->id,
            'amount' => 100.00,
            'payment_method' => 'cash',
        ]);
    }

    /** @test */
    public function admin_can_update_payment()
    {
        $payment = Payment::factory()->create();

        $response = $this->actingAs($this->admin)
            ->put("/admin/payments/{$payment->id}", [
                'amount' => 150.00,
                'status' => 'completed',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'amount' => 150.00,
            'status' => 'completed',
        ]);
    }

    /** @test */
    public function admin_can_delete_payment()
    {
        $payment = Payment::factory()->create();

        $response = $this->actingAs($this->admin)
            ->delete("/admin/payments/{$payment->id}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('payments', [
            'id' => $payment->id,
        ]);
    }

    /** @test */
    public function non_admin_cannot_access_payment_management()
    {
        $student = User::factory()->create();
        $student->assignRole('student');

        $response = $this->actingAs($student)
            ->get('/admin/payments');

        $response->assertStatus(403);
    }

    /** @test */
    public function payment_creation_validates_required_fields()
    {
        $response = $this->actingAs($this->admin)
            ->post('/admin/payments', []);

        $response->assertSessionHasErrors([
            'student_id',
            'fee_id',
            'amount',
            'payment_method',
        ]);
    }

    /** @test */
    public function payment_creation_validates_amount_positive()
    {
        $student = User::factory()->create();
        $student->assignRole('student');

        $fee = Fee::factory()->create();

        $response = $this->actingAs($this->admin)
            ->post('/admin/payments', [
                'student_id' => $student->id,
                'fee_id' => $fee->id,
                'amount' => -50.00, // Invalid: negative amount
                'payment_method' => 'credit_card',
                'status' => 'completed',
            ]);

        $response->assertSessionHasErrors(['amount']);
    }

    /** @test */
    public function payment_creation_validates_payment_method()
    {
        $student = User::factory()->create();
        $student->assignRole('student');

        $fee = Fee::factory()->create();

        $response = $this->actingAs($this->admin)
            ->post('/admin/payments', [
                'student_id' => $student->id,
                'fee_id' => $fee->id,
                'amount' => 100.00,
                'payment_method' => 'invalid_method', // Invalid payment method
            ]);

        $response->assertSessionHasErrors(['payment_method']);
    }

    /** @test */
    public function payment_creation_validates_student_exists()
    {
        $fee = Fee::factory()->create();

        $response = $this->actingAs($this->admin)
            ->post('/admin/payments', [
                'student_id' => 99999, // Non-existent student
                'fee_id' => $fee->id,
                'amount' => 100.00,
                'payment_method' => 'credit_card',
                'status' => 'completed',
            ]);

        $response->assertSessionHasErrors(['student_id']);
    }

    /** @test */
    public function payment_creation_validates_fee_exists()
    {
        $student = User::factory()->create();
        $student->assignRole('student');

        $response = $this->actingAs($this->admin)
            ->post('/admin/payments', [
                'student_id' => $student->id,
                'fee_id' => 99999, // Non-existent fee
                'amount' => 100.00,
                'payment_method' => 'credit_card',
                'status' => 'completed',
            ]);

        $response->assertSessionHasErrors(['fee_id']);
    }
}
