<?php
/**
 * Admin Academic Standings - Create Page
 *
 * Purpose: Form to manually create a new academic standing record
 * Route: admin.academic-standings.create (GET)
 * Controller: App\Http\Controllers\Admin\AcademicStandingController@create
 *
 * Components:
 * - x-app-layout: Main application layout
 * - Create form: Form with student, standing, type, dates, reason, notes
 *
 * Required Data Variables:
 * - $semesters: Collection of Semester models
 * - $standingOptions: Array of standing type options
 * - $standingTypes: Array of standing type categories
 *
 * Dependencies:
 * - Routes: admin.academic-standings.index, admin.academic-standings.store
 * - Models: User (students with role), Semester
 * - Helpers: __(), route()
 */
?>
<x-app-layout>
    <x-slot name="title">{{ __('lms.create_academic_standing') }}</x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Back Button -->
            <div class="mb-6">
                <a href="{{ route('admin.academic-standings.index') }}" class="inline-flex items-center text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    {{ __('lms.back_to_standings') }}
                </a>
            </div>

            <!-- Create Form -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100">
                        {{ __('lms.create_academic_standing') }}
                    </h2>
                </div>

                <form method="POST" action="{{ route('admin.academic-standings.store') }}" class="p-6">
                    @csrf

                    <div class="grid grid-cols-1 gap-6">
                        <!-- Student -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('lms.student') }} *</label>
                            <select name="student_id" required class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500">
                                <option value="">{{ __('lms.select_student') }}</option>
                                @foreach(\App\Models\User::role('student')->orderBy('name')->get() as $student)
                                    <option value="{{ $student->id }}">{{ $student->name }} ({{ $student->user_id }})</option>
                                @endforeach
                            </select>
                            @error('student_id')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Semester -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('lms.semester') }}</label>
                            <select name="semester_id" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500">
                                <option value="">{{ __('lms.select_semester') }}</option>
                                @foreach($semesters as $semester)
                                    <option value="{{ $semester->id }}">{{ $semester->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Standing -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('lms.standing') }} *</label>
                            <select name="standing" required class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500">
                                <option value="">{{ __('lms.select_standing') }}</option>
                                @foreach($standingOptions as $option)
                                    <option value="{{ $option }}">{{ __(ucwords(str_replace('_', ' ', $option))) }}</option>
                                @endforeach
                            </select>
                            @error('standing')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Standing Type -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('lms.type') }}</label>
                            <select name="standing_type" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500">
                                @foreach($standingTypes as $type)
                                    <option value="{{ $type }}">{{ ucfirst($type) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Reason -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('lms.reason') }} *</label>
                            <textarea name="reason" rows="4" required class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500"
                                placeholder="{{ __('lms.reason_placeholder') }}"></textarea>
                            @error('reason')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Notes -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('lms.notes') }}</label>
                            <textarea name="notes" rows="3" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500"
                                placeholder="{{ __('lms.notes_placeholder') }}"></textarea>
                        </div>

                        <!-- Dates -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('lms.start_date') }}</label>
                                <input type="date" name="start_date" value="{{ now()->format('Y-m-d') }}"
                                    class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('lms.end_date') }}</label>
                                <input type="date" name="end_date"
                                    class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end gap-3">
                        <a href="{{ route('admin.academic-standings.index') }}" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-md transition">
                            {{ __('lms.cancel') }}
                        </a>
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md transition">
                            {{ __('lms.save') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
