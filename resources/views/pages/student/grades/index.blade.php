<?php
/**
 * Student Grades - Index Page
 *
 * Purpose: Display student's grades across all enrolled courses with GPA summary
 * Route: student.grades.index (GET)
 * Controller: App\Http\Controllers\Student\GradeController@index
 *
 * Components:
 * - x-layouts::app: Main application layout
 * - GPA Summary cards: Cumulative GPA, Total Credits, Courses Enrolled
 * - Course grades: Accordion-style display for each enrolled course
 * - Grade table: Assessment, type, score, percentage, date
 * - Color-coded percentages: Green (90+), Emerald (80+), Amber (70+), Orange (60+), Red (<60)
 * - View Transcript button: Link to transcript page
 * - View Detailed Grades button: Link to course-specific grades
 *
 * Required Data Variables:
 * - $gpa: Cumulative GPA value
 * - $totalCredits: Total credit hours completed
 * - $enrollments: Collection of Enrollment models for the student
 * - $allGrades: Collection of all Grade models for the student
 *
 * Dependencies:
 * - Routes: student.transcript.index, student.courses.grades, student.courses.browse
 * - Models: Enrollment, CourseOffering, Course, Semester, Grade, Assessment
 * - Helpers: __(), route(), number_format()
 */
?>
<x-layouts::app :title="__('My Grades')">
    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-neutral-900 dark:text-neutral-100">{{ __('My Grades') }}</h1>
            <p class="mt-1 text-neutral-500 dark:text-neutral-400">{{ __('View your academic performance across all courses') }}</p>
        </div>
        <div class="flex gap-2">
            <flux:button :href="route('student.transcript.index')" variant="ghost">
                <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                {{ __('View Transcript') }}
            </flux:button>
            <flux:button :href="route('student.grades.export')" variant="outline">
                <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                {{ __('Export PDF') }}
            </flux:button>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="mb-6 rounded-xl border border-neutral-200 bg-white p-4 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
        <form method="GET" class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div class="flex flex-1 gap-4">
                <!-- Search -->
                <div class="relative flex-1 max-w-md">
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="{{ __('Search courses...') }}"
                        class="w-full rounded-lg border border-neutral-300 bg-white px-4 py-2 pl-10 text-neutral-900 focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500 dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-100"
                    >
                    <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-2.5 size-5 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>

                <!-- Semester Filter -->
                <select
                    name="semester"
                    class="rounded-lg border border-neutral-300 bg-white px-4 py-2 text-neutral-900 focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500 dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-100"
                >
                    <option value="">{{ __('All Semesters') }}</option>
                    <option value="current" {{ request('semester') === 'current' ? 'selected' : '' }}>{{ __('Current Semester') }}</option>
                    <option value="previous" {{ request('semester') === 'previous' ? 'selected' : '' }}>{{ __('Previous Semesters') }}</option>
                </select>
            </div>

            <div class="flex gap-2">
                <!-- Sort -->
                <select
                    name="sort"
                    class="rounded-lg border border-neutral-300 bg-white px-4 py-2 text-neutral-900 focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500 dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-100"
                >
                    <option value="newest" {{ request('sort', 'newest') === 'newest' ? 'selected' : '' }}>{{ __('Newest First') }}</option>
                    <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>{{ __('Oldest First') }}</option>
                    <option value="grade_desc" {{ request('sort') === 'grade_desc' ? 'selected' : '' }}>{{ __('Highest Grade') }}</option>
                    <option value="grade_asc" {{ request('sort') === 'grade_asc' ? 'selected' : '' }}>{{ __('Lowest Grade') }}</option>
                </select>

                <flux:button type="submit" variant="primary">
                    {{ __('Filter') }}
                </flux:button>

                @if(request()->anyFilled(['search', 'semester', 'sort']))
                <flux:button :href="route('student.grades.index')" variant="ghost">
                    {{ __('Clear') }}
                </flux:button>
                @endif
            </div>
        </form>
    </div>

    <!-- GPA Summary Cards -->
    <div class="mb-8 grid gap-4 md:grid-cols-3">
        <div class="relative overflow-hidden rounded-2xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="absolute inset-0 bg-gradient-to-br from-violet-50 to-purple-50 dark:from-violet-900/20 dark:to-purple-900/20"></div>
            <div class="relative">
                <p class="text-sm font-medium text-neutral-500 dark:text-neutral-400">{{ __('Cumulative GPA') }}</p>
                <p class="mt-2 text-4xl font-bold text-neutral-900 dark:text-neutral-100">{{ number_format($gpa, 2) }}</p>
                <p class="mt-3 text-sm text-neutral-500 dark:text-neutral-400">{{ __('Out of 4.0') }}</p>
            </div>
        </div>
        <div class="relative overflow-hidden rounded-2xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="absolute inset-0 bg-gradient-to-br from-emerald-50 to-teal-50 dark:from-emerald-900/20 dark:to-teal-900/20"></div>
            <div class="relative">
                <p class="text-sm font-medium text-neutral-500 dark:text-neutral-400">{{ __('Total Credits') }}</p>
                <p class="mt-2 text-4xl font-bold text-neutral-900 dark:text-neutral-100">{{ $totalCredits }}</p>
                <p class="mt-3 text-sm text-neutral-500 dark:text-neutral-400">{{ __('Credit hours completed') }}</p>
            </div>
        </div>
        <div class="relative overflow-hidden rounded-2xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="absolute inset-0 bg-gradient-to-br from-amber-50 to-orange-50 dark:from-amber-900/20 dark:to-orange-900/20"></div>
            <div class="relative">
                <p class="text-sm font-medium text-neutral-500 dark:text-neutral-400">{{ __('Courses Enrolled') }}</p>
                <p class="mt-2 text-4xl font-bold text-neutral-900 dark:text-neutral-100">{{ $enrollments->count() }}</p>
                <p class="mt-3 text-sm text-neutral-500 dark:text-neutral-400">{{ __('Active courses') }}</p>
            </div>
        </div>
    </div>

    <!-- Performance Analytics -->
    <div class="mb-8 grid gap-4 md:grid-cols-4">
        <div class="relative overflow-hidden rounded-2xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="absolute inset-0 bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20"></div>
            <div class="relative">
                <p class="text-sm font-medium text-neutral-500 dark:text-neutral-400">{{ __('A Grades') }}</p>
                <p class="mt-2 text-3xl font-bold text-neutral-900 dark:text-neutral-100">{{ $allGrades->where('percentage', '>=', 90)->count() }}</p>
                <p class="mt-3 text-sm text-neutral-500 dark:text-neutral-400">{{ __('90%+ grades') }}</p>
            </div>
        </div>
        <div class="relative overflow-hidden rounded-2xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20"></div>
            <div class="relative">
                <p class="text-sm font-medium text-neutral-500 dark:text-neutral-400">{{ __('B Grades') }}</p>
                <p class="mt-2 text-3xl font-bold text-neutral-900 dark:text-neutral-100">{{ $allGrades->whereBetween('percentage', [80, 89])->count() }}</p>
                <p class="mt-3 text-sm text-neutral-500 dark:text-neutral-400">{{ __('80-89% grades') }}</p>
            </div>
        </div>
        <div class="relative overflow-hidden rounded-2xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="absolute inset-0 bg-gradient-to-br from-amber-50 to-orange-50 dark:from-amber-900/20 dark:to-orange-900/20"></div>
            <div class="relative">
                <p class="text-sm font-medium text-neutral-500 dark:text-neutral-400">{{ __('C Grades') }}</p>
                <p class="mt-2 text-3xl font-bold text-neutral-900 dark:text-neutral-100">{{ $allGrades->whereBetween('percentage', [70, 79])->count() }}</p>
                <p class="mt-3 text-sm text-neutral-500 dark:text-neutral-400">{{ __('70-79% grades') }}</p>
            </div>
        </div>
        <div class="relative overflow-hidden rounded-2xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="absolute inset-0 bg-gradient-to-br from-red-50 to-rose-50 dark:from-red-900/20 dark:to-rose-900/20"></div>
            <div class="relative">
                <p class="text-sm font-medium text-neutral-500 dark:text-neutral-400">{{ __('Needs Improvement') }}</p>
                <p class="mt-2 text-3xl font-bold text-neutral-900 dark:text-neutral-100">{{ $allGrades->where('percentage', '<', 70)->count() }}</p>
                <p class="mt-3 text-sm text-neutral-500 dark:text-neutral-400">{{ __('< 70% grades') }}</p>
            </div>
        </div>
    </div>

    <!-- Grades by Course -->
    @if($enrollments->isNotEmpty())
    <div class="space-y-6">
        @foreach($enrollments as $enrollment)
        @php
            $courseGrades = $allGrades->filter(function($grade) use ($enrollment) {
                return $grade->assessment && $grade->assessment->course_offering_id === $enrollment->course_offering_id;
            });
            $courseAverage = $courseGrades->isNotEmpty() ? $courseGrades->avg('percentage') : 0;
        @endphp
        <div class="rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800 overflow-hidden">
            <!-- Course Header -->
            <div class="border-b border-neutral-200 bg-gradient-to-r from-indigo-50 to-purple-50 px-6 py-4 dark:border-neutral-700 dark:from-indigo-900/20 dark:to-purple-900/20">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">
                            {{ $enrollment->offering?->course?->name ?? __('Unknown Course') }}
                        </h3>
                        <p class="text-sm text-neutral-500 dark:text-neutral-400">
                            {{ $enrollment->offering?->course?->code ?? '' }} - {{ $enrollment->offering?->semester?->name ?? __('Current Semester') }}
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="text-2xl font-bold text-neutral-900 dark:text-neutral-100">{{ number_format($courseAverage, 1) }}%</p>
                        <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Course Average') }}</p>
                    </div>
                </div>
            </div>

            <!-- Grade Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-neutral-50 text-neutral-500 dark:bg-neutral-700 dark:text-neutral-400">
                        <tr>
                            <th class="px-6 py-3">{{ __('Assessment') }}</th>
                            <th class="px-6 py-3">{{ __('Type') }}</th>
                            <th class="px-6 py-3">{{ __('Score') }}</th>
                            <th class="px-6 py-3">{{ __('Percentage') }}</th>
                            <th class="px-6 py-3">{{ __('Date') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                        @forelse($courseGrades as $grade)
                        <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-700/50">
                            <td class="px-6 py-4 font-medium text-neutral-900 dark:text-neutral-100">
                                {{ $grade->assessment?->title ?? '-' }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center rounded-full bg-purple-100 px-2.5 py-0.5 text-xs font-medium text-purple-800 dark:bg-purple-900 dark:text-purple-200">
                                    {{ __($grade->assessment?->type ?? 'N/A') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-neutral-900 dark:text-neutral-100">
                                {{ $grade->grade ?? '-' }} / {{ $grade->assessment?->max_grade ?? '-' }}
                            </td>
                            <td class="px-6 py-4">
                                @php $percentage = $grade->percentage; @endphp
                                <span class="font-medium
                                    @if($percentage >= 90) text-green-600 dark:text-green-400
                                    @elseif($percentage >= 80) text-emerald-600 dark:text-emerald-400
                                    @elseif($percentage >= 70) text-amber-600 dark:text-amber-400
                                    @elseif($percentage >= 60) text-orange-600 dark:text-orange-400
                                    @else text-red-600 dark:text-red-400 @endif">
                                    {{ number_format($percentage, 1) }}%
                                </span>
                            </td>
                            <td class="px-6 py-4 text-neutral-500 dark:text-neutral-400">
                                {{ $grade->created_at->format('Y-m-d') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-neutral-500 dark:text-neutral-400">
                                {{ __('No grades available for this course yet') }}
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Course Actions -->
            <div class="border-t border-neutral-200 bg-neutral-50 px-6 py-3 dark:border-neutral-700 dark:bg-neutral-800/50">
                @if($enrollment->offering)
                <flux:button :href="route('student.courses.grades', $enrollment->offering)" variant="subtle" size="sm">
                    {{ __('View Detailed Grades') }}
                </flux:button>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="rounded-xl border border-neutral-200 bg-white p-12 text-center shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
        <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-neutral-100 dark:bg-neutral-700">
            <svg xmlns="http://www.w3.org/2000/svg" class="size-8 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
        </div>
        <h3 class="text-lg font-medium text-neutral-900 dark:text-neutral-100">{{ __('No courses enrolled') }}</h3>
        <p class="mt-1 text-neutral-500 dark:text-neutral-400">{{ __('Enroll in courses to see your grades here') }}</p>
        <flux:button :href="route('student.courses.browse')" variant="primary" class="mt-4">
            {{ __('Browse Courses') }}
        </flux:button>
    </div>
    @endif
</x-layouts::app>
