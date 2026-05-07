<?php
/**
 * Admin Course Feedback - Course Details Page
 *
 * Purpose: View detailed feedback for a specific course including ratings and written responses
 * Route: admin.feedback.course (GET)
 * Controller: App\Http\Controllers\Admin\CourseFeedbackController@course
 *
 * Components:
 * - x-app-layout: Main application layout
 * - Summary statistics: Total responses, average rating, anonymous responses
 * - Rating averages: Category-wise rating breakdown with progress bars
 * - Rating distribution: Bar chart of rating distribution (1-5 stars)
 * - Qualitative feedback: Written feedback with strengths, improvements, comments
 *
 * Required Data Variables:
 * - $courseOffering: CourseOffering model instance
 * - $feedbacks: Collection of CourseFeedback models for this course
 * - $averages: Array of average ratings by category
 * - $ratingDistribution: Array of rating counts
 * - $ratingCategories: Array of rating category labels
 * - $qualitativeFeedback: Collection of written feedback responses
 *
 * Dependencies:
 * - Routes: admin.feedback.index
 * - Models: CourseFeedback, CourseOffering, Course, Semester
 * - Helpers: __(), route()
 */
?>
<x-app-layout>
    <x-slot name="title">{{ __('lms.course_feedback') }} - {{ $courseOffering?->course?->name ?? __('Course') }}</x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-6">
                <a href="{{ route('admin.feedback.index') }}" class="inline-flex items-center text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    {{ __('lms.back_to_feedback') }}
                </a>
            </div>

            <div class="mb-6">
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
                    {{ $courseOffering->course->name }}
                </h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    {{ $courseOffering?->course?->code ?? '' }} - {{ $courseOffering?->semester?->name ?? '' }}
                </p>
            </div>

            <!-- Summary Statistics -->
            <div class="grid gap-6 md:grid-cols-3 mb-6">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">
                        {{ __('lms.total_responses') }}
                    </div>
                    <div class="mt-2 text-3xl font-semibold text-gray-900 dark:text-gray-100">
                        {{ $feedbacks->count() }}
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">
                        {{ __('lms.average_rating') }}
                    </div>
                    <div class="mt-2 flex items-center">
                        <span class="text-3xl font-semibold text-gray-900 dark:text-gray-100">
                            {{ $averages['overall_rating'] ?? 0 }}
                        </span>
                        <span class="ml-2 text-gray-500 dark:text-gray-400">/5</span>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">
                        {{ __('lms.anonymous_responses') }}
                    </div>
                    <div class="mt-2 text-3xl font-semibold text-gray-900 dark:text-gray-100">
                        {{ $feedbacks->where('is_anonymous', true)->count() }}
                    </div>
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-2">
                <!-- Rating Averages -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                            {{ __('lms.rating_averages') }}
                        </h2>
                    </div>
                    <div class="p-6 space-y-4">
                        @foreach($ratingCategories as $field => $label)
                            <div>
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">{{ __($label) }}</span>
                                    <span class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                        {{ $averages[$field] ?? '-' }}/5
                                    </span>
                                </div>
                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                    <div class="bg-yellow-400 h-2 rounded-full" style="width: {{ ($averages[$field] ?? 0) * 20 }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Rating Distribution -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                            {{ __('lms.rating_distribution') }}
                        </h2>
                    </div>
                    <div class="p-6 space-y-3">
                        @for($i = 5; $i >= 1; $i--)
                            <div class="flex items-center">
                                <span class="w-8 text-sm text-gray-600 dark:text-gray-400">{{ $i }} ★</span>
                                <div class="flex-1 mx-3 bg-gray-200 dark:bg-gray-700 rounded-full h-4">
                                    @php $percentage = $feedbacks->count() > 0 ? ($ratingDistribution[$i] / $feedbacks->count()) * 100 : 0; @endphp
                                    <div class="bg-blue-500 h-4 rounded-full" style="width: {{ $percentage }}%"></div>
                                </div>
                                <span class="w-12 text-sm text-gray-600 dark:text-gray-400 text-right">
                                    {{ $ratingDistribution[$i] }}
                                </span>
                            </div>
                        @endfor
                    </div>
                </div>
            </div>

            <!-- Qualitative Feedback -->
            @if($qualitativeFeedback->isNotEmpty())
                <div class="mt-6 bg-white dark:bg-gray-800 rounded-lg shadow">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                            {{ __('lms.written_feedback') }}
                        </h2>
                    </div>
                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($qualitativeFeedback as $response)
                            <div class="p-6">
                                <div class="text-xs text-gray-500 dark:text-gray-400 mb-2">
                                    {{ $response['student_name'] }}
                                </div>
                                @if($response['strengths'])
                                    <div class="mb-3">
                                        <span class="text-xs font-medium text-green-600 dark:text-green-400 uppercase">
                                            {{ __('lms.strengths') }}
                                        </span>
                                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                            {{ $response['strengths'] }}
                                        </p>
                                    </div>
                                @endif
                                @if($response['improvements'])
                                    <div class="mb-3">
                                        <span class="text-xs font-medium text-orange-600 dark:text-orange-400 uppercase">
                                            {{ __('lms.improvements') }}
                                        </span>
                                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                            {{ $response['improvements'] }}
                                        </p>
                                    </div>
                                @endif
                                @if($response['additional_comments'])
                                    <div>
                                        <span class="text-xs font-medium text-blue-600 dark:text-blue-400 uppercase">
                                            {{ __('lms.additional_comments') }}
                                        </span>
                                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                            {{ $response['additional_comments'] }}
                                        </p>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
