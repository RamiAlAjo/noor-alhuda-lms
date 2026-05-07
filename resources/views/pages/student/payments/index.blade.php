<?php
/**
 * Page: Student Payments Index
 *
 * Purpose: Display a student's fees and payment history.
 * Shows summary cards for total fees, paid, pending, and overdue.
 * Lists outstanding fees with Pay Now buttons and payment transaction history.
 *
 * Route: student.payments.index (GET)
 * Route: student.payments.store (POST)
 *
 * Controller: App\Http\Controllers\Student\PaymentController@index
 * Controller: App\Http\Controllers\Student\PaymentController@store
 *
 * Components on this page:
 * - x-layouts::app: Main application layout wrapper
 * - Summary cards: Total Fees, Paid, Pending, Overdue
 * - Outstanding fees section with Pay Now button
 * - Payment history table with date, description, amount, status
 * - Payment modal form
 *
 * Required Data variables:
 * - $studentFees: Collection of StudentFee objects
 * - $payments: Collection of Payment objects
 * - $totalFees: Total fee amount
 * - $totalPaid: Total paid amount
 * - $totalPending: Total pending amount
 * - $overdueFees: Collection of overdue fee objects
 *
 * Dependencies:
 * - Helpers: __(), number_format(), route()
 *
 * @package App\Views\Pages\Student\Payments
 */
