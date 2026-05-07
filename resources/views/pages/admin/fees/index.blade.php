<?php
/**
 * Admin Fees - Index Page
 *
 * Purpose: Manage fee structures including create, edit, and delete fee types
 * Route: admin.fees.index (GET)
 * Controller: App\Http\Controllers\Admin\FeeController@index
 *
 * Components:
 * - x-layouts::app: Main application layout
 * - Stats cards: Fee types count, revenue, pending, overdue
 * - Fee types table: List of all fee types with CRUD actions
 * - Create modal: Dialog form to create new fee type
 * - Edit modals: Per-fee dialog forms to edit fee details
 * - Flux UI components: Buttons, forms
 *
 * Required Data Variables:
 * - $fees: Collection of Fee models
 *
 * Dependencies:
 * - Routes: admin.payments.index, admin.fees.store, admin.fees.update, admin.fees.destroy
 * - Models: Fee
 * - Helpers: __(), route(), number_format()
 */
?>
<x-layouts::app :title="__('Fee Management')">
    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-neutral-900 dark:text-neutral-100">{{ __('Fee Management') }}</h1>
            <p class="mt-1 text-neutral-500 dark:text-neutral-400">{{ __('Manage fee structures and payments') }}</p>
        </div>
        <flux:button :href="route('admin.payments.index')" variant="ghost">
            <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            {{ __('View Payments') }}
        </flux:button>
    </div>

    <!-- Stats Cards -->
    <div class="mb-6 grid gap-4 md:grid-cols-4">
        <div class="rounded-xl border border-neutral-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100 text-blue-600 dark:bg-blue-900 dark:text-blue-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Fee Types') }}</p>
                    <p class="text-xl font-bold text-neutral-900 dark:text-neutral-100">{{ $fees->count() }}</p>
                </div>
            </div>
        </div>
        <div class="rounded-xl border border-neutral-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-green-100 text-green-600 dark:bg-green-900 dark:text-green-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Total Revenue') }}</p>
                    <p class="text-xl font-bold text-neutral-900 dark:text-neutral-100">${{ number_format($stats['total_revenue'] ?? 0, 2) }}</p>
                </div>
            </div>
        </div>
        <div class="rounded-xl border border-neutral-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-amber-100 text-amber-600 dark:bg-amber-900 dark:text-amber-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Pending Payments') }}</p>
                    <p class="text-xl font-bold text-neutral-900 dark:text-neutral-100">{{ $stats['pending_payments'] ?? 0 }}</p>
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
                    <p class="text-xl font-bold text-neutral-900 dark:text-neutral-100">{{ $stats['overdue'] ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Fee Types -->
    <div class="rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
        <div class="border-b border-neutral-200 px-6 py-4 dark:border-neutral-700">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Fee Types') }}</h2>
                <flux:button variant="primary" onclick="document.getElementById('create-fee-modal').showModal()">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    {{ __('Add New Fee') }}
                </flux:button>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-neutral-50 text-neutral-500 dark:bg-neutral-700 dark:text-neutral-400">
                    <tr>
                        <th class="px-6 py-3">{{ __('Name') }}</th>
                        <th class="px-6 py-3">{{ __('Amount') }}</th>
                        <th class="px-6 py-3">{{ __('Type') }}</th>
                        <th class="px-6 py-3">{{ __('Description') }}</th>
                        <th class="px-6 py-3 text-right">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                    @forelse($fees as $fee)
                    <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-700/50">
                        <td class="px-6 py-4 font-medium text-neutral-900 dark:text-neutral-100">{{ $fee->name }}</td>
                        <td class="px-6 py-4 text-neutral-600 dark:text-neutral-400">${{ number_format($fee->amount, 2) }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                @if($fee->fee_type === 'tuition') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                @elseif($fee->fee_type === 'registration') bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200
                                @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200
                                @endif">
                                {{ __($fee->fee_type) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-neutral-500 dark:text-neutral-400">{{ $fee->description ?? '-' }}</td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <flux:button size="sm" variant="ghost" onclick="document.getElementById('edit-fee-modal-{{ $fee->id }}').showModal()">
                                    {{ __('Edit') }}
                                </flux:button>
                                <form method="POST" action="{{ route('admin.fees.destroy', $fee) }}">
                                    @csrf
                                    @method('DELETE')
                                    <flux:button type="submit" size="sm" variant="danger">{{ __('Delete') }}</flux:button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-neutral-100 dark:bg-neutral-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="size-8 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                </div>
                                <h3 class="text-lg font-medium text-neutral-900 dark:text-neutral-100">{{ __('No fees defined') }}</h3>
                                <p class="mt-1 text-neutral-500 dark:text-neutral-400">{{ __('Create your first fee type to get started') }}</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Create Fee Modal -->
    <dialog id="create-fee-modal" class="modal rounded-2xl p-6 bg-white dark:bg-zinc-800 w-full max-w-md backdrop:bg-black/50">
        <div class="mb-6">
            <h3 class="text-xl font-bold text-neutral-900 dark:text-neutral-100">{{ __('Add New Fee') }}</h3>
            <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Create a new fee type') }}</p>
        </div>
        <form method="POST" action="{{ route('admin.fees.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">{{ __('Fee Name') }}</label>
                <input type="text" name="name" class="w-full rounded-lg border-neutral-300 dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-100 px-3 py-2 focus:ring-2 focus:ring-indigo-500" placeholder="e.g., Tuition Fee" required />
            </div>
            <div>
                <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">{{ __('Fee Name (Arabic)') }}</label>
                <input type="text" name="name_ar" class="w-full rounded-lg border-neutral-300 dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-100 px-3 py-2" placeholder="e.g., رسوم tuition" />
            </div>
            <div>
                <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">{{ __('Amount') }}</label>
                <input type="number" name="amount" step="0.01" class="w-full rounded-lg border-neutral-300 dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-100 px-3 py-2" placeholder="0.00" required />
            </div>
            <div>
                <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">{{ __('Fee Type') }}</label>
                <select name="fee_type" class="w-full rounded-lg border-neutral-300 dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-100 px-3 py-2" required>
                    <option value="tuition">{{ __('Tuition') }}</option>
                    <option value="registration">{{ __('Registration') }}</option>
                    <option value="library">{{ __('Library') }}</option>
                    <option value="lab">{{ __('Lab') }}</option>
                    <option value="other">{{ __('Other') }}</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">{{ __('Academic Year') }}</label>
                <input type="text" name="academic_year" class="w-full rounded-lg border-neutral-300 dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-100 px-3 py-2" placeholder="e.g., 2024-2025" />
            </div>
            <div>
                <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">{{ __('Due Date') }}</label>
                <input type="date" name="due_date" class="w-full rounded-lg border-neutral-300 dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-100 px-3 py-2" required />
            </div>
            <div>
                <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">{{ __('Description') }}</label>
                <textarea name="description" rows="3" class="w-full rounded-lg border-neutral-300 dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-100 px-3 py-2" placeholder="Optional description"></textarea>
            </div>
            <div class="mt-6 flex justify-end gap-3">
                <flux:button type="button" variant="ghost" onclick="document.getElementById('create-fee-modal').close()">
                    {{ __('Cancel') }}
                </flux:button>
                <flux:button type="submit" variant="primary">
                    {{ __('Create Fee') }}
                </flux:button>
            </div>
        </form>
    </dialog>

    <!-- Edit Fee Modals -->
    @foreach($fees as $fee)
    <dialog id="edit-fee-modal-{{ $fee->id }}" class="modal rounded-2xl p-6 bg-white dark:bg-zinc-800 w-full max-w-md backdrop:bg-black/50">
        <div class="mb-6">
            <h3 class="text-xl font-bold text-neutral-900 dark:text-neutral-100">{{ __('Edit Fee') }}</h3>
            <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Update fee details') }}</p>
        </div>
        <form method="POST" action="{{ route('admin.fees.update', $fee) }}" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">{{ __('Fee Name') }}</label>
                <input type="text" name="name" value="{{ $fee->name }}" class="w-full rounded-lg border-neutral-300 dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-100 px-3 py-2" required />
            </div>
            <div>
                <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">{{ __('Fee Name (Arabic)') }}</label>
                <input type="text" name="name_ar" value="{{ $fee->name_ar }}" class="w-full rounded-lg border-neutral-300 dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-100 px-3 py-2" />
            </div>
            <div>
                <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">{{ __('Amount') }}</label>
                <input type="number" name="amount" value="{{ $fee->amount }}" step="0.01" class="w-full rounded-lg border-neutral-300 dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-100 px-3 py-2" required />
            </div>
            <div>
                <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">{{ __('Fee Type') }}</label>
                <select name="fee_type" class="w-full rounded-lg border-neutral-300 dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-100 px-3 py-2" required>
                    <option value="tuition" {{ $fee->fee_type == 'tuition' ? 'selected' : '' }}>{{ __('Tuition') }}</option>
                    <option value="registration" {{ $fee->fee_type == 'registration' ? 'selected' : '' }}>{{ __('Registration') }}</option>
                    <option value="library" {{ $fee->fee_type == 'library' ? 'selected' : '' }}>{{ __('Library') }}</option>
                    <option value="lab" {{ $fee->fee_type == 'lab' ? 'selected' : '' }}>{{ __('Lab') }}</option>
                    <option value="other" {{ $fee->fee_type == 'other' ? 'selected' : '' }}>{{ __('Other') }}</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">{{ __('Academic Year') }}</label>
                <input type="text" name="academic_year" value="{{ $fee->academic_year }}" class="w-full rounded-lg border-neutral-300 dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-100 px-3 py-2" placeholder="e.g., 2024-2025" />
            </div>
            <div>
                <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">{{ __('Due Date') }}</label>
                <input type="date" name="due_date" value="{{ $fee->due_date ? $fee->due_date->format('Y-m-d') : '' }}" class="w-full rounded-lg border-neutral-300 dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-100 px-3 py-2" required />
            </div>
            <div>
                <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">{{ __('Description') }}</label>
                <textarea name="description" rows="3" class="w-full rounded-lg border-neutral-300 dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-100 px-3 py-2">{{ $fee->description }}</textarea>
            </div>
            <div class="mt-6 flex justify-end gap-3">
                <flux:button type="button" variant="ghost" onclick="document.getElementById('edit-fee-modal-{{ $fee->id }}').close()">
                    {{ __('Cancel') }}
                </flux:button>
                <flux:button type="submit" variant="primary">
                    {{ __('Update Fee') }}
                </flux:button>
            </div>
        </form>
    </dialog>
    @endforeach
</x-layouts::app>
