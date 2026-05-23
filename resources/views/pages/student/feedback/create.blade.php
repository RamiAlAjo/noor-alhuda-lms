<x-app-layout>
    <x-slot name="title">{{ __('lms.submit_feedback') }}</x-slot>

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
                        {{ __('lms.course_feedback') }}
                    </h1>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        {{ $enrollment->courseOffering?->course?->name ?? __('Course') }} ({{ $enrollment->courseOffering->course->code }})
                    </p>
                    <p class="text-sm text-gray-600 dark:text-gray-300">
                        {{ $enrollment->courseOffering?->semester?->name ?? '' }}
                    </p>
                </div>

                <form method="POST" action="{{ route('student.feedback.store') }}">
                    @csrf
                    <input type="hidden" name="course_offering_id" value="{{ $enrollment->course_offering_id }}">

                    <div class="p-6 space-y-6">
                        <!-- Overall Rating -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('lms.overall_rating') }} <span class="text-red-500">*</span>
                            </label>
                            <div class="flex items-center space-x-1">
                                @for($i = 1; $i <= 5; $i++)
                                    <button type="button" class="star-btn p-1 focus:outline-none" data-rating="{{ $i }}">
                                        <svg class="w-8 h-8 star-icon {{ $i <= old('overall_rating', $feedback->overall_rating ?? 0) ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    </button>
                                @endfor
                                <input type="hidden" name="overall_rating" id="overall_rating" value="{{ old('overall_rating', $feedback->overall_rating ?? '') }}" required>
                            </div>
                            @error('overall_rating')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Detailed Ratings -->
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                            <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">
                                {{ __('lms.detailed_ratings') }} ({{ __('lms.optional') }})
                            </h3>
                            <div class="space-y-4">
                                @foreach(['content_quality' => 'lms.content_quality', 'instructor_knowledge' => 'lms.instructor_knowledge', 'instructor_communication' => 'lms.instructor_communication', 'course_organization' => 'lms.course_organization', 'materials_quality' => 'lms.materials_quality', 'workload_appropriateness' => 'lms.workload_appropriateness'] as $field => $label)
                                    <div>
                                        <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">
                                            {{ __($label) }}
                                        </label>
                                        <div class="flex items-center space-x-1">
                                            @for($i = 1; $i <= 5; $i++)
                                                <button type="button" class="star-btn-{{ $field }} p-1 focus:outline-none" data-rating="{{ $i }}" data-field="{{ $field }}">
                                                    <svg class="w-6 h-6 star-icon-{{ $field }} {{ $i <= old($field, $feedback->$field ?? 0) ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                    </svg>
                                                </button>
                                            @endfor
                                            <input type="hidden" name="{{ $field }}" id="{{ $field }}" value="{{ old($field, $feedback->$field ?? '') }}">
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Qualitative Feedback -->
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-6 space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('lms.course_strengths') }}
                                </label>
                                <textarea name="strengths" rows="3" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="{{ __('lms.strengths_placeholder') }}">{{ old('strengths', $feedback->strengths ?? '') }}</textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('lms.suggested_improvements') }}
                                </label>
                                <textarea name="improvements" rows="3" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="{{ __('lms.improvements_placeholder') }}">{{ old('improvements', $feedback->improvements ?? '') }}</textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('lms.additional_comments') }}
                                </label>
                                <textarea name="additional_comments" rows="3" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="{{ __('lms.comments_placeholder') }}">{{ old('additional_comments', $feedback->additional_comments ?? '') }}</textarea>
                            </div>
                        </div>

                        <!-- Anonymous Option -->
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                            <label class="flex items-center">
                                <input type="checkbox" name="is_anonymous" value="1" class="rounded border-gray-300 dark:border-gray-600 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500" {{ old('is_anonymous', $feedback->is_anonymous ?? false) ? 'checked' : '' }}>
                                <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">
                                    {{ __('lms.submit_anonymously') }}
                                </span>
                            </label>
                            <p class="mt-1 text-xs text-gray-600 dark:text-gray-300">
                                {{ __('lms.anonymous_note') }}
                            </p>
                        </div>
                    </div>

                    <div class="p-6 bg-gray-50 dark:bg-gray-700 border-t border-gray-200 dark:border-gray-600 flex justify-between">
                         <x-button.submit loading-text="{{ __('Saving Draft...') }}" variant="secondary" name="save_draft" value="1">
                             {{ __('lms.save_draft') }}
                         </x-button.submit>
                    <x-button.submit loading-text="{{ __('Submitting...') }}" variant="primary">
                        {{ __('lms.submit_feedback') }}
                    </x-button.submit>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Star rating functionality
        document.querySelectorAll('.star-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const rating = this.dataset.rating;
                document.getElementById('overall_rating').value = rating;
                updateStars('star-icon', rating);
            });
        });

        // Detailed ratings
        @foreach(['content_quality', 'instructor_knowledge', 'instructor_communication', 'course_organization', 'materials_quality', 'workload_appropriateness'] as $field)
        document.querySelectorAll('.star-btn-{{ $field }}').forEach(btn => {
            btn.addEventListener('click', function() {
                const rating = this.dataset.rating;
                document.getElementById('{{ $field }}').value = rating;
                updateStars('star-icon-{{ $field }}', rating);
            });
        });
        @endforeach

        function updateStars(className, rating) {
            document.querySelectorAll('.' + className).forEach((star, index) => {
                if (index < rating) {
                    star.classList.remove('text-gray-300', 'dark:text-gray-600');
                    star.classList.add('text-yellow-400');
                } else {
                    star.classList.remove('text-yellow-400');
                    star.classList.add('text-gray-300', 'dark:text-gray-600');
                }
            });
        }
    </script>
    @endpush
</x-app-layout>
