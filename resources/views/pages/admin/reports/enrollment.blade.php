<x-layouts::app :title="__('Enrollment Reports')">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-neutral-900 dark:text-neutral-100">{{ __('Enrollment Reports') }}</h1>
        <p class="mt-1 text-neutral-500 dark:text-neutral-400">{{ __('View enrollment statistics and trends') }}</p>
    </div>

    <!-- Stats Cards -->
    <div class="mb-6 grid gap-4 md:grid-cols-3">
        <div class="rounded-xl border border-neutral-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-green-100 text-green-600 dark:bg-green-900 dark:text-green-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Approved') }}</p>
                    <p class="text-2xl font-bold text-neutral-900 dark:text-neutral-100">
                        {{ $enrollmentsByStatus->where('status', 'approved')->first()?->count ?? 0 }}
                    </p>
                </div>
            </div>
        </div>
        <div class="rounded-xl border border-neutral-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-yellow-100 text-yellow-600 dark:bg-yellow-900 dark:text-yellow-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="-500 dark:text-neutral-400">{{ __('Pending') }}</p>
                    <p class="text-2xl font-bold text-neutral-900 dark:text-neutraltext-sm text-neutral-100">
                        {{ $enrollmentsByStatus->where('status', 'pending')->first()?->count ?? 0 }}
                    </p>
                </div>
            </div>
        </div>
        <div class="rounded-xl border border-neutral-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-red-100 text-red-600 dark:bg-red-900 dark:text-red-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Rejected') }}</p>
                    <p class="text-2xl font-bold text-neutral-900 dark:text-neutral-100">
                        {{ $enrollmentsByStatus->where('status', 'rejected')->first()?->count ?? 0 }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Enrollments by Course -->
    <div class="rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
        <h3 class="mb-4 text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Enrollments by Course') }}</h3>

        @if($enrollmentsByCourse->isEmpty())
            <div class="text-center py-8 text-neutral-500 dark:text-neutral-400">
                {{ __('No enrollment data available') }}
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-neutral-200 dark:border-neutral-700">
                            <th class="pb-3 text-start text-sm font-medium text-neutral-500 dark:text-neutral-400">{{ __('Course') }}</th>
                            <th class="pb-3 text-start text-sm font-medium text-neutral-500 dark:text-neutral-400">{{ __('Enrollments') }}</th>
                            <th class="pb-3 text-start text-sm font-medium text-neutral-500 dark:text-neutral-400">{{ __('Progress') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($enrollmentsByCourse as $enrollment)
                            <tr class="border-b border-neutral-100 dark:border-neutral-800">
                                <td class="py-3 text-neutral-900 dark:text-neutral-100">
                                    {{ $enrollment->section?->course?->name ?? __('Unknown Course') }}
                                </td>
                                <td class="py-3 text-neutral-900 dark:text-neutral-100">
                                    {{ $enrollment->count }}
                                </td>
                                <td class="py-3">
                                    <div class="w-full max-w-xs h-2 bg-neutral-200 rounded-full dark:bg-neutral-700">
                                        <div class="h-2 bg-blue-600 rounded-full" style="width: {{ min($enrollment->count * 10, 100) }}%"></div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-layouts::app>
