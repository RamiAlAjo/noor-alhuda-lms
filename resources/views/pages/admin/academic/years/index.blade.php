<x-layouts::app :title="__('Academic Years')">
    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-neutral-900 dark:text-neutral-100">{{ __('Academic Years') }}</h1>
            <p class="mt-1 text-neutral-500 dark:text-neutral-400">{{ __('Manage academic years and semesters') }}</p>
        </div>
        <flux:button variant="primary" onclick="document.getElementById('create-year-modal').showModal()">
            <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            {{ __('Add Academic Year') }}
        </flux:button>
    </div>

    <!-- Years Table -->
    <div class="rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-neutral-50 text-neutral-500 dark:bg-neutral-700 dark:text-neutral-400">
                    <tr>
                        <th class="px-6 py-3">{{ __('Year Name') }}</th>
                        <th class="px-6 py-3">{{ __('Start Year') }}</th>
                        <th class="px-6 py-3">{{ __('End Year') }}</th>
                        <th class="px-6 py-3">{{ __('Semesters') }}</th>
                        <th class="px-6 py-3">{{ __('Status') }}</th>
                        <th class="px-6 py-3 text-right">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                    @forelse($years as $year)
                    <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-700/50">
                        <td class="px-6 py-4">
                            <span class="font-medium text-neutral-900 dark:text-neutral-100">{{ $year->name }}</span>
                        </td>
                        <td class="px-6 py-4 text-neutral-600 dark:text-neutral-400">{{ $year->start_year }}</td>
                        <td class="px-6 py-4 text-neutral-600 dark:text-neutral-400">{{ $year->end_year }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <span class="text-neutral-600 dark:text-neutral-400">{{ $year->semesters->count() }}</span>
                                <flux:button size="xs" variant="ghost" onclick="document.getElementById('manage-semesters-{{ $year->id }}').showModal()">
                                    {{ __('Manage') }}
                                </flux:button>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if($year->is_active)
                            <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800 dark:bg-green-900/50 dark:text-green-400">
                                {{ __('Active') }}
                            </span>
                            @else
                            <span class="inline-flex items-center rounded-full bg-neutral-100 px-2.5 py-0.5 text-xs font-medium text-neutral-800 dark:bg-neutral-700 dark:text-neutral-300">
                                {{ __('Inactive') }}
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <flux:button size="sm" variant="ghost" onclick="document.getElementById('edit-year-{{ $year->id }}').showModal()">
                                    {{ __('Edit') }}
                                </flux:button>
                                <form method="POST" action="{{ route('admin.academic.years.destroy', $year) }}">
                                    @csrf
                                    @method('DELETE')
                                    <x-button.submit size="sm" variant="danger" loading-text="{{ __('Deleting...') }}" confirm="{{ __('Are you sure you want to delete this academic year?') }}">
                                        {{ __('Delete') }}
                                    </x-button.submit>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-neutral-100 dark:bg-neutral-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="size-8 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <h3 class="text-lg font-medium text-neutral-900 dark:text-neutral-100">{{ __('No academic years') }}</h3>
                                <p class="text-neutral-500 dark:text-neutral-400">{{ __('Create your first academic year to get started') }}</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Create Year Modal -->
    <dialog id="create-year-modal" class="rounded-xl shadow-2xl backdrop:bg-black/60 p-0 w-full max-w-md">
        <div class="overflow-hidden bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-500 px-6 py-5">
            <div class="absolute -right-8 -top-8 h-24 w-24 rounded-full bg-white/10"></div>
            <div class="absolute -bottom-4 -left-4 h-16 w-16 rounded-full bg-white/10"></div>
            <div class="relative flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-white/20">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <div class="flex-1">
                    <h2 class="text-xl font-bold text-white">{{ __('Create New Academic Year') }}</h2>
                    <p class="text-sm text-indigo-100">{{ __('Add a new academic year to the system') }}</p>
                </div>
                <button onclick="document.getElementById('create-year-modal').close()" class="rounded-full bg-white/20 p-2 text-white transition hover:bg-white/30">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
        <form method="POST" action="{{ route('admin.academic.years.store') }}" class="p-6">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="mb-1 block text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Year Name') }}</label>
                    <input type="text" name="name" placeholder="e.g., 2025-2026" required
                        class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-neutral-900 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white" />
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Start Year') }}</label>
                        <input type="number" name="start_year" placeholder="2025" min="2000" max="2100" required
                            class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-neutral-900 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white" />
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('End Year') }}</label>
                        <input type="number" name="end_year" placeholder="2026" min="2000" max="2100" required
                            class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-neutral-900 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white" />
                    </div>
                </div>
                <div class="flex items-center justify-between rounded-xl bg-neutral-50 p-4 dark:bg-neutral-900">
                    <div>
                        <label class="text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Active Status') }}</label>
                        <p class="text-xs text-neutral-500">{{ __('Make this academic year active') }}</p>
                    </div>
                    <label class="relative inline-flex cursor-pointer items-center">
                        <input type="checkbox" name="is_active" value="1" checked class="peer sr-only" />
                        <div class="h-6 w-11 rounded-full bg-neutral-200 after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:bg-white after:transition-all after:content-[''] peer-checked:bg-green-600 peer-checked:after:translate-x-full peer-checked:after:border-white dark:bg-neutral-700"></div>
                    </label>
                </div>
            </div>
            <div class="mt-6 flex gap-3">
                <button type="button" onclick="document.getElementById('create-year-modal').close()" class="flex-1 justify-center rounded-lg px-4 py-2 text-neutral-600 hover:bg-neutral-100 dark:text-neutral-400 dark:hover:bg-neutral-800">
                    {{ __('Cancel') }}
                </button>
                <x-button.submit loading-text="{{ __('Creating...') }}" class="flex-1">{{ __('Create Year') }}</x-button.submit>
            </div>
        </form>
    </dialog>

    <!-- Edit Year Modals -->
    @forelse($years as $year)
    <dialog id="edit-year-{{ $year->id }}" class="rounded-xl shadow-2xl backdrop:bg-black/60 p-0 w-full max-w-md">
        <div class="overflow-hidden bg-gradient-to-r from-amber-500 to-orange-500 px-6 py-5">
            <div class="absolute -right-8 -top-8 h-24 w-24 rounded-full bg-white/10"></div>
            <div class="absolute -bottom-4 -left-4 h-16 w-16 rounded-full bg-white/10"></div>
            <div class="relative flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-white/20">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                </div>
                <div class="flex-1">
                    <h2 class="text-xl font-bold text-white">{{ __('Edit Academic Year') }}</h2>
                    <p class="text-sm text-amber-100">{{ __('Update academic year information') }}</p>
                </div>
                <button onclick="document.getElementById('edit-year-{{ $year->id }}').close()" class="rounded-full bg-white/20 p-2 text-white transition hover:bg-white/30">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
        <form method="POST" action="{{ route('admin.academic.years.update', $year) }}" class="p-6">
            @csrf
            @method('PUT')
            <div class="space-y-4">
                <div>
                    <label class="mb-1 block text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Year Name') }}</label>
                    <input type="text" name="name" value="{{ $year->name }}" required
                        class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-neutral-900 focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-200 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white" />
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Start Year') }}</label>
                        <input type="number" name="start_year" value="{{ $year->start_year }}" min="2000" max="2100" required
                            class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-neutral-900 focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-200 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white" />
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('End Year') }}</label>
                        <input type="number" name="end_year" value="{{ $year->end_year }}" min="2000" max="2100" required
                            class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-neutral-900 focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-200 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white" />
                    </div>
                </div>
                <div class="flex items-center justify-between rounded-xl bg-neutral-50 p-4 dark:bg-neutral-900">
                    <div>
                        <label class="text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Active Status') }}</label>
                        <p class="text-xs text-neutral-500">{{ __('Enable or disable this year') }}</p>
                    </div>
                    <label class="relative inline-flex cursor-pointer items-center">
                        <input type="checkbox" name="is_active" value="1" {{ $year->is_active ? 'checked' : '' }} class="peer sr-only" />
                        <div class="h-6 w-11 rounded-full bg-neutral-200 after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:bg-white after:transition-all after:content-[''] peer-checked:bg-amber-500 peer-checked:after:translate-x-full peer-checked:after:border-white dark:bg-neutral-700"></div>
                    </label>
                </div>
            </div>
            <div class="mt-6 flex gap-3">
                <button type="button" onclick="document.getElementById('edit-year-{{ $year->id }}').close()" class="flex-1 justify-center rounded-lg px-4 py-2 text-neutral-600 hover:bg-neutral-100 dark:text-neutral-400 dark:hover:bg-neutral-800">
                    {{ __('Cancel') }}
                </button>
                <x-button.submit loading-text="{{ __('Updating...') }}" class="flex-1">{{ __('Update Year') }}</x-button.submit>
            </div>
        </form>
    </dialog>
    @empty
    @endforelse

    <!-- Manage Semesters Modals -->
    @forelse($years as $year)
    <dialog id="manage-semesters-{{ $year->id }}" class="rounded-xl shadow-2xl backdrop:bg-black/60 p-0 w-full max-w-2xl">
        <div class="overflow-hidden bg-gradient-to-r from-teal-600 via-cyan-600 to-blue-600 px-6 py-5">
            <div class="absolute -right-8 -top-8 h-24 w-24 rounded-full bg-white/10"></div>
            <div class="absolute -bottom-4 -left-4 h-16 w-16 rounded-full bg-white/10"></div>
            <div class="relative flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-white/20">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-white">{{ __('Semesters') }}</h2>
                        <p class="text-sm text-teal-100">{{ $year->name }}</p>
                    </div>
                </div>
                <button onclick="document.getElementById('manage-semesters-{{ $year->id }}').close()" class="rounded-full bg-white/20 p-2 text-white transition hover:bg-white/30">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
        <div class="p-6">
            <!-- Add Semester Button -->
            <div class="mb-4">
                <flux:button size="sm" variant="primary" onclick="document.getElementById('create-semester-{{ $year->id }}').showModal()">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    {{ __('Add Semester') }}
                </flux:button>
            </div>

            <!-- Semesters List -->
            @if($year->semesters->count() > 0)
            <div class="space-y-3">
                @foreach($year->semesters as $semester)
                <div class="flex items-center justify-between rounded-lg border border-neutral-200 p-4 dark:border-neutral-700">
                    <div>
                        <h4 class="font-medium text-neutral-900 dark:text-neutral-100">{{ $semester->name }}</h4>
                        <p class="text-sm text-neutral-500 dark:text-neutral-400">
                            {{ $semester->start_date?->format('M d, Y') ?? 'No start date' }} - {{ $semester->end_date?->format('M d, Y') ?? 'No end date' }}
                        </p>
                        <div class="mt-1 flex gap-2">
                            @if($semester->enrollment_start_date)
                            <span class="text-xs text-neutral-500">{{ __('lms.enrollment') }}: {{ $semester->enrollment_start_date->format('M d') }} - {{ $semester->enrollment_end_date?->format('M d') ?? __('lms.not_set') }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <flux:button size="sm" variant="ghost" onclick="document.getElementById('edit-semester-{{ $semester->id }}').showModal()">
                            {{ __('Edit') }}
                        </flux:button>
                        <form method="POST" action="{{ route('admin.academic.semesters.destroy', $semester) }}">
                            @csrf
                            @method('DELETE')
                            <x-button.submit size="sm" variant="danger" loading-text="{{ __('Deleting...') }}" confirm="{{ __('Are you sure you want to delete this semester?') }}">
                                {{ __('Delete') }}
                            </x-button.submit>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="py-8 text-center">
                <div class="mb-4 flex justify-center">
                    <div class="flex h-16 w-16 items-center justify-center rounded-full bg-neutral-100 dark:bg-neutral-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-8 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>
                <p class="text-neutral-500 dark:text-neutral-400">{{ __('No semesters yet') }}</p>
                <p class="text-sm text-neutral-400 dark:text-neutral-500">{{ __('Add a semester to this academic year') }}</p>
            </div>
            @endif

            <div class="mt-6 flex justify-end">
                <button type="button" onclick="document.getElementById('manage-semesters-{{ $year->id }}').close()" class="rounded-lg px-4 py-2 text-neutral-600 hover:bg-neutral-100 dark:text-neutral-400 dark:hover:bg-neutral-800">
                    {{ __('Close') }}
                </button>
            </div>
        </div>
    </dialog>

    <!-- Create Semester Modal for this year -->
    <dialog id="create-semester-{{ $year->id }}" class="rounded-xl shadow-2xl backdrop:bg-black/60 p-0 w-full max-w-md">
        <div class="overflow-hidden bg-gradient-to-r from-teal-600 to-cyan-600 px-6 py-5">
            <div class="relative flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-white/20">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-white">{{ __('Add Semester') }}</h2>
                        <p class="text-sm text-teal-100">{{ $year->name }}</p>
                    </div>
                </div>
                <button onclick="document.getElementById('create-semester-{{ $year->id }}').close()" class="rounded-full bg-white/20 p-2 text-white transition hover:bg-white/30">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
        <form method="POST" action="{{ route('admin.academic.semesters.store') }}" class="p-6">
            @csrf
            <input type="hidden" name="academic_year_id" value="{{ $year->id }}">
            <div class="space-y-4">
                <div>
                    <label class="mb-1 block text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Semester Name') }}</label>
                    <input type="text" name="name" placeholder="e.g., Fall 2025" required
                        class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-neutral-900 focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-200 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white" />
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Start Date') }}</label>
                        <input type="date" name="start_date" required
                            class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-neutral-900 focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-200 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white" />
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('End Date') }}</label>
                        <input type="date" name="end_date" required
                            class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-neutral-900 focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-200 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white" />
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Enrollment Start') }}</label>
                        <input type="date" name="enrollment_start_date"
                            class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-neutral-900 focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-200 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white" />
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Enrollment End') }}</label>
                        <input type="date" name="enrollment_end_date"
                            class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-neutral-900 focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-200 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white" />
                    </div>
                </div>
            </div>
            <div class="mt-6 flex gap-3">
                <button type="button" onclick="document.getElementById('create-semester-{{ $year->id }}').close()" class="flex-1 justify-center rounded-lg px-4 py-2 text-neutral-600 hover:bg-neutral-100 dark:text-neutral-400 dark:hover:bg-neutral-800">
                    {{ __('Cancel') }}
                </button>
                <x-button.submit loading-text="{{ __('Adding...') }}" class="flex-1">{{ __('Add Semester') }}</x-button.submit>
            </div>
        </form>
    </dialog>
    @empty
    @endforelse

    <!-- Edit Semester Modals -->
    @forelse($years as $year)
        @forelse($year->semesters as $semester)
        <dialog id="edit-semester-{{ $semester->id }}" class="rounded-xl shadow-2xl backdrop:bg-black/60 p-0 w-full max-w-md">
            <div class="overflow-hidden bg-gradient-to-r from-amber-500 to-orange-500 px-6 py-5">
                <div class="relative flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-white/20">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-white">{{ __('Edit Semester') }}</h2>
                            <p class="text-sm text-amber-100">{{ $year->name }}</p>
                        </div>
                    </div>
                    <button onclick="document.getElementById('edit-semester-{{ $semester->id }}').close()" class="rounded-full bg-white/20 p-2 text-white transition hover:bg-white/30">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
            <form method="POST" action="{{ route('admin.academic.semesters.update', $semester) }}" class="p-6">
                @csrf
                @method('PUT')
                <div class="space-y-4">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Semester Name') }}</label>
                        <input type="text" name="name" value="{{ $semester->name }}" required
                            class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-neutral-900 focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-200 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white" />
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Start Date') }}</label>
                            <input type="date" name="start_date" value="{{ $semester->start_date?->format('Y-m-d') }}" required
                                class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-neutral-900 focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-200 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white" />
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('End Date') }}</label>
                            <input type="date" name="end_date" value="{{ $semester->end_date?->format('Y-m-d') }}" required
                                class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-neutral-900 focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-200 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white" />
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Enrollment Start') }}</label>
                            <input type="date" name="enrollment_start_date" value="{{ $semester->enrollment_start_date?->format('Y-m-d') }}"
                                class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-neutral-900 focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-200 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white" />
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Enrollment End') }}</label>
                            <input type="date" name="enrollment_end_date" value="{{ $semester->enrollment_end_date?->format('Y-m-d') }}"
                                class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-neutral-900 focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-200 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white" />
                        </div>
                    </div>
                </div>
                <div class="mt-6 flex gap-3">
                    <button type="button" onclick="document.getElementById('edit-semester-{{ $semester->id }}').close()" class="flex-1 justify-center rounded-lg px-4 py-2 text-neutral-600 hover:bg-neutral-100 dark:text-neutral-400 dark:hover:bg-neutral-800">
                        {{ __('Cancel') }}
                    </button>
                    <x-button.submit loading-text="{{ __('Updating...') }}" class="flex-1">{{ __('Update Semester') }}</x-button.submit>
                </div>
            </form>
        </dialog>
        @empty
        @endforelse
    @empty
    @endforelse
</x-layouts::app>
