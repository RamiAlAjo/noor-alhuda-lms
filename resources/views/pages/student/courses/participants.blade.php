<x-layouts::app :title="__('Course Participants')">
    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-neutral-900 dark:text-neutral-100">{{ __('Participants') }}</h1>
            <p class="mt-1 text-neutral-500 dark:text-neutral-400">{{ $offering->course?->name ?? __('Course') }} - {{ $offering->section_name }}</p>
        </div>
        <flux:button :href="route('student.courses.show', $offering)" variant="ghost">
            <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12" />
            </svg>
            {{ __('Back to Course') }}
        </flux:button>
    </div>

    <!-- Stats Cards -->
    <div class="mb-6 grid gap-4 md:grid-cols-3">
        <div class="rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <p class="text-sm font-medium text-neutral-500 dark:text-neutral-400">{{ __('Total Participants') }}</p>
            <p class="mt-2 text-3xl font-bold text-neutral-900 dark:text-neutral-100">{{ $participants->count() }}</p>
        </div>
        <div class="rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <p class="text-sm font-medium text-neutral-500 dark:text-neutral-400">{{ __('Teacher') }}</p>
            <p class="mt-2 text-xl font-bold text-neutral-900 dark:text-neutral-100">{{ $offering->teacher?->full_name ?? __('Not assigned') }}</p>
        </div>
        <div class="rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <p class="text-sm font-medium text-neutral-500 dark:text-neutral-400">{{ __('Section') }}</p>
            <p class="mt-2 text-xl font-bold text-neutral-900 dark:text-neutral-100">{{ $offering->section_name }}</p>
        </div>
    </div>

    <!-- Participants List -->
    <div class="rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
        <div class="border-b border-neutral-200 px-6 py-4 dark:border-neutral-700">
            <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Enrolled Students') }}</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-neutral-50 text-neutral-500 dark:bg-neutral-700 dark:text-neutral-400">
                    <tr>
                        <th class="px-6 py-3">{{ __('Student') }}</th>
                        <th class="px-6 py-3">{{ __('Email') }}</th>
                        <th class="px-6 py-3">{{ __('Enrolled Date') }}</th>
                        <th class="px-6 py-3">{{ __('Status') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                    @forelse($participants as $participant)
                    <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-700/50">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-indigo-100 text-indigo-600 dark:bg-indigo-900 dark:text-indigo-300">
                                    {{ strtoupper(substr($participant->student?->first_name ?? 'S', 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-medium text-neutral-900 dark:text-neutral-100">{{ $participant->student?->full_name ?? __('Unknown') }}</p>
                                    <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ $participant->student?->user_id ?? '-' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-neutral-600 dark:text-neutral-400">
                            {{ $participant->student?->email ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-neutral-600 dark:text-neutral-400">
                            {{ $participant->enrolled_at ? $participant->enrolled_at->format('M d, Y') : '-' }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800 dark:bg-green-900 dark:text-green-200">
                                {{ __($participant->status) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-neutral-100 dark:bg-neutral-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="size-8 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                </div>
                                <h3 class="text-lg font-medium text-neutral-900 dark:text-neutral-100">{{ __('No participants yet') }}</h3>
                                <p class="mt-1 text-neutral-500 dark:text-neutral-400">{{ __('Students will appear here once enrolled') }}</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if(method_exists($participants, 'hasPages') && $participants->hasPages())
            <div class="border-t border-neutral-200 px-6 py-4 dark:border-neutral-700">
                {{ $participants->links() }}
            </div>
        @endif
    </div>
</x-layouts::app>
