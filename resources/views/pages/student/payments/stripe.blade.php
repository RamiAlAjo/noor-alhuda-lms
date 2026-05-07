<?php
/**
 * Page: Stripe Checkout
 *
 * Purpose: Process payment via Stripe
 *
 * Route: student.payments.stripe.checkout (GET)
 *
 * Controller: App\Http\Controllers\Student\PaymentController@stripeCheckout
 */
?>
<x-layouts::app :title="__('Stripe Payment')">
    <div class="max-w-2xl mx-auto py-12">
        <div class="bg-white rounded-xl border border-neutral-200 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <!-- Header -->
            <div class="border-b border-neutral-200 px-6 py-4 dark:border-neutral-700">
                <h2 class="text-xl font-semibold text-neutral-900 dark:text-neutral-100">
                    {{ __('Pay with Stripe') }}
                </h2>
            </div>

            <!-- Payment Details -->
            <div class="p-6">
                <div class="mb-6">
                    <h3 class="text-lg font-medium text-neutral-900 dark:text-neutral-100">
                        {{ __('Payment Summary') }}
                    </h3>
                    <dl class="mt-4 space-y-3">
                        <div class="flex justify-between">
                            <dt class="text-neutral-500 dark:text-neutral-400">{{ __('Fee') }}</dt>
                            <dd class="font-medium text-neutral-900 dark:text-neutral-100">
                                {{ $studentFee->fee->name ?? __('Fee Payment') }}
                            </dd>
                        </div>
                        <div class="flex justify-between border-t border-neutral-200 pt-3 dark:border-neutral-700">
                            <dt class="text-lg font-medium text-neutral-900 dark:text-neutral-100">{{ __('Total') }}</dt>
                            <dd class="text-lg font-bold text-blue-600 dark:text-blue-400">
                                ${{ number_format($payment->amount, 2) }}
                            </dd>
                        </div>
                    </dl>
                </div>

                <!-- Stripe Elements Container -->
                <div id="payment-element" class="mb-6">
                    <!-- Stripe.js will mount the payment form here -->
                </div>

                <!-- Error Messages -->
                <div id="error-message" class="hidden mb-4 rounded-lg bg-red-50 p-4 text-red-800 dark:bg-red-900/20 dark:text-red-200">
                </div>

                <!-- Pay Button -->
                <button id="submit"
                        class="w-full rounded-lg bg-blue-600 px-4 py-3 text-center text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:bg-blue-700 dark:hover:bg-blue-800 disabled:opacity-50 disabled:cursor-not-allowed">
                    <span id="button-text">{{ __('Pay Now') }}</span>
                    <span id="spinner" class="hidden">{{ __('Processing...') }}</span>
                </button>
            </div>
        </div>

        <!-- Back Link -->
        <div class="mt-4 text-center">
            <a href="{{ route('student.payments.index') }}" class="text-sm text-blue-600 hover:underline dark:text-blue-400">
                {{ __('← Back to Payments') }}
            </a>
        </div>
    </div>

    <!-- Stripe.js -->
    <script src="https://js.stripe.com/v3/"></script>

    <script>
        const stripe = Stripe('{{ config('services.stripe.key') }}');

        // The server-created PaymentIntent client_secret
        const clientSecret = '{{ $payment->gateway_transaction_id }}';

        let elements;

        async function initialize() {
            const appearance = {
                theme: '{{ session('theme', 'light') }}',
                variables: {
                    colorPrimary: '#2563eb',
                },
            };

            elements = stripe.elements({
                clientSecret,
                appearance
            });

            const paymentElement = elements.create('payment');
            paymentElement.mount('#payment-element');
        }

        async function handleSubmit(e) {
            e.preventDefault();

            const submitButton = document.getElementById('submit');
            const spinner = document.getElementById('spinner');
            const buttonText = document.getElementById('button-text');
            const errorMessage = document.getElementById('error-message');

            submitButton.disabled = true;
            spinner.classList.remove('hidden');
            buttonText.classList.add('hidden');
            errorMessage.classList.add('hidden');

            const { error } = await stripe.confirmPayment({
                elements,
                confirmParams: {
                    return_url: '{{ route('student.payments.success') }}',
                },
            });

            if (error) {
                errorMessage.textContent = error.message;
                errorMessage.classList.remove('hidden');
                submitButton.disabled = false;
                spinner.classList.add('hidden');
                buttonText.classList.remove('hidden');
            }
        }

        document.getElementById('submit').addEventListener('click', handleSubmit);

        // Initialize on page load
        initialize().catch(console.error);
    </script>
</x-layouts::app>
