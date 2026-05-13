<x-layouts::app :title="__('Grades')">
    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-neutral-900 dark:text-neutral-100">{{ __('Grades') }}</h1>
            <p class="mt-1 text-neutral-500 dark:text-neutral-400">{{ $section->course?->name ?? __('Course') }} - {{ __('Section') }} {{ $section->section_name }}</p>
        </div>
        <flux:button :href="route('teacher.courses.show', $section)" variant="ghost">
            <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12" />
            </svg>
            {{ __('Back to Course') }}
        </flux:button>
    </div>

    <!-- Grade Summary -->
    <div class="mb-6 grid gap-4 md:grid-cols-4">
        <div class="group relative overflow-hidden rounded-xl border border-neutral-200 bg-white p-5 shadow-sm transition-all hover:shadow-md dark:border-neutral-700 dark:bg-neutral-800">
            <div class="absolute inset-0 bg-gradient-to-br from-indigo-50 to-purple-50 opacity-0 transition-opacity group-hover:opacity-100 dark:from-indigo-900/20 dark:to-purple-900/20"></div>
            <div class="relative flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-100 text-indigo-600 dark:bg-indigo-900 dark:text-indigo-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Total Students') }}</p>
                    <p class="text-xl font-bold text-neutral-900 dark:text-neutral-100">
                        {{ $section->enrollments->where('status', 'approved')->count() }}
                    </p>
                </div>
            </div>
        </div>
        <div class="group relative overflow-hidden rounded-xl border border-neutral-200 bg-white p-5 shadow-sm transition-all hover:shadow-md dark:border-neutral-700 dark:bg-neutral-800">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-50 to-indigo-50 opacity-0 transition-opacity group-hover:opacity-100 dark:from-blue-900/20 dark:to-indigo-900/20"></div>
            <div class="relative flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100 text-blue-600 dark:bg-blue-900 dark:text-blue-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Assessments') }}</p>
                    <p class="text-xl font-bold text-neutral-900 dark:text-neutral-100">
                        {{ $section->assessments->count() }}
                    </p>
                </div>
            </div>
        </div>
        <div class="group relative overflow-hidden rounded-xl border border-neutral-200 bg-white p-5 shadow-sm transition-all hover:shadow-md dark:border-neutral-700 dark:bg-neutral-800">
            <div class="absolute inset-0 bg-gradient-to-br from-green-50 to-teal-50 opacity-0 transition-opacity group-hover:opacity-100 dark:from-green-900/20 dark:to-teal-900/20"></div>
            <div class="relative flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-green-100 text-green-600 dark:bg-green-900 dark:text-green-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Average Grade') }}</p>
                    <p class="text-xl font-bold text-neutral-900 dark:text-neutral-100">{{ $averageGrade ? $averageGrade . '%' : '--' }}</p>
                </div>
            </div>
        </div>
        <div class="group relative overflow-hidden rounded-xl border border-neutral-200 bg-white p-5 shadow-sm transition-all hover:shadow-md dark:border-neutral-700 dark:bg-neutral-800">
            <div class="absolute inset-0 bg-gradient-to-br from-amber-50 to-orange-50 opacity-0 transition-opacity group-hover:opacity-100 dark:from-amber-900/20 dark:to-orange-900/20"></div>
            <div class="relative flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-amber-100 text-amber-600 dark:bg-amber-900 dark:text-amber-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Pass Rate') }}</p>
                    <p class="text-xl font-bold text-neutral-900 dark:text-neutral-100">{{ $passRate ? $passRate . '%' : '--' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Grade Analytics -->
    <div class="mb-6 grid gap-6 lg:grid-cols-2">
        <!-- Grade Distribution -->
        <div class="rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="border-b border-neutral-200 px-6 py-4 dark:border-neutral-700">
                <h3 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Grade Distribution') }}</h3>
            </div>
            <div class="p-6">
                <div class="space-y-3">
                    @php
                        $gradeRanges = [
                            'A' => ['min' => 90, 'count' => 0, 'color' => 'bg-green-500'],
                            'B' => ['min' => 80, 'count' => 0, 'color' => 'bg-blue-500'],
                            'C' => ['min' => 70, 'count' => 0, 'color' => 'bg-yellow-500'],
                            'D' => ['min' => 60, 'count' => 0, 'color' => 'bg-orange-500'],
                            'F' => ['min' => 0, 'count' => 0, 'color' => 'bg-red-500'],
                        ];

                        foreach ($enrollments as $enrollment) {
                            $avgGrade = $enrollment->grades->avg('percentage') ?? 0;
                            foreach ($gradeRanges as $letter => $range) {
                                if ($avgGrade >= $range['min']) {
                                    $gradeRanges[$letter]['count']++;
                                    break;
                                }
                            }
                        }

                        $maxCount = max(array_column($gradeRanges, 'count')) ?: 1;
                    @endphp

                    @foreach($gradeRanges as $letter => $data)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="w-8 text-center font-semibold text-neutral-900 dark:text-neutral-100">{{ $letter }}</span>
                            <div class="flex-1 bg-neutral-200 rounded-full h-2 dark:bg-neutral-700">
                                <div class="h-2 rounded-full {{ $data['color'] }}" style="width: {{ ($data['count'] / $maxCount) * 100 }}%"></div>
                            </div>
                        </div>
                        <span class="text-sm font-medium text-neutral-600 dark:text-neutral-400 w-8 text-center">{{ $data['count'] }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Class Performance -->
        <div class="rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="border-b border-neutral-200 px-6 py-4 dark:border-neutral-700">
                <h3 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Class Performance') }}</h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @php
                        $classAverage = $enrollments->map(fn($e) => $e->grades->avg('percentage') ?? 0)->avg() ?? 0;
                        $highestGrade = $enrollments->map(fn($e) => $e->grades->max('percentage') ?? 0)->max() ?? 0;
                        $lowestGrade = $enrollments->map(fn($e) => $e->grades->min('percentage') ?? 0)->filter()->min() ?? 0;
                        $gradedStudents = $enrollments->filter(fn($e) => $e->grades->count() > 0)->count();
                    @endphp

                    <div class="text-center">
                        <p class="text-3xl font-bold text-indigo-600 dark:text-indigo-400">{{ number_format($classAverage, 1) }}%</p>
                        <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Class Average') }}</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4 pt-4">
                        <div class="text-center">
                            <p class="text-xl font-bold text-green-600 dark:text-green-400">{{ number_format($highestGrade, 1) }}%</p>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('Highest') }}</p>
                        </div>
                        <div class="text-center">
                            <p class="text-xl font-bold text-red-600 dark:text-red-400">{{ $lowestGrade > 0 ? number_format($lowestGrade, 1) . '%' : '--' }}</p>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('Lowest') }}</p>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-neutral-200 dark:border-neutral-700">
                        <div class="flex justify-between text-sm">
                            <span class="text-neutral-600 dark:text-neutral-400">{{ __('Graded Students') }}</span>
                            <span class="font-medium text-neutral-900 dark:text-neutral-100">{{ $gradedStudents }}/{{ $enrollments->count() }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

        <!-- Grades Table -->
        <div class="rounded-xl border border-neutral-200 bg-white dark:border-neutral-700 dark:bg-neutral-800 shadow-sm overflow-hidden">
            <div class="border-b border-neutral-200 px-6 py-4 dark:border-neutral-700">
                <div class="flex items-center gap-3">
                    <div class="rounded-lg bg-indigo-10 p-2">
                        <flux:icon.academic-cap class="w-5 h-5 text-indigo-600" />
                    </div>
                    <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">
                        {{ __('Student Grades') }}
                    </h2>
                </div>
            </div>
            @if($section->enrollments->where('status', 'approved')->isEmpty())
                <div class="p-12 text-center">
                    <div class="mb-4 inline-flex rounded-full bg-neutral-100 p-4 dark:bg-neutral-700">
                        <flux:icon.academic-cap class="w-8 h-8 text-neutral-400" />
                    </div>
                    <h3 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">
                        {{ __('No students enrolled') }}
                    </h3>
                    <p class="mt-2 text-neutral-600 dark:text-neutral-400">
                        {{ __('Students will appear here once they enroll in this course') }}
                    </p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-neutral-200 bg-neutral-50 dark:border-neutral-700 dark:bg-neutral-900">
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-neutral-600 dark:text-neutral-400">
                                    {{ __('Student') }}
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-neutral-600 dark:text-neutral-400">
                                    {{ __('Student ID') }}
                                </th>
                                @foreach($section->assessments as $assessment)
                                    <th class="px-4 py-4 text-center text-xs font-semibold uppercase tracking-wider text-neutral-600 dark:text-neutral-400">
                                        <div class="truncate max-w-[120px]" title="{{ $assessment->title }}">
                                            @if($assessment->quiz_type && $assessment->quiz_type !== 'none')
                                                <a href="{{ route('teacher.courses.grades.view', [$section, $assessment]) }}" class="hover:text-primary">
                                                    {{ $assessment->title }}
                                                </a>
                                            @else
                                                {{ $assessment->title }}
                                            @endif
                                        </div>
                                        <span class="text-xs font-normal text-neutral-400">({{ $assessment->max_score }})</span>
                                    </th>
                                @endforeach
                                <th class="px-6 py-4 text-center text-xs font-semibold uppercase tracking-wider text-neutral-600 dark:text-neutral-400">
                                    {{ __('Total') }}
                                </th>
                                <th class="px-6 py-4 text-center text-xs font-semibold uppercase tracking-wider text-neutral-600 dark:text-neutral-400">
                                    {{ __('Grade') }}
                                </th>
                                <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-neutral-600 dark:text-neutral-400">
                                    {{ __('Actions') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                            @foreach($section->enrollments->where('status', 'approved') as $enrollment)
                                <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-900 transition-colors">
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="h-10 w-10 rounded-full bg-indigo-10 flex items-center justify-center">
                                                <span class="text-sm font-semibold text-indigo-600">
                                                    {{ substr($enrollment->student?->profile?->first_name ?? 'S', 0, 1) }}{{ substr($enrollment->student?->profile?->last_name ?? '', 0, 1) }}
                                                </span>
                                            </div>
                                            <span class="text-sm font-medium text-neutral-900 dark:text-neutral-100">
                                                {{ $enrollment->student?->full_name ?? __('Unknown') }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-neutral-600 dark:text-neutral-400">
                                        {{ $enrollment->student?->user_id ?? '-' }}
                                    </td>
                                    @foreach($section->assessments as $assessment)
                                        <td class="whitespace-nowrap px-4 py-4 text-center">
                                            <span class="inline-flex items-center rounded-md bg-neutral-100 px-2 py-1 text-xs font-medium text-neutral-600 dark:bg-neutral-700 dark:text-neutral-300">
                                                --
                                            </span>
                                        </td>
                                    @endforeach
                                    <td class="whitespace-nowrap px-6 py-4 text-center text-sm font-medium text-neutral-900 dark:text-neutral-100">
                                        --
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-center">
                                        <span class="inline-flex items-center rounded-md bg-yellow-100 px-2 py-1 text-xs font-medium text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400">
                                            --
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-right">
                                        <flux:button size="sm" variant="subtle">
                                            <flux:icon.pencil class="w-4 h-4" />
                                        </flux:button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
