<?php
/**
 * Teacher Accommodations - Student Details Page
 *
 * Purpose: View detailed accommodation information for a specific student
 * Route: teacher.accommodations.student (GET)
 * Controller: App\Http\Controllers\Teacher\AccommodationController@student
 *
 * Components:
 * - x-layouts::app: Main application layout
 * - Back navigation: Return to accommodations index
 * - Student info: Name and email display
 * - Accommodation cards: All accommodations for the student
 * - Quiz accommodations: Quiz-specific settings for each accommodation
 *
 * Required Data Variables:
 * - $student: User model instance (the student)
 * - $accommodations: Collection of StudentAccommodation models for the student
 *
 * Dependencies:
 * - Routes: teacher.accommodations.index
 * - Models: StudentAccommodation, User, AccommodationType, QuizAccommodation, Assessment
 * - Helpers: __(), route()
 */
?>
<x-layouts::app :title="$student->name . ' - ' . __('lms.accommodations')">

<div class="p-6 space-y-6">
    <div class="flex items-center mb-6">
        <a href="{{ route('teacher.accommodations.index') }}" class="mr-4 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
            <flux:icon name="arrow-left" class="w-5 h-5" />
        </a>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $student->name }} - {{ __('lms.accommodations') }}</h1>
    </div>

    <!-- Student Info -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center">
                <span class="text-blue-600 dark:text-blue-400 font-medium text-xl">{{ $student->name[0] ?? 'S' }}</span>
            </div>
            <div>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">{{ $student->name }}</h2>
                <p class="text-gray-600 dark:text-gray-300">{{ $student->email }}</p>
            </div>
        </div>
    </div>

    @if($accommodations->isEmpty())
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 text-center">
            <flux:icon name="clipboard-document-list" class="w-12 h-12 mx-auto text-gray-400 mb-4" />
            <p class="text-gray-500 dark:text-gray-400">{{ __('lms.no_accommodations') }}</p>
        </div>
    @else
        <div class="space-y-4">
            @foreach($accommodations as $accommodation)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ $accommodation->accommodationType->name }}
                            </h3>
                            <p class="text-gray-600 dark:text-gray-300 mt-1">
                                {{ $accommodation->start_date->format('M d, Y') }} - {{ $accommodation->end_date->format('M d, Y') }}
                            </p>
                            @if($accommodation->description)
                                <p class="text-sm text-gray-500 mt-2">{{ $accommodation->description }}</p>
                            @endif
                        </div>
                        <span class="px-3 py-1 rounded-full text-sm font-medium
                            {{ $accommodation->status === 'active' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200' }}">
                            {{ __('lms.' . $accommodation->status) }}
                        </span>
                    </div>

                    @if($accommodation->quizAccommodations->isNotEmpty())
                        <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <h4 class="font-medium text-gray-900 dark:text-white mb-2">{{ __('lms.quiz_accommodations') }}</h4>
                            <div class="space-y-2">
                                @foreach($accommodation->quizAccommodations as $qa)
                                    <div class="bg-gray-50 dark:bg-gray-700 p-3 rounded-lg">
                                        <p class="font-medium text-gray-900 dark:text-white">{{ $qa->assessment->title }}</p>
                                        <p class="text-sm text-gray-500">
                                            @if($qa->extended_time_minutes)
                                                +{{ $qa->extended_time_minutes }} min
                                            @endif
                                            @if($qa->extended_time_percentage)
                                                +{{ $qa->extended_time_percentage }}%
                                            @endif
                                            @if($qa->additional_attempts)
                                                +{{ $qa->additional_attempts }} attempts
                                            @endif
                                        </p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</div>
</x-layouts::app>
