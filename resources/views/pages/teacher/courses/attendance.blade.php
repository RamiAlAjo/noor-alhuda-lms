<x-layouts::app :title="__('Attendance')">
    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-neutral-900 dark:text-neutral-100">{{ __('Attendance') }}</h1>
            <p class="mt-1 text-neutral-500 dark:text-neutral-400">{{ $section->course?->name ?? __('Course') }} - {{ __('Section') }} {{ $section->sectionNumber }}</p>
        </div>
        <flux:button :href="route('teacher.courses.show', $section)" variant="ghost">
            <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12" />
            </svg>
            {{ __('Back to Course') }}
        </flux:button>
    </div>

    <!-- Stats -->
    <div class="mb-6 grid gap-4 md:grid-cols-5">
        <div class="group relative overflow-hidden rounded-xl border border-neutral-200 bg-white p-5 shadow-sm transition-all hover:shadow-md dark:border-neutral-700 dark:bg-neutral-800">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-50 to-indigo-50 opacity-0 transition-opacity group-hover:opacity-100 dark:from-blue-900/20 dark:to-indigo-900/20"></div>
            <div class="relative flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100 text-blue-600 dark:bg-blue-900 dark:text-blue-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Enrolled Students') }}</p>
                    <p class="text-xl font-bold text-neutral-900 dark:text-neutral-100">{{ $section->enrollments->where('status', 'approved')->count() }}</p>
                </div>
            </div>
        </div>
        <div class="group relative overflow-hidden rounded-xl border border-neutral-200 bg-white p-5 shadow-sm transition-all hover:shadow-md dark:border-neutral-700 dark:bg-neutral-800">
            <div class="absolute inset-0 bg-gradient-to-br from-green-50 to-emerald-50 opacity-0 transition-opacity group-hover:opacity-100 dark:from-green-900/20 dark:to-emerald-900/20"></div>
            <div class="relative flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-green-100 text-green-600 dark:bg-green-900 dark:text-green-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Present') }}</p>
                    <p class="text-xl font-bold text-neutral-900 dark:text-neutral-100">{{ $presentCount ?? 0 }}</p>
                </div>
            </div>
        </div>
        <div class="group relative overflow-hidden rounded-xl border border-neutral-200 bg-white p-5 shadow-sm transition-all hover:shadow-md dark:border-neutral-700 dark:bg-neutral-800">
            <div class="absolute inset-0 bg-gradient-to-br from-red-50 to-rose-50 opacity-0 transition-opacity group-hover:opacity-100 dark:from-red-900/20 dark:to-rose-900/20"></div>
            <div class="relative flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-red-100 text-red-600 dark:bg-red-900 dark:text-red-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Absent') }}</p>
                    <p class="text-xl font-bold text-neutral-900 dark:text-neutral-100">{{ $absentCount ?? 0 }}</p>
                </div>
            </div>
        </div>
        <div class="group relative overflow-hidden rounded-xl border border-neutral-200 bg-white p-5 shadow-sm transition-all hover:shadow-md dark:border-neutral-700 dark:bg-neutral-800">
            <div class="absolute inset-0 bg-gradient-to-br from-yellow-50 to-amber-50 opacity-0 transition-opacity group-hover:opacity-100 dark:from-yellow-900/20 dark:to-amber-900/20"></div>
            <div class="relative flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-yellow-100 text-yellow-600 dark:bg-yellow-900 dark:text-yellow-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Excused') }}</p>
                    <p class="text-xl font-bold text-neutral-900 dark:text-neutral-100">{{ $excusedCount ?? 0 }}</p>
                </div>
            </div>
        </div>
        <div class="group relative overflow-hidden rounded-xl border border-neutral-200 bg-white p-5 shadow-sm transition-all hover:shadow-md dark:border-neutral-700 dark:bg-neutral-800">
            <div class="absolute inset-0 bg-gradient-to-br from-indigo-50 to-purple-50 opacity-0 transition-opacity group-hover:opacity-100 dark:from-indigo-900/20 dark:to-purple-900/20"></div>
            <div class="relative flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-100 text-indigo-600 dark:bg-indigo-900 dark:text-indigo-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Attendance Rate') }}</p>
                    <p class="text-xl font-bold text-neutral-900 dark:text-neutral-100">{{ $attendanceRate ? $attendanceRate . '%' : '--' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Attendance Form -->
    <div class="rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
        <div class="border-b border-neutral-200 px-6 py-4 dark:border-neutral-700">
            <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Mark Attendance') }}</h2>
        </div>
        <form method="POST" action="{{ route('teacher.courses.attendance.store', $section) }}" class="p-6">
            @csrf

            <div class="mb-6">
                <flux:input
                    type="date"
                    name="date"
                    :label="__('Select Date')"
                    :value="now()->format('Y-m-d')"
                    required
                />
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-neutral-200 bg-neutral-50 dark:border-neutral-700 dark:bg-neutral-900">
                            <th class="px-4 py-3 text-left text-sm font-medium text-neutral-600 dark:text-neutral-400">{{ __('Student') }}</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-neutral-600 dark:text-neutral-400">{{ __('Student ID') }}</th>
                            <th class="px-4 py-3 text-center text-sm font-medium text-neutral-600 dark:text-neutral-400">{{ __('Status') }}</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-neutral-600 dark:text-neutral-400">{{ __('Notes') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                        @forelse($section->enrollments->where('status', 'approved') as $enrollment)
                            <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-700/50">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 text-xs font-bold text-white">
                                            {{ substr($enrollment->student->profile?->first_name ?? 'S', 0, 1) }}{{ substr($enrollment->student->profile?->last_name ?? '', 0, 1) }}
                                        </div>
                                        <span class="text-neutral-900 dark:text-neutral-100">{{ $enrollment->student?->fullName ?? __('Unknown') }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-neutral-600 dark:text-neutral-400">{{ $enrollment->student?->id ?? 'N/A' }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-center gap-1">
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="attendance[{{ $enrollment->id }}]" value="present" class="text-green-600 focus:ring-green-500" checked>
                                            <span class="ml-1 text-sm text-neutral-700 dark:text-neutral-300">{{ __('Present') }}</span>
                                        </label>
                                        <label class="inline-flex items-center ml-3">
                                            <input type="radio" name="attendance[{{ $enrollment->id }}]" value="absent" class="text-red-600 focus:ring-red-500">
                                            <span class="ml-1 text-sm text-neutral-700 dark:text-neutral-300">{{ __('Absent') }}</span>
                                        </label>
                                        <label class="inline-flex items-center ml-3">
                                            <input type="radio" name="attendance[{{ $enrollment->id }}]" value="excused" class="text-yellow-600 focus:ring-yellow-500">
                                            <span class="ml-1 text-sm text-neutral-700 dark:text-neutral-300">{{ __('Excused') }}</span>
                                        </label>
                                        <label class="inline-flex items-center ml-3">
                                            <input type="radio" name="attendance[{{ $enrollment->id }}]" value="late" class="text-blue-600 focus:ring-blue-500">
                                            <span class="ml-1 text-sm text-neutral-700 dark:text-neutral-300">{{ __('Late') }}</span>
                                        </label>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <input type="text" name="notes[{{ $enrollment->id }}]" placeholder="{{ __('Optional notes') }}" class="rounded-lg border border-neutral-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-100">
                                </td>
                            </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-neutral-500 dark:text-neutral-400">
                                {{ __('No enrolled students') }}
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($section->enrollments->where('status', 'approved')->count() > 0)
            <div class="mt-6 flex justify-end">
                <flux:button type="submit" variant="primary">
                    {{ __('Save Attendance') }}
                </flux:button>
            </div>
            @endif
        </form>
    </div>
</x-layouts::app>
