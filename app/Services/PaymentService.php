<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\StudentFee;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Stripe\PaymentIntent;
use Stripe\Stripe;

class PaymentService
{
    /**
     * Process payment through specified gateway.
     */
    public function processPayment(
        StudentFee $studentFee,
        float $amount,
        string $gateway,
        array $gatewayData = []
    ): Payment {
        return match ($gateway) {
            'stripe' => $this->processStripePayment($studentFee, $amount, $gatewayData),
            'paypal' => $this->processPayPalPayment($studentFee, $amount, $gatewayData),
            default => $this->processManualPayment($studentFee, $amount, $gatewayData),
        };
    }

    /**
     * Process payment via Stripe.
     */
    public function processStripePayment(
        StudentFee $studentFee,
        float $amount,
        array $stripeData
    ): Payment {
        try {
            // Set Stripe API key
            Stripe::setApiKey(config('services.stripe.secret'));

            // Create PaymentIntent
            $paymentIntent = PaymentIntent::create([
                'amount' => (int) ($amount * 100), // Stripe uses cents
                'currency' => config('services.stripe.currency', 'usd'),
                'metadata' => [
                    'student_id' => $studentFee->student_id,
                    'student_fee_id' => $studentFee->id,
                    'fee_name' => $studentFee->fee->name ?? 'Fee Payment',
                ],
                'description' => 'Payment for: '.($studentFee->fee->name ?? 'Fee'),
            ]);

            // If this is a confirmed payment (client_secret used on frontend)
            if (! empty($stripeData['payment_intent_id']) && ! empty($stripeData['confirmed'])) {
                $confirmedIntent = PaymentIntent::retrieve($stripeData['payment_intent_id']);

                if ($confirmedIntent->status === 'succeeded') {
                    return $this->createCompletedPayment($studentFee, $amount, 'stripe', $confirmedIntent->id);
                }
            }

            // Return payment with client secret for frontend confirmation
            return Payment::create([
                'student_id' => $studentFee->student_id,
                'student_fee_id' => $studentFee->id,
                'amount' => $amount,
                'payment_method' => 'stripe',
                'payment_gateway' => 'stripe',
                'gateway_transaction_id' => $paymentIntent->id,
                'status' => 'pending',
            ]);

        } catch (\Exception $e) {
            Log::error('Stripe payment error: '.$e->getMessage());
            throw new \Exception('Payment processing failed: '.$e->getMessage());
        }
    }

    /**
     * Process payment via PayPal.
     */
    public function processPayPalPayment(
        StudentFee $studentFee,
        float $amount,
        array $paypalData
    ): Payment {
        try {
            $provider = $this->getPayPalProvider();

            // If we have an order ID, capture it
            if (! empty($paypalData['order_id'])) {
                $order = $provider->capturePaymentOrder($paypalData['order_id']);

                if ($order['status'] === 'COMPLETED') {
                    $transactionId = $order['purchase_units'][0]['payments']['captures'][0]['id'] ?? null;

                    return $this->createCompletedPayment($studentFee, $amount, 'paypal', $transactionId);
                }
            }

            // Otherwise create a new order
            $orderData = [
                'intent' => 'CAPTURE',
                'purchase_units' => [
                    [
                        'reference_id' => $studentFee->id,
                        'description' => $studentFee->fee->name ?? 'Fee Payment',
                        'amount' => [
                            'currency_code' => config('services.paypal.currency', 'USD'),
                            'value' => number_format($amount, 2, '.', ''),
                        ],
                    ],
                ],
                'application_context' => [
                    'return_url' => route('student.payments.success'),
                    'cancel_url' => route('student.payments.cancel'),
                ],
            ];

            $order = $provider->createOrder($orderData);

            // Create pending payment record
            return Payment::create([
                'student_id' => $studentFee->student_id,
                'student_fee_id' => $studentFee->id,
                'amount' => $amount,
                'payment_method' => 'paypal',
                'payment_gateway' => 'paypal',
                'gateway_transaction_id' => $order['id'],
                'status' => 'pending',
            ]);

        } catch (\Exception $e) {
            Log::error('PayPal payment error: '.$e->getMessage());
            throw new \Exception('PayPal payment processing failed: '.$e->getMessage());
        }
    }

    /**
     * Process manual/offline payment (cash, bank transfer).
     */
    public function processManualPayment(
        StudentFee $studentFee,
        float $amount,
        array $data
    ): Payment {
        $payment = Payment::create([
            'student_id' => $studentFee->student_id,
            'student_fee_id' => $studentFee->id,
            'amount' => $amount,
            'payment_method' => $data['method'] ?? 'cash',
            'payment_gateway' => 'manual',
            'transaction_id' => $data['transaction_id'] ?? null,
            'status' => 'pending', // Requires admin approval
            'notes' => $data['notes'] ?? null,
        ]);

        return $payment;
    }

