<?php
/**
 * Teacher Accommodations - Quiz Accommodation Page
 *
 * Purpose: Apply and manage quiz-specific accommodations for students
 * Route: teacher.accommodations.quiz (GET)
 * Controller: App\Http\Controllers\Teacher\AccommodationController@quiz
 *
 * Components:
 * - x-layouts::app: Main application layout
 * - Back navigation: Return to assessments
 * - Student table: List of students with accommodations for this quiz
 * - Accommodation form: Extended time, additional attempts, breaks
 * - Bulk save: Apply accommodations to selected students
 *
 * Required Data Variables:
 * - $assessment: Assessment model instance (the quiz)
 * - $studentAccommodations: Collection of StudentAccommodation models for enrolled students
 * - $existingQuizAccommodations: Collection of existing QuizAccommodation settings
 *
 * Dependencies:
 * - Routes: teacher.courses.assessments, teacher.accommodations.bulk-apply-quiz
 * - Models: Assessment, StudentAccommodation, User, AccommodationType, QuizAccommodation
 * - Helpers: __(), route()
 */
?>
<x-layouts::app :title="$assessment->title . ' - ' . __('lms.quiz_accommodations')">

<div class="p-6 space-y-6">
    <div class="flex items-center mb-6">
        <a href="{{ route('teacher.courses.assessments', $assessment->courseOffering) }}" class="mr-4 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
            <flux:icon name="arrow-left" class="w-5 h-5" />
        </a>
        <div class="flex-1">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('lms.quiz_accommodations') }}</h1>
            <p class="text-gray-600 dark:text-gray-300">{{ $assessment->title }}</p>
        </div>
    </div>

    @if($studentAccommodations->isEmpty())
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 text-center">
            <flux:icon name="clipboard-document-list" class="w-12 h-12 mx-auto text-gray-400 mb-4" />
            <p class="text-gray-500 dark:text-gray-400">{{ __('lms.no_accommodations') }}</p>
        </div>
    @else
        <form action="{{ route('teacher.accommodations.bulk-apply-quiz', $assessment) }}" method="POST">
            @csrf
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                {{ __('lms.student') }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                {{ __('lms.accommodation_type') }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                {{ __('lms.extended_time') }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                {{ __('lms.additional_attempts') }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                {{ __('lms.allow_breaks') }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                {{ __('lms.status') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($studentAccommodations as $sa)
                            @php
                                $existing = $existingQuizAccommodations->get($sa->id);
                            @endphp
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center mr-3">
                                            <span class="text-blue-600 dark:text-blue-400 text-sm">{{ $sa->student?->name[0] ?? 'S' }}</span>
                                        </div>
                                        <span class="text-gray-900 dark:text-white">{{ $sa->student?->name ?? __('Unknown Student') }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-600 dark:text-gray-300">
                                    {{ $sa->accommodationType?->name ?? __('Unknown') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="number" name="accommodations[{{ $sa->id }}][extended_time_minutes]"
                                        value="{{ $existing->extended_time_minutes ?? '' }}" min="0"
                                        class="w-24 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm"
                                        placeholder="Minutes">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="number" name="accommodations[{{ $sa->id }}][additional_attempts]"
                                        value="{{ $existing->additional_attempts ?? 0 }}" min="0"
                                        class="w-20 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="checkbox" name="accommodations[{{ $sa->id }}][allow_breaks]"
                                        {{ $existing && $existing->allow_breaks ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($existing && $existing->is_applied)
                                        <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 rounded">
                                            {{ __('lms.applied') }}
                                        </span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200 rounded">
                                            {{ __('lms.not_applied') }}
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4 flex justify-end">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    {{ __('lms.save_accommodations') }}
                </button>
            </div>
        </form>
    @endif
</div>
</x-layouts::app>
