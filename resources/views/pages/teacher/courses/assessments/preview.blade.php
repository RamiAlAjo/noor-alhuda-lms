<x-layouts::app :title="__('Preview Quiz')">
    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-neutral-900 dark:text-neutral-100">{{ __('Preview Quiz') }}</h1>
            <p class="mt-1 text-neutral-500 dark:text-neutral-400">
                {{ $assessment->title }} - {{ $section->course?->name ?? __('Course') }}
            </p>
        </div>
        <flux:button :href="route('teacher.courses.assessments', $section)" variant="ghost">
            <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12" />
            </svg>
            {{ __('Back to Assessments') }}
        </flux:button>
    </div>

    <!-- Quiz Info -->
    <div class="mb-6 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Quiz Settings') }}</h2>
                <div class="mt-2 flex flex-wrap gap-3">
                    @if($assessment->time_limit_minutes > 0)
                        <span class="inline-flex items-center rounded-full bg-blue-100 px-3 py-1 text-xs font-medium text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ $assessment->time_limit_minutes }} {{ __('minutes') }}
                        </span>
                    @else
                        <span class="inline-flex items-center rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                            {{ __('No time limit') }}
                        </span>
                    @endif
                    @if($assessment->attempts_allowed)
                        <span class="inline-flex items-center rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-800 dark:bg-green-900 dark:text-green-200">
                            {{ $assessment->attempts_allowed }} {{ __('attempts') }}
                        </span>
                    @else
                        <span class="inline-flex items-center rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                            {{ __('Unlimited attempts') }}
                        </span>
                    @endif
                    <span class="inline-flex items-center rounded-full bg-purple-100 px-3 py-1 text-xs font-medium text-purple-800 dark:bg-purple-900 dark:text-purple-200">
                        {{ $assessment->questions->count() }} {{ __('questions') }}
                    </span>
                    <span class="inline-flex items-center rounded-full bg-indigo-100 px-3 py-1 text-xs font-medium text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200">
                        {{ $assessment->questions->sum('points') }} {{ __('total points') }}
                    </span>
                    @if($assessment->passing_score)
                        <span class="inline-flex items-center rounded-full bg-yellow-100 px-3 py-1 text-xs font-medium text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                            {{ __('Passing') }}: {{ $assessment->passing_score }}%
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Preview Notice -->
    <div class="mb-6 rounded-lg bg-yellow-50 border border-yellow-200 p-4 dark:bg-yellow-900/20 dark:border-yellow-800">
        <div class="flex">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-800 dark:text-yellow-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div class="ml-3">
                <p class="text-sm text-yellow-800 dark:text-yellow-200">
                    {{ __('This is a preview of how the quiz will appear to students. No answers will be recorded.') }}
                </p>
            </div>
        </div>
    </div>

    <!-- Questions Preview -->
    <div class="space-y-6">
        @forelse($assessment->questions as $qIndex => $question)
            <div class="rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                <div class="border-b border-neutral-200 px-6 py-4 dark:border-neutral-700">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-indigo-100 text-indigo-600 dark:bg-indigo-900 dark:text-indigo-400">
                                <span class="text-sm font-semibold">{{ $qIndex + 1 }}</span>
                            </span>
                            <span class="text-sm font-medium text-neutral-500 dark:text-neutral-400">
                                {{ $question->points }} {{ __('points') }}
                            </span>
                        </div>
                        <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium
                            @if($question->question_type === 'multiple_choice') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                            @elseif($question->question_type === 'true_false') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                            @elseif($question->question_type === 'short_answer') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                            @else bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200
                            @endif">
                            @if($question->question_type === 'multiple_choice')
                                {{ __('Multiple Choice') }}
                            @elseif($question->question_type === 'true_false')
                                {{ __('True/False') }}
                            @elseif($question->question_type === 'short_answer')
                                {{ __('Short Answer') }}
                            @else
                                {{ __('Essay') }}
                            @endif
                        </span>
                    </div>
                </div>
                
                <div class="p-6">
                    <!-- Question -->
                    <div class="mb-4">
                        <h3 class="text-lg font-medium text-neutral-900 dark:text-neutral-100">{!! $question->question_text !!}</h3>
                    </div>
                    
                    <!-- Options / Answer Area -->
                    @if(in_array($question->question_type, ['multiple_choice', 'true_false']))
                        <div class="space-y-3">
                            @foreach($question->options as $option)
                                <div class="flex items-center gap-3 rounded-lg border border-neutral-200 p-4 dark:border-neutral-700">
                                    <div class="flex h-5 w-5 items-center justify-center rounded-full border border-neutral-300 dark:border-neutral-600">
                                    </div>
                                    <span class="text-neutral-700 dark:text-neutral-300">{!! $option['option_text'] ?? '' !!}</span>
                                    @if(isset($option['is_correct']) && $option['is_correct'])
                                        <span class="ml-auto inline-flex items-center rounded-full bg-green-100 px-2 py-1 text-xs font-medium text-green-800 dark:bg-green-900 dark:text-green-200">
                                            {{ __('Correct Answer') }}
                                        </span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @elseif($question->question_type === 'short_answer')
                        <div class="rounded-lg border border-neutral-200 bg-neutral-50 p-4 dark:border-neutral-700 dark:bg-neutral-700">
                            <textarea 
                                disabled
                                class="w-full resize-none rounded-lg border-neutral-300 bg-white p-3 text-neutral-500 dark:border-neutral-600 dark:bg-neutral-800 dark:text-neutral-400"
                                rows="3"
                                placeholder="{{ __('Student will type their short answer here...') }}"
                            ></textarea>
                        </div>
                        @if($question->correct_answer)
                            <div class="mt-3 rounded-lg bg-blue-50 p-3 dark:bg-blue-900/20">
                                <p class="text-sm font-medium text-blue-800 dark:text-blue-200">{{ __('Model Answer') }}:</p>
                                <p class="text-neutral-700 dark:text-neutral-300">{{ $question->correct_answer }}</p>
                            </div>
                        @endif
                    @elseif($question->question_type === 'essay')
                        <div class="rounded-lg border border-neutral-200 bg-neutral-50 p-4 dark:border-neutral-700 dark:bg-neutral-700">
                            <textarea 
                                disabled
                                class="w-full resize-none rounded-lg border-neutral-300 bg-white p-3 text-neutral-500 dark:border-neutral-600 dark:bg-neutral-800 dark:text-neutral-400"
                                rows="6"
                                placeholder="{{ __('Student will write their essay here...') }}"
                            ></textarea>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="rounded-xl border border-neutral-200 bg-white p-12 text-center shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h3 class="mt-4 text-lg font-medium text-neutral-900 dark:text-neutral-100">{{ __('No Questions Yet') }}</h3>
                <p class="mt-2 text-neutral-500 dark:text-neutral-400">{{ __('Add questions to this quiz to preview them here.') }}</p>
                <flux:button :href="route('teacher.courses.assessments.questions', [$section, $assessment])" variant="primary" class="mt-4">
                    {{ __('Add Questions') }}
                </flux:button>
            </div>
        @endforelse
    </div>
</x-layouts::app>
