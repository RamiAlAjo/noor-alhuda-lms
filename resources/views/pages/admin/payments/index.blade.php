<?php
/**
 * Page: Payment Management Index
 *
 * Purpose: Display a list of all student payments with filtering and approval capabilities.
 * Allows admins to search payments, filter by status, and approve/reject pending payments.
 *
 * Route: admin.payments.index (GET)
 *
 * Controller: App\Http\Controllers\Admin\PaymentController@index
 *
 * Components on this page:
 * - x-layouts::app: Main application layout wrapper
 * - Filter form with search, status dropdown
 * - Data table displaying payments with student info, fee type, amount, method, status
 * - Action buttons for approving/rejecting pending payments
 * - Pagination links
 *
 * Required Data variables:
 * - $payments: Collection of Payment objects (paginated)
 *
 * Dependencies:
 * - Routes: admin.payments.approve, admin.payments.reject
 * - Helpers: __(), route(), request(), number_format()
 * - Relationships: Payment->student (User), Payment->fee (Fee)
 * - Flux UI components: flux:input, flux:select, flux:button
 *
 * @package App\Views\Pages\Admin\Payments
 */
?>
<x-layouts::app :title="__('Payment Management')">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-neutral-900 dark:text-neutral-100">{{ __('Payment Management') }}</h1>
        <p class="mt-1 text-neutral-500 dark:text-neutral-400">{{ __('Track and manage student payments') }}</p>
    </div>

    <!-- Filters -->
    <div class="mb-6 rounded-xl border border-neutral-200 bg-white p-4 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
        <form method="GET" class="flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-[200px]">
                <flux:input name="search" :label="__('Search')" :value="request('search')" placeholder="{{ __('Student name or ID') }}" />
            </div>
            <div class="w-40">
                <flux:select name="status" :label="__('Status')">
                    <flux:select.option value="">{{ __('All') }}</flux:select.option>
                    <flux:select.option value="pending" :selected="request('status') === 'pending'">{{ __('Pending') }}</flux:select.option>
                    <flux:select.option value="approved" :selected="request('status') === 'approved'">{{ __('Approved') }}</flux:select.option>
                    <flux:select.option value="rejected" :selected="request('status') === 'rejected'">{{ __('Rejected') }}</flux:select.option>
                </flux:select>
            </div>
            <flux:button type="submit" variant="primary">{{ __('Filter') }}</flux:button>
        </form>
    </div>

    <!-- Payments Table -->
    <div class="rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-neutral-50 text-neutral-500 dark:bg-neutral-700 dark:text-neutral-400">
                    <tr>
                        <th class="px-6 py-3">{{ __('Student') }}</th>
                        <th class="px-6 py-3">{{ __('Fee Type') }}</th>
                        <th class="px-6 py-3">{{ __('Amount') }}</th>
                        <th class="px-6 py-3">{{ __('Payment Method') }}</th>
                        <th class="px-6 py-3">{{ __('Status') }}</th>
                        <th class="px-6 py-3">{{ __('Date') }}</th>
                        <th class="px-6 py-3 text-right">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                    @forelse($payments as $payment)
                    <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-700/50">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 text-sm font-bold text-white">
                                    {{ substr($payment->student?->first_name ?? 'S', 0, 1) }}{{ substr($payment->student?->last_name ?? '', 0, 1) }}
                                </div>
                                <div>
                                    <p class="font-medium text-neutral-900 dark:text-neutral-100">{{ $payment->student?->full_name ?? __('Unknown') }}</p>
                                    <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ $payment->student?->user_id ?? '-' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-neutral-600 dark:text-neutral-400">{{ $payment->fee->name ?? '-' }}</td>
                        <td class="px-6 py-4 font-medium text-neutral-900 dark:text-neutral-100">${{ number_format($payment->amount, 2) }}</td>
                        <td class="px-6 py-4 text-neutral-600 dark:text-neutral-400">{{ __($payment->payment_method) }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                @if($payment->status === 'approved') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                @elseif($payment->status === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                @endif">
                                {{ __($payment->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-neutral-500 dark:text-neutral-400">{{ $payment->created_at->format('Y-m-d') }}</td>
                        <td class="px-6 py-4 text-right">
                            @if($payment->status === 'pending')
                            <div class="flex items-center justify-end gap-2">
                                <form method="POST" action="{{ route('admin.payments.approve', $payment) }}">
                                    @csrf
                                    <flux:button type="submit" size="sm" variant="primary">{{ __('Approve') }}</flux:button>
                                </form>
                                <form method="POST" action="{{ route('admin.payments.reject', $payment) }}">
                                    @csrf
                                    <flux:button type="submit" size="sm" variant="danger">{{ __('Reject') }}</flux:button>
                                </form>
                            </div>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-neutral-100 dark:bg-neutral-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="size-8 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                </div>
                                <h3 class="text-lg font-medium text-neutral-900 dark:text-neutral-100">{{ __('No payments found') }}</h3>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($payments->hasPages())
        <div class="border-t border-neutral-200 px-6 py-4 dark:border-neutral-700">
            {{ $payments->links() }}
        </div>
        @endif
    </div>
</x-layouts::app>
