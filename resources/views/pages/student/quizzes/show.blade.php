<x-app-layout>
    <x-slot name="title">{{ $assessment->title }} - {{ __('Quiz') }}</x-slot>

    <!-- Hero Section -->
    <div class="mb-8 relative overflow-hidden rounded-2xl bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-500 px-8 py-12 text-white shadow-xl">
        <div class="absolute inset-0 bg-black/10"></div>
        <div class="absolute -right-20 -top-20 h-64 w-64 rounded-full bg-white/10 blur-3xl"></div>
        <div class="absolute -bottom-20 -left-20 h-64 w-64 rounded-full bg-white/10 blur-3xl"></div>

        <div class="relative flex items-center gap-6">
            <div class="flex h-20 w-20 items-center justify-center rounded-2xl bg-white/20 backdrop-blur-sm">
                <flux:icon name="clipboard-document-check" class="h-10 w-10 text-white" />
            </div>
            <div>
                <h1 class="text-3xl font-bold">{{ $assessment->title }}</h1>
                <p class="mt-1 text-lg text-white/80">{{ $assessment->offering?->course?->name ?? '' }}</p>
            </div>
        </div>

        <!-- Decorative circles -->
        <div class="absolute right-8 top-1/2 -translate-y-1/2 hidden lg:block">
            <div class="flex gap-2">
                <div class="h-3 w-3 rounded-full bg-white/30"></div>
                <div class="h-3 w-3 rounded-full bg-white/60"></div>
                <div class="h-3 w-3 rounded-full bg-white"></div>
            </div>
        </div>
    </div>

    <div class="mx-auto max-w-3xl">
        <!-- Instructions Card -->
        <div class="mb-6 rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="border-b border-neutral-100 px-6 py-4 dark:border-neutral-700">
                <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">{{ __('Quiz Instructions') }}</h2>
            </div>
            <div class="p-6">
                @if($assessment->description)
                    <p class="mb-6 text-neutral-600 dark:text-neutral-400">{{ $assessment->description }}</p>
                @endif

                <div class="grid gap-4 sm:grid-cols-2">
                    <!-- Time Limit -->
                    <div class="group relative overflow-hidden rounded-xl border border-neutral-200 bg-gradient-to-br from-neutral-50 to-neutral-100 p-5 transition-all hover:shadow-md dark:border-neutral-700 dark:from-neutral-800 dark:to-neutral-700/50">
                        <div class="flex items-start gap-4">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-indigo-100 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400">
                                <flux:icon name="clock" class="h-6 w-6" />
                            </div>
                            <div>
                                <p class="text-sm font-medium text-neutral-500 dark:text-neutral-400">{{ __('Time Limit') }}</p>
                                <p class="text-xl font-bold text-neutral-900 dark:text-white">
                                    @if($assessment->time_limit_minutes)
                                        {{ $assessment->time_limit_minutes }} {{ __('min') }}
                                    @else
                                        {{ __('Unlimited') }}
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Questions -->
                    <div class="group relative overflow-hidden rounded-xl border border-neutral-200 bg-gradient-to-br from-neutral-50 to-neutral-100 p-5 transition-all hover:shadow-md dark:border-neutral-700 dark:from-neutral-800 dark:to-neutral-700/50">
                        <div class="flex items-start gap-4">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-purple-100 text-purple-600 dark:bg-purple-900/30 dark:text-purple-400">
                                <flux:icon name="question-mark-circle" class="h-6 w-6" />
                            </div>
                            <div>
                                <p class="text-sm font-medium text-neutral-500 dark:text-neutral-400">{{ __('Questions') }}</p>
                                <p class="text-xl font-bold text-neutral-900 dark:text-white">{{ $assessment->questions->count() }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Attempts -->
                    <div class="group relative overflow-hidden rounded-xl border border-neutral-200 bg-gradient-to-br from-neutral-50 to-neutral-100 p-5 transition-all hover:shadow-md dark:border-neutral-700 dark:from-neutral-800 dark:to-neutral-700/50">
                        <div class="flex items-start gap-4">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400">
                                <flux:icon name="arrow-path" class="h-6 w-6" />
                            </div>
                            <div>
                                <p class="text-sm font-medium text-neutral-500 dark:text-neutral-400">{{ __('Attempts') }}</p>
                                <p class="text-xl font-bold text-neutral-900 dark:text-white">
                                    @if($assessment->attempts_allowed)
                                        {{ $assessment->attempts_allowed }}
                                    @else
                                        {{ __('Unlimited') }}
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Passing Score -->
                    <div class="group relative overflow-hidden rounded-xl border border-neutral-200 bg-gradient-to-br from-neutral-50 to-neutral-100 p-5 transition-all hover:shadow-md dark:border-neutral-700 dark:from-neutral-800 dark:to-neutral-700/50">
                        <div class="flex items-start gap-4">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-amber-100 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400">
                                <flux:icon name="check-circle" class="h-6 w-6" />
                            </div>
                            <div>
                                <p class="text-sm font-medium text-neutral-500 dark:text-neutral-400">{{ __('Passing Score') }}</p>
                                <p class="text-xl font-bold text-neutral-900 dark:text-white">
                                    @if($assessment->passing_score)
                                        {{ $assessment->passing_score }}%
                                    @else
                                        {{ __('No minimum') }}
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alerts -->
        @if($attemptCount > 0 || $inProgress)
            <div class="mb-6 space-y-3">
                @if($inProgress)
                    <div class="flex items-center gap-3 rounded-xl border border-blue-200 bg-blue-50 p-4 dark:border-blue-800 dark:bg-blue-900/20">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-blue-100 text-blue-600 dark:bg-blue-900/50 dark:text-blue-400">
                            <flux:icon name="play" class="h-5 w-5" />
                        </div>
                        <div>
                            <p class="font-medium text-blue-700 dark:text-blue-300">{{ __('In Progress') }}</p>
                            <p class="text-sm text-blue-600 dark:text-blue-400">{{ __('You have an incomplete attempt. Continue to finish the quiz.') }}</p>
                        </div>
                    </div>
                @endif

                @if($attemptCount > 0 && !$inProgress)
                    <div class="flex items-center gap-3 rounded-xl border border-amber-200 bg-amber-50 p-4 dark:border-amber-800 dark:bg-amber-900/20">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-amber-100 text-amber-600 dark:bg-amber-900/50 dark:text-amber-400">
                            <flux:icon name="arrow-path" class="h-5 w-5" />
                        </div>
                        <div>
                            <p class="font-medium text-amber-700 dark:text-amber-300">{{ __('Previous Attempt') }}</p>
                            <p class="text-sm text-amber-600 dark:text-amber-400">{{ __('You have :count attempt(s) on this quiz.', ['count' => $attemptCount]) }}</p>
                        </div>
                    </div>
                @endif
            </div>
        @endif

        <!-- Action Buttons -->
        <div class="rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="flex flex-col gap-3 sm:flex-row">
                @if($inProgress)
                    <flux:button href="{{ route('student.quizzes.take', $assessment) }}" variant="filled" class="flex-1 justify-center bg-indigo-600 hover:bg-indigo-700">
                        <flux:icon name="play" class="mr-2 h-5 w-5" />
                        {{ __('Continue Quiz') }}
                    </flux:button>
                @else
                    <form method="POST" action="{{ route('student.quizzes.start', $assessment) }}" class="flex-1">
                        @csrf
                        <x-button.submit loading-text="{{ __('Starting...') }}" class="w-full justify-center">
                            <flux:icon name="{{ $attemptCount > 0 ? 'arrow-path' : 'play' }}" class="mr-2 h-5 w-5" />
                            {{ $attemptCount > 0 ? __('Start New Attempt') : __('Start Quiz') }}
                        </x-button.submit>
                    </form>
                @endif
                <flux:button href="{{ route('student.quizzes.index') }}" variant="outline" class="flex-1 justify-center">
                    <flux:icon name="arrow-left" class="mr-2 h-5 w-5" />
                    {{ __('Back') }}
                </flux:button>
            </div>
        </div>

        <!-- Tips -->
        <div class="mt-6 rounded-xl border border-neutral-200 bg-neutral-50 p-4 dark:border-neutral-700 dark:bg-neutral-800/50">
            <div class="flex items-start gap-3">
                <flux:icon name="light-bulb" class="mt-0.5 h-5 w-5 text-amber-500" />
                <div>
                    <p class="font-medium text-neutral-700 dark:text-neutral-300">{{ __('Tips for Success') }}</p>
                    <ul class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">
                        <li>• {{ __('Read each question carefully before answering') }}</li>
                        <li>• {{ __('Manage your time wisely') }}</li>
                        <li>• {{ __('You can flag questions to review later') }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
