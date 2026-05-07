<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\StudentFee;
use App\Services\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class PaymentController extends Controller
{
    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Display student's payments and fees.
     */
    public function index(): View
    {
        $student = auth()->user();

        $studentFees = StudentFee::with('fee')
            ->where('student_id', $student->id)
            ->orderBy('due_date', 'asc')
            ->get();

        $payments = Payment::with(['studentFee.fee'])
            ->where('student_id', $student->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $totalFees = $studentFees->sum('amount');
        $totalPaid = $studentFees->sum('paid_amount');
        $totalPending = $totalFees - $totalPaid;
        $overdueFees = $studentFees->filter(function ($fee) {
            return $fee->status !== 'paid' && $fee->due_date && $fee->due_date->isPast();
        });

        // Get available payment gateways
        $gateways = PaymentService::getAvailableGateways();

        return view('pages.student.payments.index', compact(
            'studentFees',
            'payments',
            'totalFees',
            'totalPaid',
            'totalPending',
            'overdueFees',
            'gateways'
        ));
    }

    /**
     * Display student's fees.
     */
    public function fees(): View
    {
        $student = auth()->user();

        $studentFees = StudentFee::with('fee')
            ->where('student_id', $student->id)
            ->orderBy('due_date', 'asc')
            ->get();

        $totalFees = $studentFees->sum('amount');
        $totalPaid = $studentFees->sum('paid_amount');
        $totalPending = $totalFees - $totalPaid;
        $overdueFees = $studentFees->filter(function ($fee) {
            return $fee->status !== 'paid' && $fee->due_date && $fee->due_date->isPast();
        });

        return view('pages.student.fees.index', compact(
            'studentFees',
            'totalFees',
            'totalPaid',
            'totalPending',
            'overdueFees'
        ));
    }

    /**
     * Store a new payment for a student fee.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'student_fee_id' => 'required|exists:student_fees,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:stripe,paypal,cash,bank_transfer',
            'gateway_data' => 'nullable|array',
        ]);

        $student = auth()->user();
        $studentFee = StudentFee::where('id', $validated['student_fee_id'])
            ->where('student_id', $student->id)
            ->firstOrFail();

        // Verify the amount doesn't exceed the remaining balance
        $remainingBalance = $studentFee->amount - $studentFee->paid_amount;
        if ($validated['amount'] > $remainingBalance) {
            return back()->with('error', __('payment_amount_exceeds').' $'.number_format($remainingBalance, 2));
        }

        try {
            // Process payment based on gateway
            $gatewayData = $validated['gateway_data'] ?? [];

            $payment = $this->paymentService->processPayment(
                $studentFee,
                $validated['amount'],
                $validated['payment_method'],
                $gatewayData
            );

            // For Stripe, redirect to Stripe checkout page
            if ($validated['payment_method'] === 'stripe' && $payment->status === 'pending') {
                return redirect()->route('student.payments.stripe.checkout', ['payment' => $payment->id]);
            }

            // For PayPal, redirect to PayPal
            if ($validated['payment_method'] === 'paypal') {
                return redirect()->route('student.payments.paypal.checkout', ['payment' => $payment->id]);
            }

            // For manual payments, return success message
            return redirect()->route('student.payments.index')
                ->with('success', __('payment_submitted_success'));

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Process Stripe checkout.
     */
    public function stripeCheckout(Payment $payment): View
    {
        // Ensure the payment belongs to the current student
        if ($payment->student_id !== auth()->id()) {
            abort(403);
        }

        $studentFee = $payment->studentFee->load('fee');

        return view('pages.student.payments.stripe', compact('payment', 'studentFee'));
    }

    /**
     * Process PayPal checkout - redirects to PayPal.
     */
    public function paypalCheckout(Payment $payment): RedirectResponse
    {
        // Ensure the payment belongs to the current student
        if ($payment->student_id !== auth()->id()) {
            abort(403);
        }

        $provider = new \Srmklive\PayPal\Services\PayPal;
        $provider->setApiCredentials(config('services.paypal'));
        $provider->getAccessToken();

        $studentFee = $payment->studentFee->load('fee');

        $orderData = [
            'intent' => 'CAPTURE',
            'purchase_units' => [
                [
                    'reference_id' => $payment->id,
                    'description' => $studentFee->fee->name ?? 'Fee Payment',
                    'amount' => [
                        'currency_code' => config('services.paypal.currency', 'USD'),
                        'value' => number_format($payment->amount, 2, '.', ''),
                    ],
                ],
            ],
            'application_context' => [
                'return_url' => route('student.payments.paypal.success', ['payment' => $payment->id]),
                'cancel_url' => route('student.payments.paypal.cancel', ['payment' => $payment->id]),
            ],
        ];

        $order = $provider->createOrder($orderData);

        // Find PayPal approval URL
        foreach ($order['links'] as $link) {
            if ($link['rel'] === 'approve') {
                return redirect($link['href']);
            }
        }

        return redirect()->route('student.payments.index')
            ->with('error', 'Unable to connect to PayPal');
    }

    /**
     * PayPal success callback.
     */
    public function paypalSuccess(Request $request, Payment $payment): View|RedirectResponse
    {
        // Ensure the payment belongs to the current student
        if ($payment->student_id !== auth()->id()) {
            abort(403);
        }

        $provider = new \Srmklive\PayPal\Services\PayPal;
        $provider->setApiCredentials(config('services.paypal'));
        $provider->getAccessToken();

        // Capture the payment
        $orderId = $request->query('token');

        try {
            $result = $provider->capturePaymentOrder($orderId);

            if ($result['status'] === 'COMPLETED') {
                // Update payment status
                $transactionId = $result['purchase_units'][0]['payments']['captures'][0]['id'] ?? null;

                $payment->update([
                    'status' => 'completed',
                    'gateway_transaction_id' => $transactionId,
                ]);

                // Update student fee
                $studentFee = $payment->studentFee;
                $studentFee->paid_amount += $payment->amount;

                if ($studentFee->paid_amount >= $studentFee->amount) {
                    $studentFee->status = 'paid';
                } elseif ($studentFee->paid_amount > 0) {
                    $studentFee->status = 'partial';
                }

                $studentFee->save();

                return view('pages.student.payments.paypal-success', compact('payment'));
            }
        } catch (\Exception $e) {
            Log::error('PayPal capture error: '.$e->getMessage());
        }

        return redirect()->route('student.payments.index')
            ->with('error', 'Payment processing failed');
    }

    /**
     * PayPal cancel callback.
     */
    public function paypalCancel(Payment $payment): View
    {
        // Ensure the payment belongs to the current student
        if ($payment->student_id !== auth()->id()) {
            abort(403);
        }

        return view('pages.student.payments.paypal-cancel');
    }

    /**
     * Handle Stripe webhook.
     */
    public function stripeWebhook(Request $request): \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
    {
        $payload = $request->all();

        // Verify webhook signature in production
        $sigHeader = $request->header('stripe-signature');

        try {
            $this->paymentService->handleStripeWebhook($payload);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Stripe webhook error: '.$e->getMessage());

            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
