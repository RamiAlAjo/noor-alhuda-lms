<x-app-layout>
    <x-slot name="title">{{ __('Preview') }} - {{ $quiz->title }}</x-slot>

    <div class="mb-6">
        <nav class="flex text-sm text-gray-400">
            <a href="{{ route('teacher.courses.index') }}" class="hover:text-white">{{ __('Courses') }}</a>
            <span class="mx-2">/</span>
            <a href="{{ route('teacher.courses.show', $offering) }}" class="hover:text-white">{{ $offering->course?->name ?? __('Course') }}</a>
            <span class="mx-2">/</span>
            <a href="{{ route('teacher.quizzes.index', $offering) }}" class="hover:text-white">{{ __('Quizzes') }}</a>
            <span class="mx-2">/</span>
            <span class="text-white">{{ __('Preview') }}</span>
        </nav>
    </div>

    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-neutral-900 dark:text-white">{{ __('Quiz Preview') }}</h1>
            <p class="text-neutral-600 dark:text-neutral-400">{{ $quiz->title }}</p>
        </div>
        <flux:button href="{{ route('teacher.quizzes.questions', [$offering, $quiz]) }}" variant="ghost">
            {{ __('Back to Questions') }}
        </flux:button>
    </div>

    <!-- Quiz Info -->
    <div class="mb-6 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
        <div class="flex flex-wrap gap-6">
            @if($quiz->time_limit_minutes)
                <div class="flex items-center gap-2">
                    <flux:icon name="clock" class="h-5 w-5 text-indigo-500" />
                    <span>{{ $quiz->time_limit_minutes }} {{ __('minutes') }}</span>
                </div>
            @endif
            <div class="flex items-center gap-2">
                <flux:icon name="question-mark-circle" class="h-5 w-5 text-indigo-500" />
                <span>{{ $quiz->questions->count() }} {{ __('questions') }}</span>
            </div>
            @if($quiz->passing_score)
                <div class="flex items-center gap-2">
                    <flux:icon name="check-circle" class="h-5 w-5 text-indigo-500" />
                    <span>{{ $quiz->passing_score }}% {{ __('to pass') }}</span>
                </div>
            @endif
            <div class="flex items-center gap-2">
                <flux:icon name="arrow-path" class="h-5 w-5 text-indigo-500" />
                <span>{{ $quiz->shuffle_questions ? __('Questions shuffled') : __('Questions in order') }}</span>
            </div>
        </div>
    </div>

    <!-- Questions Preview -->
    <div class="space-y-4">
        @foreach($quiz->questions as $index => $question)
            <div class="rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                <div class="flex items-start gap-4">
                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-indigo-100 text-sm font-semibold text-indigo-600 dark:bg-indigo-900 dark:text-indigo-300">
                        {{ $index + 1 }}
                    </span>
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-2">
                            <flux:badge>
                                @if($question->question_type === 'multiple_choice')
                                    {{ __('Multiple Choice') }}
                                @elseif($question->question_type === 'true_false')
                                    {{ __('True/False') }}
                                @elseif($question->question_type === 'short_answer')
                                    {{ __('Short Answer') }}
                                @else
                                    {{ __('Essay') }}
                                @endif
                            </flux:badge>
                            <span class="text-sm text-neutral-500 dark:text-neutral-400">{{ $question->points }} {{ __('points') }}</span>
                        </div>

                        <div class="prose prose-sm dark:prose-invert max-w-none mb-4">
                            {!! $question->question_text !!}
                        </div>

                        @if(in_array($question->question_type, ['multiple_choice', 'true_false']) && $question->options)
                            <div class="space-y-2">
                                @foreach($question->options as $option)
                                    <div class="flex items-center gap-2 rounded-lg border border-neutral-200 p-3 dark:border-neutral-700 @if($option['is_correct']) bg-green-50 dark:bg-green-900/20 border-green-300 dark:border-green-700 @endif">
                                        <input type="{{ $question->question_type === 'true_false' ? 'radio' : 'radio' }}" disabled @if($option['is_correct']) checked @endif class="h-4 w-4 text-indigo-600">
                                        <span class="{{ $option['is_correct'] ? 'font-medium text-green-700 dark:text-green-300' : 'text-neutral-600 dark:text-neutral-300' }}">
                                            {{ $option['option_text'] }}
                                        </span>
                                        @if($option['is_correct'])
                                            <flux:badge color="green" class="ml-auto">{{ __('Correct') }}</flux:badge>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            @if($question->correct_answer)
                                <div class="rounded-lg bg-blue-50 p-4 dark:bg-blue-900/20">
                                    <p class="text-sm text-blue-700 dark:text-blue-300">
                                        <strong>{{ __('Model Answer:') }}</strong> {{ $question->correct_answer }}
                                    </p>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-6">
        <flux:button href="{{ route('teacher.quizzes.questions', [$offering, $quiz]) }}" variant="primary">
            {{ __('Back to Questions') }}
        </flux:button>
    </div>
</x-app-layout>
