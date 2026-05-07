<x-layouts::app :title="__('My Grades')">
    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-neutral-900 dark:text-neutral-100">{{ $offering->course?->name ?? __('Course') }}</h1>
            <p class="mt-1 text-neutral-500 dark:text-neutral-400">{{ __('Section') }} {{ $offering->section_name }} - {{ __('Grades') }}</p>
        </div>
        <flux:button :href="route('student.courses.show', $offering)" variant="ghost">
            <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12" />
            </svg>
            {{ __('Back to Course') }}
        </flux:button>
    </div>

    <!-- Grade Summary Cards with Gradient -->
    <div class="mb-6 grid gap-4 md:grid-cols-3">
        <div class="relative overflow-hidden rounded-2xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="absolute inset-0 bg-gradient-to-br from-violet-50 to-purple-50 dark:from-violet-900/20 dark:to-purple-900/20"></div>
            <div class="relative">
                <p class="text-sm font-medium text-neutral-500 dark:text-neutral-400">{{ __('Current Grade') }}</p>
                <p class="mt-2 text-4xl font-bold text-neutral-900 dark:text-neutral-100">{{ number_format($currentGrade, 1) }}%</p>
                <div class="mt-3 h-2 w-full overflow-hidden rounded-full bg-neutral-100 dark:bg-neutral-700">
                    <div class="h-full rounded-full bg-gradient-to-r from-violet-500 to-purple-500" style="width: {{ $currentGrade }}%"></div>
                </div>
            </div>
        </div>
        <div class="relative overflow-hidden rounded-2xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="absolute inset-0 bg-gradient-to-br from-emerald-50 to-teal-50 dark:from-emerald-900/20 dark:to-teal-900/20"></div>
            <div class="relative">
                <p class="text-sm font-medium text-neutral-500 dark:text-neutral-400">{{ __('Letter Grade') }}</p>
                <p class="mt-2 text-4xl font-bold text-neutral-900 dark:text-neutral-100">{{ $letterGrade }}</p>
                <p class="mt-3 text-sm text-neutral-500 dark:text-neutral-400">{{ __('Based on your performance') }}</p>
            </div>
        </div>
        <div class="relative overflow-hidden rounded-2xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="absolute inset-0 bg-gradient-to-br from-amber-50 to-orange-50 dark:from-amber-900/20 dark:to-orange-900/20"></div>
            <div class="relative">
                <p class="text-sm font-medium text-neutral-500 dark:text-neutral-400">{{ __('GPA Points') }}</p>
                <p class="mt-2 text-4xl font-bold text-neutral-900 dark:text-neutral-100">{{ number_format($gpaPoints, 2) }}</p>
                <p class="mt-3 text-sm text-neutral-500 dark:text-neutral-400">{{ __('Out of 4.0') }}</p>
            </div>
        </div>
    </div>

    <!-- Grade Components -->
    <div class="rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
        <div class="border-b border-neutral-200 px-6 py-4 dark:border-neutral-700">
            <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Grade Breakdown') }}</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-neutral-50 text-neutral-500 dark:bg-neutral-700 dark:text-neutral-400">
                    <tr>
                        <th class="px-6 py-3">{{ __('Assessment') }}</th>
                        <th class="px-6 py-3">{{ __('Type') }}</th>
                        <th class="px-6 py-3">{{ __('Weight') }}</th>
                        <th class="px-6 py-3">{{ __('Score') }}</th>
                        <th class="px-6 py-3">{{ __('Total') }}</th>
                        <th class="px-6 py-3">{{ __('Percentage') }}</th>
                        <th class="px-6 py-3">{{ __('Feedback') }}</th>
                        <th class="px-6 py-3">{{ __('Date') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                    @forelse($grades as $grade)
                    <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-700/50">
                        <td class="px-6 py-4 font-medium text-neutral-900 dark:text-neutral-100">{{ $grade->assessment?->title ?? '-' }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center rounded-full bg-purple-100 px-2.5 py-0.5 text-xs font-medium text-purple-800 dark:bg-purple-900 dark:text-purple-200">
                                {{ __($grade->assessment?->type ?? 'N/A') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-neutral-500 dark:text-neutral-400">
                            {{ $grade->assessment?->weight ? $grade->assessment->weight . '%' : '-' }}
                        </td>
                        <td class="px-6 py-4 text-neutral-900 dark:text-neutral-100">{{ $grade->grade ?? '-' }}</td>
                        <td class="px-6 py-4 text-neutral-500 dark:text-neutral-400">{{ $grade->assessment?->max_grade ?? '-' }}</td>
                        <td class="px-6 py-4">
                            @php $percentage = $grade->percentage; @endphp
                            <span class="font-medium
                                @if($percentage >= 90) text-green-600 dark:text-green-400
                                @elseif($percentage >= 80) text-emerald-600 dark:text-emerald-400
                                @elseif($percentage >= 70) text-amber-600 dark:text-amber-400
                                @elseif($percentage >= 60) text-orange-600 dark:text-orange-400
                                @else text-red-600 dark:text-red-400 @endif">
                                {{ number_format($percentage, 1) }}%
                            </span>
                        </td>
                        <td class="px-6 py-4 text-neutral-500 dark:text-neutral-400">{{ $grade->created_at->format('Y-m-d') }}</td>
                        <td class="px-6 py-4">
                            @if($grade->feedback)
                                <div x-data="{ open: false }">
                                    <button @click="open = !open" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                                        <span x-show="!open">{{ __('View') }}</span>
                                        <span x-show="open" x-cloak>{{ __('Hide') }}</span>
                                    </button>
                                    <div x-show="open" x-cloak class="mt-2 text-sm text-neutral-600 dark:text-neutral-400">
                                        {{ $grade->feedback }}
                                    </div>
                                </div>
                            @else
                                <span class="text-neutral-400 dark:text-neutral-500">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-neutral-100 dark:bg-neutral-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="size-8 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <h3 class="text-lg font-medium text-neutral-900 dark:text-neutral-100">{{ __('No grades available yet') }}</h3>
                                <p class="mt-1 text-neutral-500 dark:text-neutral-400">{{ __('Grades will appear here once your teacher publishes them') }}</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts::app>
