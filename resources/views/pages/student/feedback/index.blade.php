<x-app-layout>
    <x-slot name="title">{{ __('lms.course_feedback') }}</x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-6">
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
                    {{ __('lms.course_feedback') }}
                </h1>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">
                    {{ __('lms.provide_feedback_description') }}
                </p>
            </div>

            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-200 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if($enrollments->isEmpty())
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <p class="text-gray-700 dark:text-gray-300 text-center">
                        {{ __('lms.no_courses_available_feedback') }}
                    </p>
                </div>
            @else
                <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    @foreach($enrollments as $enrollment)
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                            <div class="p-6">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                    {{ $enrollment->courseOffering?->course?->name ?? __('Course') }}
                                </h3>
                                <p class="text-sm text-gray-600 dark:text-gray-300">
                                    {{ $enrollment->courseOffering->course->code }}
                                </p>
                                <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">
                                    {{ $enrollment->courseOffering?->semester?->name ?? '' }}
                                </p>

                                @isset($existingFeedback[$enrollment->course_offering_id])
                                    @php $feedback = $existingFeedback[$enrollment->course_offering_id]; @endphp
                                    <div class="mt-4">
                                        @if($feedback->is_submitted)
                                            <div class="flex items-center text-green-600 dark:text-green-400 mb-2">
                                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                </svg>
                                                {{ __('lms.feedback_submitted') }}
                                            </div>
                                            <div class="flex items-center">
                                                <span class="text-sm text-gray-700 dark:text-gray-300 mr-2">
                                                    {{ __('lms.rating') }}:
                                                </span>
                                                <div class="flex">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <svg class="w-4 h-4 {{ $i <= $feedback->overall_rating ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}" fill="currentColor" viewBox="0 0 20 20">
                                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                        </svg>
                                                    @endfor
                                                </div>
                                            </div>
                                            <a href="{{ route('student.feedback.show', $feedback) }}" class="mt-3 inline-flex items-center text-sm text-blue-600 dark:text-blue-400 hover:underline">
                                                {{ __('lms.view_feedback') }}
                                            </a>
                                        @else
                                            <div class="flex items-center text-yellow-600 dark:text-yellow-400 mb-2">
                                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                                </svg>
                                                {{ __('lms.draft_saved') }}
                                            </div>
                                            <a href="{{ route('student.feedback.edit', $feedback) }}" class="mt-2 inline-flex items-center px-5 py-2.5 bg-blue-700 hover:bg-blue-800 dark:bg-blue-600 dark:hover:bg-blue-700 text-white text-sm font-bold rounded-md transition shadow-md">
                                                {{ __('lms.continue_feedback') }}
                                            </a>
                                        @endif
                                    </div>
                                @else
                                    <div class="mt-4">
                                        <a href="{{ route('student.feedback.create', ['course_offering_id' => $enrollment->course_offering_id]) }}" class="inline-flex items-center px-5 py-2.5 bg-blue-700 hover:bg-blue-800 dark:bg-blue-600 dark:hover:bg-blue-700 text-white text-sm font-bold rounded-md transition shadow-md">
                                            Give Feedback
                                        </a>
                                    </div>
                                @endisset
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
