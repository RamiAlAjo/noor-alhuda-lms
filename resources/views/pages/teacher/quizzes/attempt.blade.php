<x-app-layout>
    <x-slot name="title">{{ __('Grade Attempt') }} - {{ $quiz->title }}</x-slot>

    <div class="mb-6">
        <nav class="flex text-sm text-gray-400">
            <a href="{{ route('teacher.courses.index') }}" class="hover:text-white">{{ __('Courses') }}</a>
            <span class="mx-2">/</span>
            <a href="{{ route('teacher.courses.show', $offering) }}" class="hover:text-white">{{ $offering->course?->name ?? __('Course') }}</a>
            <span class="mx-2">/</span>
            <a href="{{ route('teacher.quizzes.index', $offering) }}" class="hover:text-white">{{ __('Quizzes') }}</a>
            <span class="mx-2">/</span>
            <a href="{{ route('teacher.quizzes.analytics', [$offering, $quiz]) }}" class="hover:text-white">{{ $quiz->title }}</a>
            <span class="mx-2">/</span>
            <span class="text-white">{{ __('Grade Attempt') }}</span>
        </nav>
    </div>

    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-neutral-900 dark:text-white">{{ __('Grade Attempt') }}</h1>
            <p class="text-neutral-600 dark:text-neutral-400">{{ $attempt->student?->name ?? __('Unknown Student') }} - {{ __('Attempt :number', ['number' => $attempt->attempt_number]) }}</p>
        </div>
        <flux:button href="{{ route('teacher.quizzes.analytics', [$offering, $quiz]) }}" variant="ghost">
            {{ __('Back to Analytics') }}
        </flux:button>
    </div>

    <!-- Student Info -->
    <div class="mb-6 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
        <div class="flex items-center gap-4">
            <div class="h-12 w-12 rounded-full bg-indigo-100 flex items-center justify-center dark:bg-indigo-900">
                <span class="text-lg font-semibold text-indigo-600 dark:text-indigo-300">{{ $attempt->student->initials }}</span>
            </div>
            <div>
                <h3 class="font-semibold text-neutral-900 dark:text-white">{{ $attempt->student?->name ?? __('Unknown Student') }}</h3>
                <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ $attempt->student?->email ?? '' }}</p>
            </div>
            <div class="ml-auto text-right">
                <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Submitted') }}</p>
                <p class="font-medium text-neutral-900 dark:text-white">{{ $attempt->submitted_at?->format('M d, Y H:i') ?? '-' }}</p>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('teacher.quizzes.attempts.grade', [$offering, $quiz, $attempt]) }}" class="space-y-6">
        @csrf

        <div class="space-y-4">
            @foreach($quiz->questions as $index => $question)
                @php
                    $answer = $attempt->answers->where('question_id', $question->id)->first();
                @endphp
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
                                <div class="space-y-2 mb-4">
                                    @foreach($question->options as $option)
                                        <div class="flex items-center gap-2 rounded-lg p-2 @if($option['is_correct']) bg-green-50 dark:bg-green-900/20 @endif @if($answer && $answer->option_id == ($option['id'] ?? $loop->index + 1)) border-2 border-indigo-500 @endif">
                                            @if($option['is_correct'])
                                                <flux:icon name="check-circle" class="h-5 w-5 text-green-500" />
                                            @else
                                                <flux:icon name="circle" class="h-5 w-5 text-neutral-400" />
                                            @endif
                                            <span class="{{ $option['is_correct'] ? 'font-medium text-green-700 dark:text-green-300' : 'text-neutral-600 dark:text-neutral-300' }}">
                                                {{ $option['option_text'] }}
                                            </span>
                                            @if($answer && $answer->option_id == ($option['id'] ?? $loop->index + 1))
                                                <flux:badge color="indigo" class="ml-auto">{{ __('Student Answer') }}</flux:badge>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="mb-4">
                                    <p class="text-sm text-neutral-500 dark:text-neutral-400 mb-2">{{ __('Student Answer:') }}</p>
                                    <div class="rounded-lg bg-neutral-50 p-4 dark:bg-neutral-700/50">
                                        <p class="text-neutral-900 dark:text-white">{{ $answer->text_answer ?? '-' }}</p>
                                    </div>
                                </div>
                                @if($question->correct_answer)
                                    <div class="mb-4">
                                        <p class="text-sm text-neutral-500 dark:text-neutral-400 mb-2">{{ __('Model Answer:') }}</p>
                                        <div class="rounded-lg bg-blue-50 p-4 dark:bg-blue-900/20">
                                            <p class="text-blue-700 dark:text-blue-300">{{ $question->correct_answer }}</p>
                                        </div>
                                    </div>
                                @endif
                            @endif

                            <div class="grid gap-4 sm:grid-cols-2">
                                <flux:field>
                                    <flux:label>{{ __('Points Earned') }}</flux:label>
                                    <flux:input type="number" name="grades[{{ $question->id }}][points_earned]" value="{{ $answer->points_earned ?? 0 }}" min="0" max="{{ $question->points }}" step="0.5" />
                                </flux:field>

                                <flux:field>
                                    <flux:label>{{ __('Feedback') }}</flux:label>
                                    <flux:input type="text" name="grades[{{ $question->id }}][feedback]" value="{{ $answer->feedback ?? '' }}" placeholder="{{ __('Optional feedback') }}" />
                                </flux:field>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Overall Feedback -->
        <div class="rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <flux:field>
                <flux:label>{{ __('Overall Feedback') }}</flux:label>
                <flux:textarea name="overall_feedback" rows="3" placeholder="{{ __('Provide overall feedback for this attempt...') }}">{{ $attempt->feedback }}</flux:textarea>
            </flux:field>
        </div>

        <div class="flex gap-3">
            <x-button.submit loading-text="Saving..." variant="primary">
                Save Grades
            </x-button.submit>
            <flux:button href="{{ route('teacher.quizzes.analytics', [$offering, $quiz]) }}" variant="ghost">
                {{ __('Cancel') }}
            </flux:button>
        </div>
    </form>
</x-app-layout>

