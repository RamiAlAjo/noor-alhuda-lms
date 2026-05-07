<x-app-layout>
    <x-slot name="title">{{ __('Analytics') }} - {{ $quiz->title }}</x-slot>

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
            <span class="text-white">{{ __('Analytics') }}</span>
        </nav>
    </div>

    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-neutral-900 dark:text-white">{{ __('Quiz Analytics') }}</h1>
            <p class="text-neutral-600 dark:text-neutral-400">{{ $quiz->title }}</p>
        </div>
        <flux:button href="{{ route('teacher.quizzes.index', $offering) }}" variant="ghost">
            {{ __('Back to Quizzes') }}
        </flux:button>
    </div>

    <!-- Overview Stats -->
    <div class="mb-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
        <div class="rounded-xl border border-neutral-200 bg-white p-4 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-900">
                    <flux:icon name="clipboard-document-check" class="h-5 w-5 text-blue-600 dark:text-blue-400" />
                </div>
                <div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Total Attempts') }}</p>
                    <p class="text-xl font-semibold text-neutral-900 dark:text-white">{{ $stats['total_attempts'] }}</p>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-neutral-200 bg-white p-4 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-green-100 dark:bg-green-900">
                    <flux:icon name="users" class="h-5 w-5 text-green-600 dark:text-green-400" />
                </div>
                <div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Unique Students') }}</p>
                    <p class="text-xl font-semibold text-neutral-900 dark:text-white">{{ $stats['unique_students'] }}</p>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-neutral-200 bg-white p-4 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-100 dark:bg-indigo-900">
                    <flux:icon name="chart-bar" class="h-5 w-5 text-indigo-600 dark:text-indigo-400" />
                </div>
                <div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Average Score') }}</p>
                    <p class="text-xl font-semibold text-neutral-900 dark:text-white">{{ number_format($stats['average_score'], 1) }}%</p>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-neutral-200 bg-white p-4 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-purple-100 dark:bg-purple-900">
                    <flux:icon name="check-circle" class="h-5 w-5 text-purple-600 dark:text-purple-400" />
                </div>
                <div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Pass Rate') }}</p>
                    <p class="text-xl font-semibold text-neutral-900 dark:text-white">{{ number_format($stats['pass_rate'], 1) }}%</p>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-neutral-200 bg-white p-4 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-amber-100 dark:bg-amber-900">
                    <flux:icon name="arrow-trending-up" class="h-5 w-5 text-amber-600 dark:text-amber-400" />
                </div>
                <div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Highest Score') }}</p>
                    <p class="text-xl font-semibold text-neutral-900 dark:text-white">{{ number_format($stats['highest_score'], 1) }}%</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <!-- Score Distribution -->
        <div class="rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <h2 class="mb-4 text-lg font-semibold text-neutral-900 dark:text-white">{{ __('Score Distribution') }}</h2>
            <div class="space-y-3">
                @php
                    $maxCount = max($scoreDistribution);
                    $maxCount = $maxCount > 0 ? $maxCount : 1;
                @endphp
                @foreach(['A' => 'green', 'B' => 'blue', 'C' => 'yellow', 'D' => 'orange', 'F' => 'red'] as $grade => $color)
                    <div class="flex items-center gap-3">
                        <span class="w-8 text-sm font-medium text-neutral-900 dark:text-white">{{ $grade }}</span>
                        <div class="flex-1">
                            <div class="h-6 w-full rounded-full bg-neutral-100 dark:bg-neutral-700">
                                <div class="h-6 rounded-full bg-{{ $color }}-500" style="width: {{ ($scoreDistribution[$grade] / $maxCount) * 100 }}%"></div>
                            </div>
                        </div>
                        <span class="w-12 text-sm text-neutral-500 dark:text-neutral-400 text-right">{{ $scoreDistribution[$grade] }}</span>
                    </div>
                @endforeach
            </div>
            <div class="mt-4 flex justify-between text-xs text-neutral-500 dark:text-neutral-400">
                <span>A: 90-100%</span>
                <span>B: 80-89%</span>
                <span>C: 70-79%</span>
                <span>D: 60-69%</span>
                <span>F: <60%</span>
            </div>
        </div>

        <!-- Question Analysis -->
        <div class="rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <h2 class="mb-4 text-lg font-semibold text-neutral-900 dark:text-white">{{ __('Question Analysis') }}</h2>
            <div class="space-y-3 max-h-80 overflow-y-auto">
                @foreach($questionAnalysis as $analysis)
                    <div class="flex items-center gap-3 rounded-lg bg-neutral-50 p-3 dark:bg-neutral-700/50">
                        <span class="flex h-8 w-8 items-center justify-center rounded-full bg-indigo-100 text-sm font-semibold text-indigo-600 dark:bg-indigo-900 dark:text-indigo-300">
                            {{ $loop->iteration }}
                        </span>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-neutral-900 dark:text-white truncate">
                                {{ Str::limit(strip_tags($analysis['question']->question_text), 50) }}
                            </p>
                            <div class="mt-1 flex items-center gap-2">
                                <div class="flex-1 h-2 rounded-full bg-neutral-200 dark:bg-neutral-600">
                                    <div class="h-2 rounded-full {{ $analysis['accuracy'] >= 70 ? 'bg-green-500' : ($analysis['accuracy'] >= 50 ? 'bg-yellow-500' : 'bg-red-500') }}" style="width: {{ $analysis['accuracy'] }}%"></div>
                                </div>
                                <span class="text-xs text-neutral-500 dark:text-neutral-400">{{ $analysis['accuracy'] }}%</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Student Results -->
    <div class="mt-6 rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
        <div class="border-b border-neutral-200 p-6 dark:border-neutral-700">
            <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">{{ __('Student Results') }}</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-neutral-50 dark:bg-neutral-700/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">{{ __('Student') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">{{ __('Attempt') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">{{ __('Score') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">{{ __('Time Spent') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">{{ __('Status') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">{{ __('Submitted') }}</th>
                        <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                    @forelse($results as $attempt)
                        <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-700/30">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center dark:bg-indigo-900">
                                        <span class="text-sm font-medium text-indigo-600 dark:text-indigo-300">
                                            {{ $attempt->student->initials }}
                                        </span>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-neutral-900 dark:text-white">{{ $attempt->student?->name ?? __('Unknown Student') }}</p>
                                        <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ $attempt->student?->email ?? '' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-900 dark:text-white">
                                {{ $attempt->attempt_number }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-medium {{ $attempt->passed ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                        {{ number_format($attempt->percentage, 1) }}%
                                    </span>
                                    <span class="text-xs text-neutral-500 dark:text-neutral-400">
                                        ({{ $attempt->score }}/{{ $attempt->max_score }})
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-500 dark:text-neutral-400">
                                {{ $attempt->time_spent }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($attempt->status === 'graded')
                                    <flux:badge color="green">{{ __('Graded') }}</flux:badge>
                                @elseif($attempt->status === 'submitted')
                                    <flux:badge color="yellow">{{ __('Pending Review') }}</flux:badge>
                                @else
                                    <flux:badge color="blue">{{ __('In Progress') }}</flux:badge>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-500 dark:text-neutral-400">
                                {{ $attempt->submitted_at?->diffForHumans() ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <flux:button href="{{ route('teacher.quizzes.attempts.show', [$offering, $quiz, $attempt]) }}" variant="ghost" size="sm">
                                    {{ __('View') }}
                                </flux:button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-neutral-500 dark:text-neutral-400">
                                {{ __('No attempts yet') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-neutral-200 p-4 dark:border-neutral-700">
            {{ $results->links() }}
        </div>
    </div>
</x-app-layout>
