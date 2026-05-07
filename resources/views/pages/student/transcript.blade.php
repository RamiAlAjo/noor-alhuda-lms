<?php
/**
 * Page: Student Academic Transcript
 *
 * Purpose: Display student's complete academic record including GPA, credits, and course history.
 * Allows students to view and download their official transcript.
 *
 * Route: student.transcript (GET)
 *
 * Controller: App\Http\Controllers\Student\TranscriptController@show
 *
 * Components on this page:
 * - x-layouts::app: Main application layout wrapper
 * - Header with Download PDF button
 * - Student information card (ID, name, major, enrollment year)
 * - GPA summary cards (Cumulative GPA, Total Credits, Total Courses)
 * - Course records table (empty state)
 *
 * Required Data variables:
 * - $user: User model instance
 * - $gpa: Cumulative GPA value
 * - $totalCredits: Total credits earned
 * - $completedCourses: Number of completed courses
 *
 * Dependencies:
 * - Helpers: __(), number_format()
 * - Relationships: User->profile->major
 * - Flux UI components: flux:button
 *
 * @package App\Views\Pages\Student
 */
?>
<x-layouts::app :title="__('Academic Transcript')">
    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-neutral-900 dark:text-neutral-100">{{ __('Academic Transcript') }}</h1>
            <p class="mt-1 text-neutral-500 dark:text-neutral-400">{{ __('Your complete academic record') }}</p>
        </div>
        <flux:button variant="primary">
            <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            {{ __('Download PDF') }}
        </flux:button>
    </div>

    <!-- Student Info -->
    <div class="mb-6 rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
        <div class="border-b border-neutral-200 px-6 py-4 dark:border-neutral-700">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-100 text-indigo-600 dark:bg-indigo-900 dark:text-indigo-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Student Information') }}</h2>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Personal details') }}</p>
                </div>
            </div>
        </div>
        <div class="p-6">
            <div class="grid gap-4 md:grid-cols-4">
                <div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Student ID') }}</p>
                    <p class="font-medium text-neutral-900 dark:text-neutral-100">{{ $user->id }}</p>
                </div>
                <div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Student Name') }}</p>
                    <p class="font-medium text-neutral-900 dark:text-neutral-100">{{ $user->full_name ?? $user->name }}</p>
                </div>
                <div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Major') }}</p>
                    <p class="font-medium text-neutral-900 dark:text-neutral-100">{{ $user->profile?->major?->name ?? __('Not assigned') }}</p>
                </div>
                <div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Enrollment Year') }}</p>
                    <p class="font-medium text-neutral-900 dark:text-neutral-100">{{ $user->created_at->format('Y') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- GPA Summary -->
    <div class="mb-6 grid gap-4 md:grid-cols-3">
        <div class="group relative overflow-hidden rounded-xl border border-neutral-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="absolute inset-0 bg-gradient-to-br from-violet-50 to-purple-50 opacity-0 transition-opacity group-hover:opacity-100 dark:from-violet-900/20 dark:to-purple-900/20"></div>
            <div class="relative">
                <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Cumulative GPA') }}</p>
                <p class="mt-1 text-3xl font-bold text-neutral-900 dark:text-neutral-100">{{ number_format($gpa, 2) }}</p>
            </div>
        </div>
        <div class="group relative overflow-hidden rounded-xl border border-neutral-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="absolute inset-0 bg-gradient-to-br from-emerald-50 to-teal-50 opacity-0 transition-opacity group-hover:opacity-100 dark:from-emerald-900/20 dark:to-teal-900/20"></div>
            <div class="relative">
                <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Total Credits') }}</p>
                <p class="mt-1 text-3xl font-bold text-neutral-900 dark:text-neutral-100">{{ $totalCredits }}</p>
            </div>
        </div>
        <div class="group relative overflow-hidden rounded-xl border border-neutral-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-50 to-indigo-50 opacity-0 transition-opacity group-hover:opacity-100 dark:from-blue-900/20 dark:to-indigo-900/20"></div>
            <div class="relative">
                <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Total Courses') }}</p>
                <p class="mt-1 text-3xl font-bold text-neutral-900 dark:text-neutral-100">{{ $completedCourses }}</p>
            </div>
        </div>
    </div>

    <!-- Transcript Records -->
    <div class="rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
        <div class="border-b border-neutral-200 px-6 py-4 dark:border-neutral-700">
            <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Course Records') }}</h2>
        </div>
        <div class="p-6 text-center">
            <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-neutral-100 mx-auto dark:bg-neutral-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-8 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <p class="text-neutral-500 dark:text-neutral-400">{{ __('Your course records will appear here') }}</p>
        </div>
    </div>
</x-layouts::app>
