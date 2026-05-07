<x-app-layout>
    <x-slot name="title">{{ __('lms.feedback_details') }}</x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-6">
                <a href="{{ route('student.feedback.index') }}" class="inline-flex items-center text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    {{ __('lms.back_to_feedback') }}
                </a>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h1 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                        {{ $feedback->courseOffering?->course?->name ?? __('Course') }}
                    </h1>
                    <p class="text-sm text-gray-600 dark:text-gray-300">
                        {{ $feedback->courseOffering?->course?->code ?? '' }} - {{ $feedback->courseOffering?->semester?->name ?? '' }}
                    </p>
                    <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">
                        {{ __('lms.submitted_on') }}: {{ $feedback->submitted_at->format('M d, Y') }}
                    </p>
                </div>

                <div class="p-6 space-y-6">
                    <!-- Overall Rating -->
                    <div>
                        <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('lms.overall_rating') }}
                        </h3>
                        <div class="flex items-center">
                            @for($i = 1; $i <= 5; $i++)
                                <svg class="w-6 h-6 {{ $i <= $feedback->overall_rating ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            @endfor
                            <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">
                                {{ $feedback->overall_rating }}/5
                            </span>
                        </div>
                    </div>

                    <!-- Detailed Ratings -->
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                        <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">
                            {{ __('lms.detailed_ratings') }}
                        </h3>
                        <div class="space-y-3">
                            @foreach($ratingCategories as $field => $label)
                                @if($field !== 'overall_rating' && $feedback->$field)
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ __($label) }}</span>
                                        <div class="flex items-center">
                                            @for($i = 1; $i <= 5; $i++)
                                                <svg class="w-4 h-4 {{ $i <= $feedback->$field ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                </svg>
                                            @endfor
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>

                    <!-- Qualitative Feedback -->
                    @if($feedback->strengths || $feedback->improvements || $feedback->additional_comments)
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-6 space-y-4">
                            @if($feedback->strengths)
                                <div>
                                    <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        {{ __('lms.course_strengths') }}
                                    </h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 bg-gray-50 dark:bg-gray-700 p-3 rounded-md">
                                        {{ $feedback->strengths }}
                                    </p>
                                </div>
                            @endif

                            @if($feedback->improvements)
                                <div>
                                    <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        {{ __('lms.suggested_improvements') }}
                                    </h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 bg-gray-50 dark:bg-gray-700 p-3 rounded-md">
                                        {{ $feedback->improvements }}
                                    </p>
                                </div>
                            @endif

                            @if($feedback->additional_comments)
                                <div>
                                    <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        {{ __('lms.additional_comments') }}
                                    </h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 bg-gray-50 dark:bg-gray-700 p-3 rounded-md">
                                        {{ $feedback->additional_comments }}
                                    </p>
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- Anonymous Status -->
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                        <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                            @if($feedback->is_anonymous)
                                <svg class="w-4 h-4 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                {{ __('lms.submitted_anonymously') }}
                            @else
                                <svg class="w-4 h-4 mr-2 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                </svg>
                                {{ __('lms.submitted_with_identity') }}
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
