<?php
/**
 * Page: Admin Attendance Reports
 *
 * Purpose: Display attendance analytics and trends with filtering capabilities.
 * Shows summary statistics and detailed attendance records.
 *
 * Route: admin.reports.attendance (GET)
 *
 * Controller: App\Http\Controllers\Admin\ReportController@attendance
 *
 * Components on this page:
 * - x-layouts::app: Main application layout wrapper
 * - Stats cards: Total, Present, Absent, Excused, Late
 * - Filter form with course, date_from, date_to
 * - Attendance records table with date, student, course, status
 * - Pagination links
 * - Empty state when no data
 *
 * Required Data variables:
 * - $total: Total attendance records
 * - $present: Present count
 * - $absent: Absent count
 * - $excused: Excused count
 * - $late: Late count
 * - $sections: Collection of CourseSection objects
 * - $attendances: Collection of Attendance records (paginated)
 *
 * Dependencies:
 * - Helpers: __()
 * - Relationships: Attendance->enrollment->student, Attendance->enrollment->section->course
 *
 * @package App\Views\Pages\Admin\Reports
 */
?>
<x-layouts::app :title="__('Attendance Reports')">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-neutral-900 dark:text-neutral-100">{{ __('Attendance Reports') }}</h1>
        <p class="mt-1 text-neutral-500 dark:text-neutral-400">{{ __('View attendance analytics and trends') }}</p>
    </div>

    <!-- Stats Cards -->
    <div class="mb-6 grid gap-4 md:grid-cols-5">
        <div class="rounded-xl border border-neutral-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-100 text-blue-600 dark:bg-blue-900 dark:text-blue-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Total') }}</p>
                    <p class="text-2xl font-bold text-neutral-900 dark:text-neutral-100">{{ $total }}</p>
                </div>
            </div>
        </div>
        <div class="rounded-xl border border-neutral-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-green-100 text-green-600 dark:bg-green-900 dark:text-green-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Present') }}</p>
                    <p class="text-2xl font-bold text-neutral-900 dark:text-neutral-100">{{ $present }}</p>
                </div>
            </div>
        </div>
        <div class="rounded-xl border border-neutral-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-red-100 text-red-600 dark:bg-red-900 dark:text-red-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Absent') }}</p>
                    <p class="text-2xl font-bold text-neutral-900 dark:text-neutral-100">{{ $absent }}</p>
                </div>
            </div>
        </div>
        <div class="rounded-xl border border-neutral-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-yellow-100 text-yellow-600 dark:bg-yellow-900 dark:text-yellow-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Excused') }}</p>
                    <p class="text-2xl font-bold text-neutral-900 dark:text-neutral-100">{{ $excused }}</p>
                </div>
            </div>
        </div>
        <div class="rounded-xl border border-neutral-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-orange-100 text-orange-600 dark:bg-orange-900 dark:text-orange-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Late') }}</p>
                    <p class="text-2xl font-bold text-neutral-900 dark:text-neutral-100">{{ $late }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="mb-6 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
        <h3 class="mb-4 text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Filter Attendance') }}</h3>

        <form method="GET" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">{{ __('Course') }}</label>
                <select name="section_id" class="w-full rounded-lg border-neutral-300 dark:border-neutral-600 dark:bg-neutral-800">
                    <option value="">{{ __('All Courses') }}</option>
                    @foreach($sections as $section)
                        <option value="{{ $section->id }}">{{ $section->course?->name ?? __('Unknown') }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">{{ __('Date From') }}</label>
                <input type="date" name="date_from" class="w-full rounded-lg border-neutral-300 dark:border-neutral-600 dark:bg-neutral-800">
            </div>
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">{{ __('Date To') }}</label>
                <input type="date" name="date_to" class="w-full rounded-lg border-neutral-300 dark:border-neutral-600 dark:bg-neutral-800">
            </div>
            <div class="flex items-end">
                <x-button.submit loading-text="{{ __('Applying...') }}">
                    {{ __('Apply Filters') }}
                </x-button.submit>
            </div>
        </form>
    </div>

    <!-- Attendance Records -->
    <div class="rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
        <h3 class="mb-4 text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Attendance Records') }}</h3>

        @if($attendances->isEmpty())
            <div class="text-center py-8 text-neutral-500 dark:text-neutral-400">
                {{ __('No attendance data available') }}
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-neutral-200 dark:border-neutral-700">
                            <th class="pb-3 text-start text-sm font-medium text-neutral-500 dark:text-neutral-400">{{ __('Date') }}</th>
                            <th class="pb-3 text-start text-sm font-medium text-neutral-500 dark:text-neutral-400">{{ __('Student') }}</th>
                            <th class="pb-3 text-start text-sm font-medium text-neutral-500 dark:text-neutral-400">{{ __('Course') }}</th>
                            <th class="pb-3 text-start text-sm font-medium text-neutral-500 dark:text-neutral-400">{{ __('Status') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($attendances as $attendance)
                            <tr class="border-b border-neutral-100 dark:border-neutral-800">
                                <td class="py-3 text-neutral-900 dark:text-neutral-100">
                                    {{ $attendance->date?->format('M d, Y') ?? '-' }}
                                </td>
                                <td class="py-3 text-neutral-900 dark:text-neutral-100">
                                    {{ $attendance->enrollment?->student?->name ?? __('Unknown') }}
                                </td>
                                <td class="py-3 text-neutral-900 dark:text-neutral-100">
                                    {{ $attendance->enrollment?->section?->course?->name ?? __('Unknown') }}
                                </td>
                                <td class="py-3">
                                    @switch($attendance->status)
                                        @case('present')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                {{ __('Present') }}
                                            </span>
                                            @break
                                        @case('absent')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                                {{ __('Absent') }}
                                            </span>
                                            @break
                                        @case('excused')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                                {{ __('Excused') }}
                                            </span>
                                            @break
                                        @case('late')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200">
                                                {{ __('Late') }}
                                            </span>
                                            @break
                                    @endswitch
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $attendances->links() }}
            </div>
        @endif
    </div>
</x-layouts::app>
