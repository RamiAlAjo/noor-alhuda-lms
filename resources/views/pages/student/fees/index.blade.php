<?php
/**
 * Page: Student Fees Index
 *
 * Purpose: Display a student's fee assignments with payment summary.
 * Shows total fees, paid amount, pending amount, and overdue fees count.
 * Lists all fee assignments with their status and payment details.
 *
 * Route: student.fees.index (GET)
 *
 * Controller: App\Http\Controllers\Student\FeeController@index
 *
 * Components on this page:
 * - x-layouts::app: Main application layout wrapper
 * - Summary cards: Total Fees, Paid, Pending, Overdue
 * - Fee assignments table with fee name, type, amount, paid amount, due date, status
 * - Empty state when no fees assigned
 *
 * Required Data variables:
 * - $totalFees: Total fee amount
 * - $totalPaid: Total paid amount
 * - $totalPending: Total pending amount
 * - $overdueFees: Collection of overdue fee objects
 * - $studentFees: Collection of StudentFee objects
 *
 * Dependencies:
 * - Helpers: __(), number_format(), ucfirst()
 * - Relationships: StudentFee->fee, StudentFee->status, StudentFee->amount, StudentFee->paid_amount, StudentFee->due_date
 *
 * @package App\Views\Pages\Student\Fees
 */
?>
<x-layouts::app :title="__('My Fees')">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-neutral-900 dark:text-neutral-100">{{ __('My Fees') }}</h1>
        <p class="mt-1 text-neutral-500 dark:text-neutral-400">{{ __('View your fee assignments and payment status') }}</p>
    </div>

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
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Total Fees') }}</p>
                    <p class="text-xl font-bold text-neutral-900 dark:text-neutral-100">${{ number_format($totalFees, 2) }}</p>
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
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Paid') }}</p>
                    <p class="text-xl font-bold text-green-600 dark:text-green-400">${{ number_format($totalPaid, 2) }}</p>
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
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Pending') }}</p>
                    <p class="text-xl font-bold text-yellow-600 dark:text-yellow-400">${{ number_format($totalPending, 2) }}</p>
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
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Overdue') }}</p>
                    <p class="text-xl font-bold text-red-600 dark:text-red-400">{{ $overdueFees->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Fee List -->
    <div class="rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
        <div class="border-b border-neutral-200 px-6 py-4 dark:border-neutral-700">
            <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">
                {{ __('Fee Assignments') }}
            </h2>
        </div>
        <div class="overflow-x-auto">
            @if($studentFees->count() > 0)
                <table class="w-full text-left text-sm">
                    <thead class="bg-neutral-50 text-neutral-500 dark:bg-neutral-700 dark:text-neutral-400">
                        <tr>
                            <th class="px-6 py-3">{{ __('Fee Name') }}</th>
                            <th class="px-6 py-3">{{ __('Type') }}</th>
                            <th class="px-6 py-3">{{ __('Amount') }}</th>
                            <th class="px-6 py-3">{{ __('Paid') }}</th>
                            <th class="px-6 py-3">{{ __('Due Date') }}</th>
                            <th class="px-6 py-3">{{ __('Status') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                        @foreach($studentFees as $studentFee)
                            <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-700/50">
                                <td class="px-6 py-4 font-medium text-neutral-900 dark:text-neutral-100">
                                    {{ $studentFee->fee->name ?? __('N/A') }}
                                </td>
                                <td class="px-6 py-4 text-neutral-600 dark:text-neutral-300">
                                    @if($studentFee->fee)
                                        {{ ucfirst($studentFee->fee->fee_type) }}
                                    @else
                                        {{ __('N/A') }}
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-neutral-900 dark:text-neutral-100">
                                    ${{ number_format($studentFee->amount, 2) }}
                                </td>
                                <td class="px-6 py-4 text-green-600 dark:text-green-400">
                                    ${{ number_format($studentFee->paid_amount, 2) }}
                                </td>
                                <td class="px-6 py-4 text-neutral-600 dark:text-neutral-300">
                                    {{ $studentFee->due_date?->format('M d, Y') ?? '-' }}
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $statusColor = match($studentFee->status) {
                                            'paid' => 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300',
                                            'partial' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300',
                                            'pending' => 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300',
                                            'overdue' => 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300',
                                            default => 'bg-neutral-100 text-neutral-700 dark:bg-neutral-700 dark:text-neutral-300'
                                        };
                                    @endphp
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $statusColor }}">
                                        {{ ucfirst($studentFee->status) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="p-12 text-center">
                    <div class="flex flex-col items-center">
                        <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-green-100 dark:bg-green-900">
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-8 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-neutral-900 dark:text-neutral-100">{{ __('No fees assigned') }}</h3>
                        <p class="text-neutral-500 dark:text-neutral-400">{{ __('You have no fee assignments at this time') }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-layouts::app>
