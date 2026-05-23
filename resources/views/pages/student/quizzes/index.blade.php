<x-layouts::app :title="__('My Quizzes')">
    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-neutral-900 dark:text-neutral-100">{{ __('My Quizzes') }}</h1>
            <p class="mt-1 text-neutral-500 dark:text-neutral-400">{{ __('View and take your assigned quizzes') }}</p>
        </div>
    </div>

    <!-- Progress Stats -->
    <div class="mb-6 grid gap-4 md:grid-cols-4">
        <div class="rounded-xl border border-neutral-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100 text-blue-600 dark:bg-blue-900 dark:text-blue-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Total Quizzes') }}</p>
                    <p class="text-xl font-bold text-neutral-900 dark:text-neutral-100">{{ $quizzes->count() }}</p>
                </div>
            </div>
        </div>
        <div class="rounded-xl border border-neutral-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-green-100 text-green-600 dark:bg-green-900 dark:text-green-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Completed') }}</p>
                    <p class="text-xl font-bold text-neutral-900 dark:text-neutral-100">{{ $quizzes->where('attempts_count', '>', 0)->count() }}</p>
                </div>
            </div>
        </div>
        <div class="rounded-xl border border-neutral-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-yellow-100 text-yellow-600 dark:bg-yellow-900 dark:text-yellow-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Pending') }}</p>
                    <p class="text-xl font-bold text-neutral-900 dark:text-neutral-100">{{ $quizzes->where('attempts_count', 0)->count() }}</p>
                </div>
            </div>
        </div>
        <div class="rounded-xl border border-neutral-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-purple-100 text-purple-600 dark:bg-purple-900 dark:text-purple-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6-6" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Average Score') }}</p>
                    <p class="text-xl font-bold text-neutral-900 dark:text-neutral-100">
                        @php
                            $avgScore = $quizzes->where('attempts_count', '>', 0)->avg('best_score') ?? 0;
                        @endphp
                        {{ number_format($avgScore, 1) }}%
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="mb-6 rounded-xl border border-neutral-200 bg-white p-4 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
        <form method="GET" class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div class="flex flex-1 gap-4">
                <!-- Search -->
                <div class="relative flex-1 max-w-md">
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="{{ __('Search quizzes...') }}"
                        class="w-full rounded-lg border border-neutral-300 bg-white px-4 py-2 pl-10 text-neutral-900 focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500 dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-100"
                    >
                    <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-2.5 size-5 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>

                <!-- Status Filter -->
                <select
                    name="status"
                    class="rounded-lg border border-neutral-300 bg-white px-4 py-2 text-neutral-900 focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500 dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-100"
                >
                    <option value="">{{ __('All Quizzes') }}</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>{{ __('Not Started') }}</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>{{ __('Completed') }}</option>
                    <option value="overdue" {{ request('status') === 'overdue' ? 'selected' : '' }}>{{ __('Overdue') }}</option>
                </select>
            </div>

            <div class="flex gap-2">
                <x-button.submit loading-text="Applying..." variant="primary">
                    Filter
                </x-button.submit>

                @if(request()->anyFilled(['search', 'status']))
                <flux:button :href="route('student.quizzes.index')" variant="ghost">
                    {{ __('Clear') }}
                </flux:button>
                @endif
            </div>
        </form>
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
