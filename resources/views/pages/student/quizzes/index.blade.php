<x-app-layout>
    <x-slot name="title">{{ __('My Quizzes') }}</x-slot>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-neutral-900 dark:text-white">{{ __('My Quizzes') }}</h1>
        <p class="text-neutral-600 dark:text-neutral-400">{{ __('View and take your assigned quizzes') }}</p>
    </div>

    @if($quizzes->count() > 0)
        <div class="grid gap-4">
            @foreach($quizzes as $quiz)
                <div class="rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center gap-3">
                                <h3 class="text-lg font-semibold text-neutral-900 dark:text-white">{{ $quiz->title }}</h3>
                                @if($quiz->quiz_type === 'quiz')
                                    <flux:badge color="emerald">{{ __('Quiz') }}</flux:badge>
                                @elseif($quiz->quiz_type === 'pre_quiz')
                                    <flux:badge color="blue">{{ __('Pre-Quiz') }}</flux:badge>
                                @elseif($quiz->quiz_type === 'post_quiz')
                                    <flux:badge color="purple">{{ __('Post-Quiz') }}</flux:badge>
                                @endif
                            </div>
                            <p class="mt-1 text-sm text-neutral-600 dark:text-neutral-400">
                                {{ $quiz->offering?->course?->name ?? '' }}
                            </p>
                            <div class="mt-3 flex flex-wrap gap-4 text-sm text-neutral-500 dark:text-neutral-400">
                                @if($quiz->time_limit_minutes)
                                    <span class="flex items-center gap-1">
                                        <flux:icon name="clock" />
                                        {{ $quiz->time_limit_minutes }} {{ __('minutes') }}
                                    </span>
                                @endif
                                @if($quiz->attempts_allowed)
                                    <span class="flex items-center gap-1">
                                        <flux:icon name="arrow-path" />
                                        {{ $quiz->attempts_count ?? 0 }}/{{ $quiz->attempts_allowed }} {{ __('attempts') }}
                                    </span>
                                @else
                                    <span class="flex items-center gap-1">
                                        <flux:icon name="arrow-path" />
                                        {{ $quiz->attempts_count ?? 0 }} {{ __('attempts') }}
                                    </span>
                                @endif
                                @if($quiz->passing_score)
                                    <span class="flex items-center gap-1">
                                        <flux:icon name="check-circle" />
                                        {{ $quiz->passing_score }}% {{ __('to pass') }}
                                    </span>
                                @endif
                                @if($quiz->available_until)
                                    <span class="flex items-center gap-1">
                                        <flux:icon name="calendar" />
                                        {{ __('Due') }}: {{ $quiz->available_until->format('M d, Y H:i') }}
                                    </span>
                                @endif
                            </div>

                            @if($quiz->attempts_count > 0)
                                <div class="mt-3 flex items-center gap-4">
                                    <span class="text-sm text-neutral-600 dark:text-neutral-400">
                                        {{ __('Best Score') }}:
                                        <span class="font-semibold {{ $quiz->best_score >= ($quiz->passing_score ?? 0) ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                            {{ number_format($quiz->best_score, 1) }}%
                                        </span>
                                    </span>
                                </div>
                            @endif
                        </div>
                        <div class="flex items-center gap-2">
                            @if($quiz->can_take)
                                <flux:button href="{{ route('student.quizzes.show', $quiz) }}" variant="filled" class="bg-indigo-600 hover:bg-indigo-700">
                                    @if($quiz->attempts_count > 0)
                                        {{ __('Retake') }}
                                    @else
                                        {{ __('Start') }}
                                    @endif
                                </flux:button>
                            @else
                                <flux:button disabled variant="filled" class="bg-neutral-300 dark:bg-neutral-600">
                                    {{ __('No Attempts Left') }}
                                </flux:button>
                            @endif
                            @if($quiz->attempts_count > 0)
                                <flux:button href="{{ route('student.quizzes.attempts', $quiz) }}" variant="ghost">
                                    {{ __('History') }}
                                </flux:button>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="rounded-xl border border-neutral-200 bg-white p-12 text-center shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <flux:icon name="clipboard-document-list" class="mx-auto h-12 w-12 text-neutral-400" />
            <h3 class="mt-4 text-lg font-medium text-neutral-900 dark:text-white">{{ __('No quizzes available') }}</h3>
            <p class="mt-2 text-neutral-600 dark:text-neutral-400">{{ __('You don\'t have any quizzes assigned yet.') }}</p>
        </div>
    @endif
</x-app-layout>
