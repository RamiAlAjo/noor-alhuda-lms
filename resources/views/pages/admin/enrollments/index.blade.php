{{--
    =============================================================================
    ADMIN ENROLLMENTS INDEX VIEW
    =============================================================================

    Purpose: List all student enrollments with filtering and stats.

    Route: admin.enrollments.index
    Controller: Admin\EnrollmentController@index

    Components:
    - Header with "Pending Requests" button (shows count badge)
    - Stats cards: Total, Approved, Pending, Rejected
    - Filter form with Status dropdown
    - Enrollments table with ID, Student ID, Offering ID, Status, Date
    - Pagination
    - Empty state

    Required Data:
    - $enrollments: Paginated collection of Enrollment models
    - $stats: Array with enrollment statistics

    Dependencies:
    - route('admin.enrollments.requests') - Pending requests page
    - request('status') - Get status filter from URL

    =============================================================================
--}}
<x-layouts::app :title="__('Enrollment Management')">
    <x-page-header
        :title="__('Enrollment Management')"
        :description="__('Manage student course enrollments')"
    >
        <flux:button :href="route('admin.enrollments.requests')" variant="primary" class="shadow-sm">
            {{ __('Pending Requests') }}
            @if(isset($stats['pending']) && $stats['pending'] > 0)
                <span class="ml-2 rounded-full bg-red-500 px-2 py-0.5 text-xs text-white">{{ $stats['pending'] }}</span>
            @endif
        </flux:button>
    </x-page-header>

    <!-- Stats Cards -->
    <div class="mb-8 grid grid-cols-2 gap-4 md:grid-cols-4">
        <x-stat-card
            icon="users"
            :label="__('Total')"
            :value="$stats['total'] ?? 0"
            color="indigo"
        />
        <x-stat-card
            icon="check-circle"
            :label="__('Approved')"
            :value="$stats['approved'] ?? 0"
            color="green"
        />
        <x-stat-card
            icon="clock"
            :label="__('Pending')"
            :value="$stats['pending'] ?? 0"
            color="amber"
        />
        <x-stat-card
            icon="x-mark"
            :label="__('Rejected')"
            :value="$stats['rejected'] ?? 0"
            color="red"
        />
    </div>

    <!-- Filters -->
    <div class="mb-6 rounded-xl border border-neutral-200 bg-white p-4 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
        <form method="GET" class="flex flex-wrap items-end gap-4">
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

    <!-- Enrollments Table -->
    <div class="rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
        <table class="w-full text-left text-sm">
            <thead class="bg-neutral-50 text-neutral-500 dark:bg-neutral-700 dark:text-neutral-400">
                <tr>
                    <th class="px-6 py-3">{{ __('ID') }}</th>
                    <th class="px-6 py-3">{{ __('Student') }}</th>
                    <th class="px-6 py-3">{{ __('Course') }}</th>
                    <th class="px-6 py-3">{{ __('Status') }}</th>
                    <th class="px-6 py-3">{{ __('Date') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                @forelse($enrollments as $enrollment)
                <tr>
                    <td class="px-6 py-4">{{ $enrollment->id }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-indigo-100 text-xs font-medium text-indigo-600 dark:bg-indigo-900 dark:text-indigo-300">
                                {{ substr($enrollment->student?->first_name ?? 'S', 0, 1) }}{{ substr($enrollment->student?->last_name ?? '', 0, 1) }}
                            </div>
                            <div>
                                <p class="font-medium text-neutral-900 dark:text-neutral-100">{{ $enrollment->student?->full_name ?? __('Unknown') }}</p>
                                <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ $enrollment->student?->email ?? '' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-neutral-900 dark:text-neutral-100">{{ $enrollment->offering?->course?->name ?? __('Unknown') }}</p>
                        <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ $enrollment->offering?->course?->code ?? '' }} - {{ $enrollment->offering?->semester?->name ?? '' }}</p>
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                            @if($enrollment->status === 'approved') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                            @elseif($enrollment->status === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                            @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                            @endif">
                            {{ __($enrollment->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-neutral-500 dark:text-neutral-400">
                        {{ $enrollment->created_at->format('M d, Y') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center">{{ __('No enrollments found') }}</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @if($enrollments->hasPages())
        <div class="border-t border-neutral-200 px-6 py-4 dark:border-neutral-700">
            {{ $enrollments->links() }}
        </div>
        @endif
    </div>
</x-layouts::app>
