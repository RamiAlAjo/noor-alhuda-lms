<?php
/**
 * Page: PayPal Payment Success
 *
 * Purpose: Display confirmation after successful PayPal payment.
 * This page is shown after returning from PayPal checkout.
 *
 * Route: student.payments.paypal.success (GET)
 *
 * Controller: App\Http\Controllers\Student\PaymentController@paypalSuccess
 *
 * @package App\Views\Pages\Student\Payments
 */
?>

<x-layouts::app :title="__('Payment Successful')">
    <div class="flex min-h-[60vh] items-center justify-center">
        <div class="w-full max-w-md rounded-2xl border border-neutral-200 bg-white p-8 text-center shadow-lg dark:border-neutral-700 dark:bg-neutral-800">
            <!-- Success Icon -->
            <div class="mb-6 flex justify-center">
                <div class="flex h-20 w-20 items-center justify-center rounded-full bg-green-100 dark:bg-green-900">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
            </div>

            <h1 class="mb-2 text-2xl font-bold text-neutral-900 dark:text-neutral-100">
                {{ __('Payment Successful!') }}
            </h1>

            <p class="mb-6 text-neutral-600 dark:text-neutral-400">
                {{ __('Your payment has been processed successfully. Thank you for your payment.') }}
            </p>

            @if(isset($payment))
            <div class="mb-6 rounded-lg bg-neutral-50 p-4 dark:bg-neutral-700">
                <div class="grid grid-cols-2 gap-2 text-sm">
                    <span class="text-neutral-500 dark:text-neutral-400">{{ __('Amount') }}:</span>
                    <span class="font-medium text-neutral-900 dark:text-neutral-100">${{ number_format($payment->amount, 2) }}</span>

                    <span class="text-neutral-500 dark:text-neutral-400">{{ __('Transaction ID') }}:</span>
                    <span class="font-mono text-neutral-900 dark:text-neutral-100">{{ $payment->transaction_id ?? '-' }}</span>

                    <span class="text-neutral-500 dark:text-neutral-400">{{ __('Status') }}:</span>
                    <span class="font-medium text-green-600 dark:text-green-400">{{ __($payment->status) }}</span>
                </div>
            </div>
            @endif

            <div class="flex flex-col gap-3">
                <flux:button href="{{ route('student.payments.index') }}" variant="primary">
                    {{ __('View My Payments') }}
                </flux:button>

                <flux:button href="{{ route('student.dashboard') }}" variant="secondary">
                    {{ __('Return to Dashboard') }}
                </flux:button>
            </div>
        </div>
    </div>
</x-layouts::app>
