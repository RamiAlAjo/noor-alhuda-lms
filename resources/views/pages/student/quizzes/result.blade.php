<x-app-layout>
    <x-slot name="title">{{ __('Quiz Results') }} - {{ $assessment->title }}</x-slot>

    <!-- Hero Section -->
    <div class="mb-8 relative overflow-hidden rounded-2xl {{ $attempt->passed ? 'bg-gradient-to-br from-green-500 to-emerald-600' : 'bg-gradient-to-br from-red-500 to-orange-600' }} px-8 py-12 text-white shadow-xl">
        <div class="absolute inset-0 bg-black/10"></div>
        <div class="absolute -right-20 -top-20 h-64 w-64 rounded-full bg-white/10 blur-3xl"></div>
        <div class="absolute -bottom-20 -left-20 h-64 w-64 rounded-full bg-white/10 blur-3xl"></div>

        <div class="relative flex items-center gap-6">
            <div class="flex h-20 w-20 items-center justify-center rounded-2xl bg-white/20 backdrop-blur-sm">
                @if($attempt->passed)
                    <flux:icon name="check-circle" class="h-10 w-10 text-white" />
                @else
                    <flux:icon name="x-circle" class="h-10 w-10 text-white" />
                @endif
            </div>
            <div>
                <h1 class="text-3xl font-bold">
                    @if($attempt->passed)
                        {{ __('Congratulations!') }}
                    @else
                        {{ __('Keep Trying!') }}
                    @endif
                </h1>
                <p class="mt-1 text-lg text-white/80">
                    @if($attempt->passed)
                        {{ __('You passed the quiz!') }}
                    @else
                        {{ __('You didn\'t pass this time.') }}
                    @endif
                </p>
            </div>
        </div>
    </div>

    <div class="mx-auto max-w-3xl">
        <!-- Result Summary Cards -->
        <div class="mb-6 grid gap-4 sm:grid-cols-3">
            <div class="rounded-xl border border-neutral-200 bg-white p-6 text-center shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                <div class="mb-2 flex h-12 w-12 items-center justify-center rounded-lg bg-indigo-100 mx-auto dark:bg-indigo-900/30">
                    <flux:icon name="academic-cap" class="h-6 w-6 text-indigo-600 dark:text-indigo-400" />
                </div>
                <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Score') }}</p>
                <p class="text-2xl font-bold text-neutral-900 dark:text-white">{{ number_format($attempt->percentage, 1) }}%</p>
                <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ $attempt->score }}/{{ $attempt->max_score }} {{ __('pts') }}</p>
            </div>

            <div class="rounded-xl border border-neutral-200 bg-white p-6 text-center shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                <div class="mb-2 flex h-12 w-12 items-center justify-center rounded-lg bg-emerald-100 mx-auto dark:bg-emerald-900/30">
                    <flux:icon name="check-circle" class="h-6 w-6 text-emerald-600 dark:text-emerald-400" />
                </div>
                <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Correct') }}</p>
                <p class="text-2xl font-bold text-neutral-900 dark:text-white">{{ $correctCount }}/{{ $totalQuestions }}</p>
                <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('answers') }}</p>
            </div>

            <div class="rounded-xl border border-neutral-200 bg-white p-6 text-center shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                <div class="mb-2 flex h-12 w-12 items-center justify-center rounded-lg bg-amber-100 mx-auto dark:bg-amber-900/30">
                    <flux:icon name="clock" class="h-6 w-6 text-amber-600 dark:text-amber-400" />
                </div>
                <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Time Spent') }}</p>
                <p class="text-2xl font-bold text-neutral-900 dark:text-white">{{ $attempt->time_spent }}</p>
            </div>
        </div>

        <!-- Progress Bar -->
        @if($assessment->passing_score)
            <div class="mb-6 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                <div class="flex items-center justify-between text-sm text-neutral-500 dark:text-neutral-400 mb-3">
                    <span>{{ __('Progress') }}</span>
                    <span>{{ __('Passing: :score%', ['score' => $assessment->passing_score]) }}</span>
                </div>
                <div class="h-4 w-full rounded-full bg-neutral-200 dark:bg-neutral-700">
                    <div class="h-4 rounded-full {{ $attempt->passed ? 'bg-green-500' : 'bg-red-500' }} transition-all duration-500" style="width: {{ min($attempt->percentage, 100) }}%"></div>
                </div>
                <p class="mt-2 text-center text-sm {{ $attempt->passed ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                    @if($attempt->passed)
                        {{ __('You passed! 🎉') }}
                    @else
                        {{ __('You need :score% to pass. Keep practicing!', ['score' => $assessment->passing_score]) }}
                    @endif
                </p>
            </div>
        @endif

        <!-- Action Buttons -->
        <div class="mb-6 flex flex-col gap-3 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            @if($canRetake)
                <flux:button href="{{ route('student.quizzes.show', $assessment) }}" variant="filled" class="w-full justify-center bg-indigo-600 hover:bg-indigo-700">
                    <flux:icon name="arrow-path" class="mr-2 h-5 w-5" />
                    {{ __('Retake Quiz') }}
                </flux:button>
            @endif
            <flux:button href="{{ route('student.quizzes.index') }}" variant="outline" class="w-full justify-center">
                <flux:icon name="arrow-left" class="mr-2 h-5 w-5" />
                {{ __('Back to Quizzes') }}
            </flux:button>
        </div>

        <!-- Show answers if enabled -->
        @if($assessment->show_correct_answers && $attempt->status === 'graded')
            <div class="rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                <div class="border-b border-neutral-100 px-6 py-4 dark:border-neutral-700">
                    <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">{{ __('Your Answers') }}</h2>
                </div>
                <div class="p-6 space-y-4">
                    @foreach($assessment->questions as $index => $question)
                        @php
                            $userAnswer = $attempt->answers->where('question_id', $question->id)->first();

                            // Handle both string (JSON) and array formats
                            $questionOptions = $question->options;
                            if (is_string($questionOptions)) {
                                $questionOptions = json_decode($questionOptions, true);
                            }
                            // Convert associative array format if needed
                            if (is_array($questionOptions) && !empty($questionOptions) && isset($questionOptions[array_key_first($questionOptions)]) && is_string($questionOptions[array_key_first($questionOptions)])) {
                                $convertedOptions = [];
                                foreach ($questionOptions as $key => $value) {
                                    $convertedOptions[] = [
                                        'id' => $key,
                                        'option_text' => $value,
                                        'is_correct' => false,
                                    ];
                                }
                                $questionOptions = $convertedOptions;
                            }
                        @endphp
                        <div class="rounded-lg border {{ $userAnswer && $userAnswer->is_correct ? 'border-green-200 bg-green-50 dark:border-green-800 dark:bg-green-900/20' : 'border-red-200 bg-red-50 dark:border-red-800 dark:bg-red-900/20' }} p-4">
                            <div class="flex items-start gap-3">
                                <span class="flex h-7 w-7 items-center justify-center rounded-full text-sm font-semibold {{ $userAnswer && $userAnswer->is_correct ? 'bg-green-200 text-green-700 dark:bg-green-800 dark:text-green-300' : 'bg-red-200 text-red-700 dark:bg-red-800 dark:text-red-300' }}">
                                    @if($userAnswer && $userAnswer->is_correct)
                                        <flux:icon name="check" class="h-4 w-4" />
                                    @else
                                        <flux:icon name="x-mark" class="h-4 w-4" />
                                    @endif
                                </span>
                                <div class="flex-1">
                                    <p class="font-medium text-neutral-900 dark:text-white">{!! $question->question_text !!}</p>

                                    @if(in_array($question->question_type, ['multiple_choice', 'true_false']))
                                        <div class="mt-3 space-y-2">
                                            @foreach($questionOptions ?? [] as $option)
                                                @php
                                                    $isUserAnswer = $userAnswer && $userAnswer->option_id == ($option['id'] ?? $loop->index + 1);
                                                    $isCorrect = $option['is_correct'] ?? false;
                                                @endphp
                                                <div class="flex items-center gap-2 rounded-lg border p-3 text-sm @if($isCorrect) border-green-200 bg-green-100 text-green-700 dark:border-green-800 dark:bg-green-900/30 dark:text-green-400 @elseif($isUserAnswer && !$isCorrect) border-red-200 bg-red-100 text-red-700 dark:border-red-800 dark:bg-red-900/30 dark:text-red-400 @else border-neutral-200 bg-neutral-50 text-neutral-500 dark:border-neutral-700 dark:bg-neutral-800/50 dark:text-neutral-400 @endif">
                                                    @if($isCorrect)
                                                        <flux:icon name="check-circle" class="h-4 w-4 shrink-0" />
                                                    @elseif($isUserAnswer && !$isCorrect)
                                                        <flux:icon name="x-circle" class="h-4 w-4 shrink-0" />
                                                    @else
                                                        <span class="h-4 w-4 shrink-0">○</span>
                                                    @endif
                                                    <span>{{ $option['option_text'] ?? $option }}</span>
                                                    @if($isUserAnswer && $isCorrect)
                                                        <span class="ml-auto text-xs font-medium">({{ __('Your answer') }})</span>
                                                    @elseif($isUserAnswer && !$isCorrect)
                                                        <span class="ml-auto text-xs font-medium">({{ __('Your answer') }})</span>
                                                    @elseif($isCorrect)
                                                        <span class="ml-auto text-xs font-medium">({{ __('Correct') }})</span>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="mt-3 rounded-lg border border-neutral-200 bg-neutral-50 p-3 dark:border-neutral-700 dark:bg-neutral-800/50">
                                            <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('Your answer:') }}</p>
                                            <p class="text-neutral-900 dark:text-white">{{ $userAnswer->text_answer ?? '-' }}</p>
                                        </div>
                                        @if($question->correct_answer)
                                            <div class="mt-2 rounded-lg border border-green-200 bg-green-50 p-3 dark:border-green-800 dark:bg-green-900/20">
                                                <p class="text-xs text-green-600 dark:text-green-400">{{ __('Correct answer:') }}</p>
                                                <p class="text-green-700 dark:text-green-300">{{ $question->correct_answer }}</p>
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
