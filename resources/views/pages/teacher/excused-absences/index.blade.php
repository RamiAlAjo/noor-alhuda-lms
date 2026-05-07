<?php
/**
 * Page: Teacher Excused Absences Index
 *
 * Purpose: Display a list of excused absence requests for teacher's courses.
 * Allows teachers to filter by status and view details of each request.
 *
 * Route: teacher.excused-absences.index (GET)
 *
 * Controller: App\Http\Controllers\Teacher\ExcusedAbsenceController@index
 *
 * Components on this page:
 * - x-layouts::app: Main application layout wrapper
 * - Filter form with status dropdown
 * - Data table displaying absence requests with student, course, date, type, status
 * - Pagination links
 *
 * Required Data variables:
 * - $absences: Collection of ExcusedAbsence objects (paginated)
 *
 * Dependencies:
 * - Routes: teacher.excused-absences.show
 * - Helpers: __(), route(), request()
 * - Relationships: ExcusedAbsence->student (User), ExcusedAbsence->courseOffering->course
 * - Methods: ExcusedAbsence->isPending()
 *
 * @package App\Views\Pages\Teacher\ExcusedAbsences
 */

use function Laravel\Folio\name;

$statuses = [
    '' => __('All Statuses'),
    'pending' => __('Pending'),
    'approved' => __('Approved'),
    'rejected' => __('Rejected'),
];

$absenceTypeLabels = [
    'single_day' => __('Single Day'),
    'multiple_days' => __('Multiple Days'),
    'late_arrival' => __('Late Arrival'),
    'early_departure' => __('Early Departure'),
];

?>

<x-layouts::app :title="__('Excused Absences')">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-neutral-900 dark:text-neutral-100">{{ __('Excused Absences') }}</h1>
        <p class="mt-1 text-neutral-500 dark:text-neutral-400">{{ __('Review student excuse requests') }}</p>
    </div>

    <!-- Filters -->
    <div class="mb-6 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
        <h3 class="mb-4 text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Filter Requests') }}</h3>

        <form method="GET" action="{{ route('teacher.excused-absences.index') }}" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">{{ __('Status') }}</label>
                <select name="status" class="w-full rounded-lg border-neutral-300 dark:border-neutral-600 dark:bg-neutral-800">
                    @foreach($statuses as $key => $label)
                        <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    {{ __('Apply Filters') }}
                </button>
            </div>
        </form>
    </div>

    <!-- Excused Absences List -->
    @if($absences->isEmpty())
        <div class="text-center py-12 rounded-xl border border-neutral-200 bg-white dark:border-neutral-700 dark:bg-neutral-800">
            <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
            <h3 class="mt-4 text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('No excused absence requests') }}</h3>
            <p class="mt-2 text-neutral-500 dark:text-neutral-400">{{ __('No student has submitted excused absence requests for your courses yet.') }}</p>
        </div>
    @else
        <div class="rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-neutral-200 dark:border-neutral-700">
                            <th class="px-4 py-3 text-start text-sm font-medium text-neutral-500 dark:text-neutral-400">{{ __('Student') }}</th>
                            <th class="px-4 py-3 text-start text-sm font-medium text-neutral-500 dark:text-neutral-400">{{ __('Course') }}</th>
                            <th class="px-4 py-3 text-start text-sm font-medium text-neutral-500 dark:text-neutral-400">{{ __('Date') }}</th>
                            <th class="px-4 py-3 text-start text-sm font-medium text-neutral-500 dark:text-neutral-400">{{ __('Type') }}</th>
                            <th class="px-4 py-3 text-start text-sm font-medium text-neutral-500 dark:text-neutral-400">{{ __('Status') }}</th>
                            <th class="px-4 py-3 text-start text-sm font-medium text-neutral-500 dark:text-neutral-400">{{ __('Submitted') }}</th>
                            <th class="px-4 py-3 text-end text-sm font-medium text-neutral-500 dark:text-neutral-400">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($absences as $absence)
                            <tr class="border-b border-neutral-100 dark:border-neutral-800 hover:bg-neutral-50 dark:hover:bg-neutral-700/50">
                                <td class="px-4 py-3">
                                    <span class="font-medium text-neutral-900 dark:text-neutral-100">
                                        {{ $absence->student?->name ?? __('Unknown Student') }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-neutral-600 dark:text-neutral-400">
                                    {{ $absence->courseOffering?->course?->name ?? __('Unknown Course') }}
                                </td>
                                <td class="px-4 py-3 text-neutral-600 dark:text-neutral-400">
                                    {{ $absence->absence_date->format('M d, Y') }}
                                    @if($absence->end_date && $absence->end_date != $absence->absence_date)
                                        - {{ $absence->end_date->format('M d, Y') }}
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-neutral-600 dark:text-neutral-400">
                                    {{ $absenceTypeLabels[$absence->absence_type] ?? $absence->absence_type }}
                                </td>
                                <td class="px-4 py-3">
                                    @switch($absence->status)
                                        @case('pending')
                                            <span class="inline-flex items-center rounded-full bg-yellow-100 px-2.5 py-0.5 text-xs font-medium text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                                {{ __('Pending') }}
                                            </span>
                                            @break
                                        @case('approved')
                                            <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800 dark:bg-green-900 dark:text-green-200">
                                                {{ __('Approved') }}
                                            </span>
                                            @break
                                        @case('rejected')
                                            <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800 dark:bg-red-900 dark:text-red-200">
                                                {{ __('Rejected') }}
                                            </span>
                                            @break
                                    @endswitch
                                </td>
                                <td class="px-4 py-3 text-neutral-600 dark:text-neutral-400">
                                    {{ $absence->created_at->format('M d, Y') }}
                                </td>
                                <td class="px-4 py-3 text-end">
                                    <a href="{{ route('teacher.excused-absences.show', $absence) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                        {{ __('View') }}
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4 px-4 pb-4">
                {{ $absences->links() }}
            </div>
        </div>
    @endif
</x-layouts::app>
