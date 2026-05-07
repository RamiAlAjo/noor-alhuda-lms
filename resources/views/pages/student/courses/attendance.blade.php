<x-layouts::app :title="__('My Attendance')">
    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-neutral-900 dark:text-neutral-100">{{ $offering->course?->name ?? __('Course') }}</h1>
            <p class="mt-1 text-neutral-500 dark:text-neutral-400">{{ __('Section') }} {{ $offering->section_name }} - {{ __('Attendance') }}</p>
        </div>
        <flux:button :href="route('student.courses.show', $offering)" variant="ghost">
            <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12" />
            </svg>
            {{ __('Back to Course') }}
        </flux:button>
    </div>

    <!-- Attendance Summary with Gradients -->
    <div class="mb-6 grid gap-4 md:grid-cols-5">
        <div class="group relative overflow-hidden rounded-xl border border-neutral-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-50 to-indigo-50 opacity-0 transition-opacity group-hover:opacity-100 dark:from-blue-900/20 dark:to-indigo-900/20"></div>
            <div class="relative">
                <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Total Sessions') }}</p>
                <p class="text-3xl font-bold text-neutral-900 dark:text-neutral-100">{{ $totalSessions }}</p>
            </div>
        </div>
        <div class="group relative overflow-hidden rounded-xl border border-neutral-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="absolute inset-0 bg-gradient-to-br from-green-50 to-emerald-50 opacity-0 transition-opacity group-hover:opacity-100 dark:from-green-900/20 dark:to-emerald-900/20"></div>
            <div class="relative">
                <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Present') }}</p>
                <p class="text-3xl font-bold text-green-600 dark:text-green-400">{{ $presentCount }}</p>
            </div>
        </div>
        <div class="group relative overflow-hidden rounded-xl border border-neutral-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="absolute inset-0 bg-gradient-to-br from-amber-50 to-orange-50 opacity-0 transition-opacity group-hover:opacity-100 dark:from-amber-900/20 dark:to-orange-900/20"></div>
            <div class="relative">
                <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Late') }}</p>
                <p class="text-3xl font-bold text-amber-600 dark:text-amber-400">{{ $lateCount ?? 0 }}</p>
            </div>
        </div>
        <div class="group relative overflow-hidden rounded-xl border border-neutral-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="absolute inset-0 bg-gradient-to-br from-red-50 to-rose-50 opacity-0 transition-opacity group-hover:opacity-100 dark:from-red-900/20 dark:to-rose-900/20"></div>
            <div class="relative">
                <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Absent') }}</p>
                <p class="text-3xl font-bold text-red-600 dark:text-red-400">{{ $absentCount }}</p>
            </div>
        </div>
        <div class="group relative overflow-hidden rounded-xl border border-neutral-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="absolute inset-0 bg-gradient-to-br from-violet-50 to-purple-50 opacity-0 transition-opacity group-hover:opacity-100 dark:from-violet-900/20 dark:to-purple-900/20"></div>
            <div class="relative">
                <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Attendance Rate') }}</p>
                <p class="text-3xl font-bold text-neutral-900 dark:text-neutral-100">{{ $totalSessions > 0 ? number_format(($presentCount / $totalSessions) * 100, 1) : 0 }}%</p>
                <div class="mt-2 h-2 w-full overflow-hidden rounded-full bg-neutral-100 dark:bg-neutral-700">
                    <div class="h-full rounded-full bg-gradient-to-r from-green-500 to-emerald-500" style="width: {{ $totalSessions > 0 ? ($presentCount / $totalSessions) * 100 : 0 }}%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Attendance Records -->
    <div class="rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
        <div class="border-b border-neutral-200 px-6 py-4 dark:border-neutral-700">
            <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Attendance Records') }}</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-neutral-50 text-neutral-500 dark:bg-neutral-700 dark:text-neutral-400">
                    <tr>
                        <th class="px-6 py-3">{{ __('Date') }}</th>
                        <th class="px-6 py-3">{{ __('Status') }}</th>
                        <th class="px-6 py-3">{{ __('Notes') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                    @forelse($attendance as $record)
                    <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-700/50">
                        <td class="px-6 py-4 text-neutral-900 dark:text-neutral-100">{{ $record->date->format('Y-m-d') }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                @if($record->status === 'present') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                @elseif($record->status === 'absent') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                @else bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                @endif">
                                {{ __($record->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-neutral-500 dark:text-neutral-400">{{ $record->notes ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-neutral-100 dark:bg-neutral-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="size-8 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <h3 class="text-lg font-medium text-neutral-900 dark:text-neutral-100">{{ __('No attendance records') }}</h3>
                                <p class="text-neutral-500 dark:text-neutral-400">{{ __('Your attendance records will appear here') }}</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts::app>
