<x-app-layout>
    <x-slot name="title">{{ __('Attempt History') }} - {{ $assessment->title }}</x-slot>

    <div class="mb-6">
        <nav class="flex text-sm text-gray-400">
            <a href="{{ route('student.quizzes.index') }}" class="hover:text-white">{{ __('My Quizzes') }}</a>
            <span class="mx-2">/</span>
            <a href="{{ route('student.quizzes.show', $assessment) }}" class="hover:text-white">{{ $assessment->title }}</a>
            <span class="mx-2">/</span>
            <span class="text-white">{{ __('Attempt History') }}</span>
        </nav>
    </div>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-neutral-900 dark:text-white">{{ __('Attempt History') }}</h1>
        <p class="text-neutral-600 dark:text-neutral-400">{{ $assessment->title }}</p>
    </div>

    @if($attempts->count() > 0)
        <div class="space-y-4">
            @foreach($attempts as $attempt)
                <div class="rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center gap-3">
                                <h3 class="text-lg font-semibold text-neutral-900 dark:text-white">
                                    {{ __('Attempt :number', ['number' => $attempt->attempt_number]) }}
                                </h3>
                                @if($attempt->passed)
                                    <flux:badge color="green">{{ __('Passed') }}</flux:badge>
                                @else
                                    <flux:badge color="red">{{ __('Failed') }}</flux:badge>
                                @endif
                                @if($attempt->status === 'graded')
                                    <flux:badge color="blue">{{ __('Graded') }}</flux:badge>
                                @elseif($attempt->status === 'submitted')
                                    <flux:badge color="yellow">{{ __('Pending Review') }}</flux:badge>
                                @endif
                            </div>
                            <div class="mt-3 grid gap-4 sm:grid-cols-4">
                                <div>
                                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Score') }}</p>
                                    <p class="text-lg font-semibold {{ $attempt->passed ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                        {{ number_format($attempt->percentage, 1) }}%
                                    </p>
                                </div>
                                <div>
                                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Points') }}</p>
                                    <p class="text-lg font-semibold text-neutral-900 dark:text-white">
                                        {{ $attempt->score }}/{{ $attempt->max_score }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Time Spent') }}</p>
                                    <p class="text-lg font-semibold text-neutral-900 dark:text-white">
                                        {{ $attempt->time_spent }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Submitted') }}</p>
                                    <p class="text-lg font-semibold text-neutral-900 dark:text-white">
                                        {{ $attempt->submitted_at?->format('M d, Y H:i') ?? '-' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <flux:button href="{{ route('student.quizzes.result', [$assessment, $attempt]) }}" variant="ghost">
                            {{ __('View Details') }}
                        </flux:button>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="rounded-xl border border-neutral-200 bg-white p-12 text-center shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <flux:icon name="clock" class="mx-auto h-12 w-12 text-neutral-400" />
            <h3 class="mt-4 text-lg font-medium text-neutral-900 dark:text-white">{{ __('No attempts yet') }}</h3>
            <p class="mt-2 text-neutral-600 dark:text-neutral-400">{{ __('You haven\'t attempted this quiz yet.') }}</p>
        </div>
    @endif

    <div class="mt-6">
        <flux:button href="{{ route('student.quizzes.show', $assessment) }}" variant="ghost">
            {{ __('Back to Quiz') }}
        </flux:button>
    </div>
</x-app-layout>