    /**
     * Create a completed payment and update student fee.
     */
    protected function createCompletedPayment(
        StudentFee $studentFee,
        float $amount,
        string $gateway,
        ?string $transactionId
    ): Payment {
        return DB::transaction(function () use ($studentFee, $amount, $gateway, $transactionId) {
            // Create payment record
            $payment = Payment::create([
                'student_id' => $studentFee->student_id,
                'student_fee_id' => $studentFee->id,
                'amount' => $amount,
                'payment_method' => $gateway,
                'payment_gateway' => $gateway,
                'gateway_transaction_id' => $transactionId,
                'status' => 'completed',
            ]);

            // Update student fee
            $studentFee->paid_amount += $amount;

            if ($studentFee->paid_amount >= $studentFee->amount) {
                $studentFee->status = 'paid';
            } elseif ($studentFee->paid_amount > 0) {
                $studentFee->status = 'partial';
            }

            $studentFee->save();

            return $payment;
        });
    }

    /**
     * Handle payment webhook from Stripe.
     */
    public function handleStripeWebhook(array $payload, ?string $signature = null): void
    {
        // Verify webhook signature if provided
        if ($signature) {
            try {
                $webhookSecret = config('services.stripe.webhook_secret');
                if ($webhookSecret) {
                    \Stripe\Webhook::constructEvent(
                        json_encode($payload),
                        $signature,
                        $webhookSecret
                    );
                }
            } catch (\Exception $e) {
                Log::error('Stripe webhook signature verification failed: '.$e->getMessage());
                throw new \Exception('Invalid webhook signature');
            }
        }

        $event = $payload['type'];
        $data = $payload['data']['object'];

        if ($event === 'payment_intent.succeeded') {
            $paymentIntentId = $data['id'];

            $payment = Payment::where('gateway_transaction_id', $paymentIntentId)->first();

            if ($payment && $payment->status === 'pending') {
                DB::transaction(function () use ($payment) {
                    // Lock the payment row to prevent race conditions
                    $payment = Payment::lockForUpdate()->find($payment->id);

                    if ($payment->status !== 'pending') {
                        return; // Already processed
                    }

                    $payment->update(['status' => 'completed']);

                    // Update student fee with row locking
                    $studentFee = StudentFee::lockForUpdate()->find($payment->student_fee_id);

                    if ($studentFee) {
                        $studentFee->paid_amount += $payment->amount;

                        if ($studentFee->paid_amount >= $studentFee->amount) {
                            $studentFee->status = 'paid';
                        } elseif ($studentFee->paid_amount > 0) {
                            $studentFee->status = 'partial';
                        }

                        $studentFee->save();
                    }
                });
            }
        }
    }

    /**
     * Get PayPal provider instance.
     */
    protected function getPayPalProvider(): PayPalClient
    {
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('services.paypal'));
        $provider->getAccessToken();

        return $provider;
    }

    /**
     * Get available payment gateways.
     */
    public static function getAvailableGateways(): array
    {
        return [
            'stripe' => [
                'name' => 'Credit/Debit Card (Stripe)',
                'icon' => 'credit-card',
                'enabled' => config('services.stripe.enabled', false),
            ],
            'paypal' => [
                'name' => 'PayPal',
                'icon' => 'paypal',
                'enabled' => config('services.paypal.enabled', false),
            ],
            'cash' => [
                'name' => 'Cash',
                'icon' => 'cash',
                'enabled' => true,
            ],
            'bank_transfer' => [
                'name' => 'Bank Transfer',
                'icon' => 'bank',
                'enabled' => true,
            ],
        ];
    }

    /**
     * Process refund via Stripe.
     */
    public function refundStripePayment(Payment $payment, ?float $amount = null): bool
    {
        try {
            Stripe::setApiKey(config('services.stripe.secret'));

            $refundAmount = $amount ?? $payment->amount;

            \Stripe\Refund::create([
                'payment_intent' => $payment->gateway_transaction_id,
                'amount' => (int) ($refundAmount * 100),
            ]);

            $payment->update(['status' => 'refunded']);

            // Update student fee
            $studentFee = $payment->studentFee;
            $studentFee->paid_amount -= $refundAmount;
            $studentFee->status = $studentFee->paid_amount > 0 ? 'partial' : 'pending';
            $studentFee->save();

            return true;

        } catch (\Exception $e) {
            Log::error('Stripe refund error: '.$e->getMessage());

            return false;
        }
    }
}