?>
<x-layouts::app :title="__('Fees & Payments')">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-neutral-900 dark:text-neutral-100">{{ __('Fees & Payments') }}</h1>
        <p class="mt-1 text-neutral-500 dark:text-neutral-400">{{ __('view_your_fees') }}</p>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="mb-4 rounded-lg bg-green-50 p-4 text-green-800 dark:bg-green-900/20 dark:text-green-200">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 rounded-lg bg-red-50 p-4 text-red-800 dark:bg-red-900/20 dark:text-red-200">
            {{ session('error') }}
        </div>
    @endif

    <!-- Summary Cards -->
    <div class="mb-6 grid gap-4 md:grid-cols-4">
        <div class="rounded-xl border border-neutral-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100 text-blue-600 dark:bg-blue-900 dark:text-blue-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('total_fees') }}</p>
                    <p class="text-xl font-bold text-neutral-900 dark:text-neutral-100">${{ number_format($totalFees ?? 0, 2) }}</p>
                </div>
            </div>
        </div>
        <div class="rounded-xl border border-neutral-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-green-100 text-green-600 dark:bg-green-900 dark:text-green-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('paid') }}</p>
                    <p class="text-xl font-bold text-green-600 dark:text-green-400">${{ number_format($totalPaid ?? 0, 2) }}</p>
                </div>
            </div>
        </div>
        <div class="rounded-xl border border-neutral-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-yellow-100 text-yellow-600 dark:bg-yellow-900 dark:text-yellow-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('pending') }}</p>
                    <p class="text-xl font-bold text-yellow-600 dark:text-yellow-400">${{ number_format($totalPending ?? 0, 2) }}</p>
                </div>
            </div>
        </div>
        <div class="rounded-xl border border-neutral-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-red-100 text-red-600 dark:bg-red-900 dark:text-red-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('overdue') }}</p>
                    <p class="text-xl font-bold text-red-600 dark:text-red-400">{{ $overdueFees->count() ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Outstanding Fees with Pay Now -->
    @php
        $outstandingFees = $studentFees->filter(function($fee) {
            return $fee->status !== 'paid';
        });
    @endphp

    @if($outstandingFees->count() > 0)
    <div class="mb-6 rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
        <div class="border-b border-neutral-200 px-6 py-4 dark:border-neutral-700">
            <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">
                {{ __('outstanding_fees') }}
            </h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-neutral-50 text-neutral-500 dark:bg-neutral-700 dark:text-neutral-400">
                    <tr>
                        <th class="px-6 py-3">{{ __('fee_type') }}</th>
                        <th class="px-6 py-3">{{ __('amount') }}</th>
                        <th class="px-6 py-3">{{ __('paid') }}</th>
                        <th class="px-6 py-3">{{ __('due_date') }}</th>
                        <th class="px-6 py-3">{{ __('status') }}</th>
                        <th class="px-6 py-3 text-right">{{ __('actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                    @foreach($outstandingFees as $fee)
                    <tr>
                        <td class="px-6 py-4">
                            <div class="font-medium text-neutral-900 dark:text-neutral-100">
                                {{ $fee->fee->name ?? __('unknown_fee') }}
                            </div>
                            @if($fee->fee?->description)
                            <div class="text-neutral-500 dark:text-neutral-400 text-xs mt-1">
                                {{ $fee->fee?->description }}
                            </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-neutral-900 dark:text-neutral-100">
                            ${{ number_format($fee->amount, 2) }}
                        </td>
                        <td class="px-6 py-4 text-green-600 dark:text-green-400">
                            ${{ number_format($fee->paid_amount, 2) }}
                        </td>
                        <td class="px-6 py-4">
                            @if($fee->due_date)
                                <span class="{{ $fee->due_date->isPast() && $fee->status !== 'paid' ? 'text-red-600 dark:text-red-400' : 'text-neutral-600 dark:text-neutral-400' }}">
                                    {{ $fee->due_date->format('M d, Y') }}
                                </span>
                            @else
                                <span class="text-neutral-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($fee->status === 'partial')
                                <span class="inline-flex items-center rounded-full bg-yellow-100 px-2.5 py-0.5 text-xs font-medium text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                    {{ __('partial') }}
                                </span>
                            @elseif($fee->due_date && $fee->due_date->isPast())
                                <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800 dark:bg-red-900 dark:text-red-200">
                                    {{ __('overdue') }}
                                </span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                    {{ __('Pending') }}
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            @if($fee->amount - $fee->paid_amount > 0)
                            <button type="button"
                                    onclick="openPaymentModal({{ $fee->id }}, '{{ $fee->fee->name ?? __('fee') }}', {{ $fee->amount - $fee->paid_amount }})"
                                    class="inline-flex items-center rounded-lg bg-blue-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:bg-blue-700 dark:hover:bg-blue-800">
                                {{ __('pay_now') }}
                            </button>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @else
    <!-- No Outstanding Fees -->
    <div class="mb-6 rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
        <div class="border-b border-neutral-200 px-6 py-4 dark:border-neutral-700">
            <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">
                {{ __('outstanding_fees') }}
            </h2>
        </div>
        <div class="p-6">
            <div class="flex flex-col items-center justify-center py-4">
                <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-green-100 dark:bg-green-900">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-8 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <p class="text-lg font-medium text-neutral-900 dark:text-neutral-100">{{ __('no_outstanding_fees') }}</p>
                <p class="text-neutral-500 dark:text-neutral-400">{{ __('no_pending_payments') }}</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Payment History -->
    <div class="rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
        <div class="border-b border-neutral-200 px-6 py-4 dark:border-neutral-700">
            <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">
                {{ __('Payment History') }}
            </h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-neutral-50 text-neutral-500 dark:bg-neutral-700 dark:text-neutral-400">
                    <tr>
                        <th class="px-6 py-3">{{ __('date') }}</th>
                        <th class="px-6 py-3">{{ __('description') }}</th>
                        <th class="px-6 py-3">{{ __('amount') }}</th>
                        <th class="px-6 py-3">{{ __('method') }}</th>
                        <th class="px-6 py-3">{{ __('status') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                    @forelse($payments as $payment)
                    <tr>
                        <td class="px-6 py-4 text-neutral-600 dark:text-neutral-400">
                            {{ $payment->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-medium text-neutral-900 dark:text-neutral-100">
                                {{ $payment->fee->name ?? __('Payment') }}
                            </div>
                            @if($payment->transaction_id || $payment->gateway_transaction_id)
                            <div class="text-xs text-neutral-500 dark:text-neutral-400">
                                {{ __('transaction_id') }}: {{ $payment->transaction_id ?? $payment->gateway_transaction_id }}
                            </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 font-medium text-neutral-900 dark:text-neutral-100">
                            ${{ number_format($payment->amount, 2) }}
                        </td>
                        <td class="px-6 py-4 text-neutral-600 dark:text-neutral-400">
                            @switch($payment->payment_method)
                                @case('cash')
                                    {{ __('Cash') }}
                                    @break
                                @case('credit_card')
                                    {{ __('Credit Card') }}
                                    @break
                                @case('bank_transfer')
                                    {{ __('Bank Transfer') }}
                                    @break
                                @case('online')
                                    {{ __('Online') }}
                                    @break
                                @case('stripe')
                                    {{ __('Credit/Debit Card (Stripe)') }}
                                    @break
                                @case('paypal')
                                    {{ __('PayPal') }}
                                    @break
                                @default
                                    {{ $payment->payment_method }}
                            @endswitch
                        </td>
                        <td class="px-6 py-4">
                            @switch($payment->status)
                                @case('pending')
                                    <span class="inline-flex items-center rounded-full bg-yellow-100 px-2.5 py-0.5 text-xs font-medium text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                        {{ __('pending') }}
                                    </span>
                                    @break
                                @case('approved')
                                    <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800 dark:bg-green-900 dark:text-green-200">
                                        {{ __('approved') }}
                                    </span>
                                    @break
                                @case('rejected')
                                    <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800 dark:bg-red-900 dark:text-red-200">
                                        {{ __('rejected') }}
                                    </span>
                                    @break
                                @case('completed')
                                    <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800 dark:bg-green-900 dark:text-green-200">
                                        {{ __('completed') }}
                                    </span>
                                    @break
                                @default
                                    {{ $payment->status }}
                            @endswitch
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-neutral-100 dark:bg-neutral-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="size-8 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <h3 class="text-lg font-medium text-neutral-900 dark:text-neutral-100">{{ __('no_payment_history') }}</h3>
                                <p class="text-neutral-500 dark:text-neutral-400">{{ __('payment_transactions_appear_here') }}</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Payment Modal -->
    <div id="paymentModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex min-h-screen items-center justify-center px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 bg-gray-500/75 transition-opacity" onclick="closePaymentModal()" aria-hidden="true"></div>

            <span class="hidden sm:inline-block sm:h-screen sm:align-middle" aria-hidden="true">&#8203;</span>

            <!-- Modal panel -->
            <div class="relative inline-block transform overflow-hidden rounded-lg bg-white text-left align-bottom shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:align-middle dark:bg-neutral-800">
                <form id="paymentForm" method="POST" action="{{ route('student.payments.store') }}">
                    @csrf
                    <input type="hidden" id="modalStudentFeeId" name="student_fee_id" value="">

                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 dark:bg-neutral-800">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg font-medium leading-6 text-neutral-900 dark:text-neutral-100" id="modal-title">
                                    {{ __('make_a_payment') }}
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-neutral-500 dark:text-neutral-400">
                                        {{ __('paying_for') }}: <span id="modalFeeName" class="font-medium text-neutral-900 dark:text-neutral-100"></span>
                                    </p>
                                    <p class="text-sm text-neutral-500 dark:text-neutral-400 mt-1">
                                        {{ __('remaining_balance') }}: <span id="modalRemainingBalance" class="font-medium text-neutral-900 dark:text-neutral-100"></span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 space-y-4">
                            <!-- Amount -->
                            <div>
                                <label for="amount" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">
                                    {{ __('payment_amount') }} ($)
                                </label>
                                <input type="number"
                                       name="amount"
                                       id="modalAmount"
                                       step="0.01"
                                       min="0.01"
                                       class="mt-1 block w-full rounded-md border-neutral-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-100 sm:text-sm"
                                       required>
                                @error('amount')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Payment Method -->
                            <div>
                                <label for="payment_method" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">
                                    {{ __('payment_method') }}
                                </label>
                                <select name="payment_method"
                                        id="modalPaymentMethod"
                                        class="mt-1 block w-full rounded-md border-neutral-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-100 sm:text-sm"
                                        required>
                                    <option value="">{{ __('select_payment_method') }}</option>

                                    @if(config('services.stripe.enabled'))
                                    <option value="stripe">{{ __('credit_debit_stripe') }}</option>
                                    @endif

                                    @if(config('services.paypal.enabled'))
                                    <option value="paypal">{{ __('PayPal') }}</option>
                                    @endif

                                    <option value="cash">{{ __('cash') }}</option>
                                    <option value="bank_transfer">{{ __('bank_transfer') }}</option>
                                </select>
                                @error('payment_method')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Transaction ID -->
                            <div>
                                <label for="transaction_id" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">
                                    {{ __('transaction_id') }} ({{ __('optional') }})
                                </label>
                                <input type="text"
                                       name="transaction_id"
                                       id="modalTransactionId"
                                       class="mt-1 block w-full rounded-md border-neutral-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-100 sm:text-sm">
                            </div>

                            <!-- Notes -->
                            <div>
                                <label for="notes" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">
                                    {{ __('notes') }} ({{ __('optional') }})
                                </label>
                                <textarea name="notes"
                                          id="modalNotes"
                                          rows="2"
                                          class="mt-1 block w-full rounded-md border-neutral-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-100 sm:text-sm"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="bg-neutral-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 dark:bg-neutral-700">
                        <button type="submit"
                                class="inline-flex w-full justify-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm dark:bg-blue-700 dark:hover:bg-blue-800">
                            {{ __('submit_payment') }}
                        </button>
                        <button type="button"
                                onclick="closePaymentModal()"
                                class="mt-3 inline-flex w-full justify-center rounded-md border border-neutral-300 bg-white px-4 py-2 text-base font-medium text-neutral-700 hover:bg-neutral-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm dark:bg-neutral-600 dark:text-neutral-100 dark:hover:bg-neutral-500">
                            {{ __('cancel') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openPaymentModal(feeId, feeName, remainingBalance) {
            document.getElementById('modalStudentFeeId').value = feeId;
            document.getElementById('modalFeeName').textContent = feeName;
            document.getElementById('modalRemainingBalance').textContent = '$' + remainingBalance.toFixed(2);
            document.getElementById('modalAmount').value = remainingBalance.toFixed(2);
            document.getElementById('modalAmount').max = remainingBalance.toFixed(2);
            document.getElementById('paymentModal').classList.remove('hidden');
        }

        function closePaymentModal() {
            document.getElementById('paymentModal').classList.add('hidden');
            document.getElementById('paymentForm').reset();
        }

        // Close modal on Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closePaymentModal();
            }
        });
    </script>
</x-layouts::app>
