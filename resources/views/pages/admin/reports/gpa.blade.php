<x-layouts::app :title="__('GPA Reports')">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-neutral-900 dark:text-neutral-100">{{ __('GPA Reports') }}</h1>
        <p class="mt-1 text-neutral-500 dark:text-neutral-400">{{ __('View student GPA analytics and statistics') }}</p>
    </div>

    <!-- Stats Cards -->
    <div class="mb-6 grid gap-4 md:grid-cols-3">
        <div class="rounded-xl border border-neutral-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-100 text-blue-600 dark:bg-blue-900 dark:text-blue-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Average GPA') }}</p>
                    <p class="text-2xl font-bold text-neutral-900 dark:text-neutral-100">{{ $averageGpa }}</p>
                </div>
            </div>
        </div>
        <div class="rounded-xl border border-neutral-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-green-100 text-green-600 dark:bg-green-900 dark:text-green-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Total Students') }}</p>
                    <p class="text-2xl font-bold text-neutral-900 dark:text-neutral-100">{{ $gpaData->count() }}</p>
                </div>
            </div>
        </div>
        <div class="rounded-xl border border-neutral-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-purple-100 text-purple-600 dark:bg-purple-900 dark:text-purple-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Honors (3.5+)') }}</p>
                    <p class="text-2xl font-bold text-neutral-900 dark:text-neutral-100">
                        {{ $gpaData->where('gpa', '>=', 3.5)->count() }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- GPA Distribution -->
    <div class="mb-6 grid gap-4 md:grid-cols-4">
        <div class="rounded-xl border border-neutral-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-800 text-center">
            <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('4.0 - 3.5') }}</p>
            <p class="text-xl font-bold text-green-600 dark:text-green-400">
                {{ $gpaData->where('gpa', '>=', 3.5)->count() }}
            </p>
        </div>
        <div class="rounded-xl border border-neutral-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-800 text-center">
            <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('3.4 - 3.0') }}</p>
            <p class="text-xl font-bold text-blue-600 dark:text-blue-400">
                {{ $gpaData->whereBetween('gpa', [3.0, 3.49])->count() }}
            </p>
        </div>
        <div class="rounded-xl border border-neutral-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-800 text-center">
            <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('2.9 - 2.0') }}</p>
            <p class="text-xl font-bold text-yellow-600 dark:text-yellow-400">
                {{ $gpaData->whereBetween('gpa', [2.0, 2.99])->count() }}
            </p>
        </div>
        <div class="rounded-xl border border-neutral-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-800 text-center">
            <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Below 2.0') }}</p>
            <p class="text-xl font-bold text-red-600 dark:text-red-400">
                {{ $gpaData->where('gpa', '<', 2.0)->count() }}
            </p>
        </div>
    </div>

    <!-- Student GPA Table -->
    <div class="rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Student GPA List') }}</h3>
            <a href="{{ route('admin.reports.gpa.export') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">
                {{ __('Export') }}
            </a>
        </div>

        @if($gpaData->isEmpty())
            <div class="text-center py-8 text-neutral-500 dark:text-neutral-400">
                {{ __('No GPA data available') }}
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-neutral-200 dark:border-neutral-700">
                            <th class="pb-3 text-start text-sm font-medium text-neutral-500 dark:text-neutral-400">{{ __('Rank') }}</th>
                            <th class="pb-3 text-start text-sm font-medium text-neutral-500 dark:text-neutral-400">{{ __('Student') }}</th>
                            <th class="pb-3 text-start text-sm font-medium text-neutral-500 dark:text-neutral-400">{{ __('Email') }}</th>
                            <th class="pb-3 text-start text-sm font-medium text-neutral-500 dark:text-neutral-400">{{ __('Total Credits') }}</th>
                            <th class="pb-3 text-start text-sm font-medium text-neutral-500 dark:text-neutral-400">{{ __('GPA') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($gpaData as $index => $data)
                            <tr class="border-b border-neutral-100 dark:border-neutral-800">
                                <td class="py-3 text-neutral-900 dark:text-neutral-100">
                                    #{{ $index + 1 }}
                                </td>
                                <td class="py-3 text-neutral-900 dark:text-neutral-100 font-medium">
                                    {{ $data['student']->name }}
                                </td>
                                <td class="py-3 text-neutral-600 dark:text-neutral-400">
                                    {{ $data['student']->email }}
                                </td>
                                <td class="py-3 text-neutral-600 dark:text-neutral-400">
                                    {{ $data['total_credits'] }}
                                </td>
                                <td class="py-3">
                                    @if($data['gpa'] >= 3.5)
                                        <span class="text-green-600 font-bold">{{ number_format($data['gpa'], 2) }}</span>
                                    @elseif($data['gpa'] >= 3.0)
                                        <span class="text-blue-600 font-bold">{{ number_format($data['gpa'], 2) }}</span>
                                    @elseif($data['gpa'] >= 2.0)
                                        <span class="text-yellow-600 font-bold">{{ number_format($data['gpa'], 2) }}</span>
                                    @else
                                        <span class="text-red-600 font-bold">{{ number_format($data['gpa'], 2) }}</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-layouts::app>
