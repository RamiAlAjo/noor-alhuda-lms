<?php

namespace Database\Seeders;

use App\Models\Payment;
use App\Models\StudentFee;
use App\Models\User;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $student = User::role('student')->first();

        if (! $student) {
            return;
        }

        $studentFee = StudentFee::where('student_id', $student->id)->first();

        if (! $studentFee) {
            return;
        }

        $payments = [
            [
                'amount' => $studentFee->amount / 2,
                'payment_method' => 'bank_transfer',
                'transaction_id' => 'PAY'.date('Ym').'001',
                'notes' => 'First installment payment',
            ],
        ];

        foreach ($payments as $payment) {
            Payment::firstOrCreate(
                [
                    'student_fee_id' => $studentFee->id,
                    'transaction_id' => $payment['transaction_id'],
                ],
                [
                    'student_id' => $student->id,
                    'amount' => $payment['amount'],
                    'payment_method' => $payment['payment_method'],
                    'status' => 'completed',
                    'notes' => $payment['notes'],
                ]
            );
        }
    }
}
