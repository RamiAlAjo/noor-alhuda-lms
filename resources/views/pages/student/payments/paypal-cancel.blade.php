<?php
/**
 * Page: PayPal Payment Cancelled
 *
 * Purpose: Display message when PayPal payment is cancelled.
 * This page is shown when user cancels PayPal checkout.
 *
 * Route: student.payments.paypal.cancel (GET)
 *
 * Controller: App\Http\Controllers\Student\PaymentController@paypalCancel
 *
 * @package App\Views\Pages\Student\Payments
 */
?>

<x-layouts::app :title="__('Payment Cancelled')">
    <div class="flex min-h-[60vh] items-center justify-center">
        <div class="w-full max-w-md rounded-2xl border border-neutral-200 bg-white p-8 text-center shadow-lg dark:border-neutral-700 dark:bg-neutral-800">
            <!-- Cancelled Icon -->
            <div class="mb-6 flex justify-center">
                <div class="flex h-20 w-20 items-center justify-center rounded-full bg-yellow-100 dark:bg-yellow-900">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-yellow-600 dark:text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
            </div>

            <h1 class="mb-2 text-2xl font-bold text-neutral-900 dark:text-neutral-100">
                {{ __('Payment Cancelled') }}
            </h1>

            <p class="mb-6 text-neutral-600 dark:text-neutral-400">
                {{ __('Your PayPal payment was cancelled. No charges have been made to your account.') }}
            </p>

            <div class="flex flex-col gap-3">
                <flux:button href="{{ route('student.payments.index') }}" variant="primary">
                    {{ __('Try Again') }}
                </flux:button>

                <flux:button href="{{ route('student.dashboard') }}" variant="secondary">
                    {{ __('Return to Dashboard') }}
                </flux:button>
            </div>
        </div>
    </div>
</x-layouts::app>
