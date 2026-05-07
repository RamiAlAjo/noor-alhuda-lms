<?php
/**
 * Teacher Accommodations - Index Page
 *
 * Purpose: View student accommodations for teacher's course offerings
 * Route: teacher.accommodations.index (GET)
 * Controller: App\Http\Controllers\Teacher\AccommodationController@index
 *
 * Components:
 * - x-layouts::app: Main application layout
 * - Course filter: Dropdown to select course offering
 * - Student cards: Grouped display of student accommodations
 * - View details button: Link to student accommodation details
 *
 * Required Data Variables:
 * - $offerings: Collection of CourseOffering models for the teacher
 * - $offeringId: Currently selected offering ID
 * - $accommodations: Grouped collection of StudentAccommodation models
 *
 * Dependencies:
 * - Routes: teacher.accommodations.student
 * - Models: StudentAccommodation, User, CourseOffering, Course, AccommodationType
 * - Helpers: __(), route()
 */
?>
<x-layouts::app :title="__('lms.student_accommodations')">

<div class="p-6 space-y-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('lms.student_accommodations') }}</h1>
    </div>

    <!-- Course Filter -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
        <form method="GET" class="flex items-center gap-4">
            <label for="offering_id" class="text-sm font-medium text-gray-700 dark:text-gray-300">
                {{ __('lms.select_course') }}:
            </label>
            <select name="offering_id" id="offering_id" onchange="this.form.submit()"
                class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                <option value="">{{ __('lms.all_courses') }}</option>
                @foreach($offerings as $offering)
                    <option value="{{ $offering->id }}" {{ $offeringId == $offering->id ? 'selected' : '' }}>
                        {{ $offering->course?->code ?? '' }} - {{ $offering->course?->name ?? __('Course') }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    @if($offeringId && $accommodations->isEmpty())
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 text-center">
            <flux:icon name="user-group" class="w-12 h-12 mx-auto text-gray-400 mb-4" />
            <p class="text-gray-500 dark:text-gray-400">{{ __('lms.no_accommodations') }}</p>
        </div>
    @elseif($offeringId)
        @foreach($accommodations as $studentId => $studentAccommodations)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center">
                            <span class="text-blue-600 dark:text-blue-400 font-medium text-lg">
                                {{ $studentAccommodations->first()?->student?->name[0] ?? 'S' }}
                            </span>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ $studentAccommodations->first()?->student?->name ?? __('Unknown Student') }}
                            </h3>
                            <p class="text-sm text-gray-500">
                                {{ $studentAccommodations->first()?->student?->email ?? '' }}
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('teacher.accommodations.student', $studentAccommodations->first()?->student) }}"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        {{ __('lms.view_details') }}
                    </a>
                </div>

                <div class="grid gap-2 md:grid-cols-2 lg:grid-cols-3">
                    @foreach($studentAccommodations as $accommodation)
                        <div class="bg-gray-50 dark:bg-gray-700 p-3 rounded-lg">
                            <p class="font-medium text-gray-900 dark:text-white">
                                {{ $accommodation->accommodationType?->name ?? __('Unknown Type') }}
                            </p>
                            <p class="text-sm text-gray-500">
                                {{ $accommodation->start_date->format('M d, Y') }} - {{ $accommodation->end_date->format('M d, Y') }}
                            </p>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    @else
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 text-center">
            <flux:icon name="information-circle" class="w-12 h-12 mx-auto text-gray-400 mb-4" />
            <p class="text-gray-500 dark:text-gray-400">{{ __('lms.select_course_to_view_accommodations') }}</p>
        </div>
    @endif
</div>
</x-layouts::app>
