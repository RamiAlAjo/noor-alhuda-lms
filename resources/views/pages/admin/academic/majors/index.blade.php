<x-layouts::app :title="__('Majors')">
    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-neutral-900 dark:text-neutral-100">{{ __('Majors') }}</h1>
            <p class="mt-1 text-neutral-500 dark:text-neutral-400">{{ __('Manage majors within departments') }}</p>
        </div>
        <flux:button variant="primary" onclick="document.getElementById('create-major-modal').showModal()">
            <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            {{ __('Add Major') }}
        </flux:button>
    </div>

    <!-- Major Table -->
    <div class="rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-neutral-50 text-neutral-500 dark:bg-neutral-700 dark:text-neutral-400">
                    <tr>
                        <th class="px-6 py-3">{{ __('Code') }}</th>
                        <th class="px-6 py-3">{{ __('Major Name') }}</th>
                        <th class="px-6 py-3">{{ __('Department') }}</th>
                        <th class="px-6 py-3">{{ __('Faculty') }}</th>
                        <th class="px-6 py-3">{{ __('Degree') }}</th>
                        <th class="px-6 py-3">{{ __('Years of Study') }}</th>
                        <th class="px-6 py-3 text-right">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                    @forelse($majors as $major)
                    <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-700/50">
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center rounded-full bg-neutral-100 px-2.5 py-0.5 text-xs font-medium text-neutral-800 dark:bg-neutral-700 dark:text-neutral-200">
                                {{ $major->code }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div>
                                <p class="font-medium text-neutral-900 dark:text-neutral-100">{{ $major->name }}</p>
                                @if($major->name_ar)
                                <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ $major->name_ar }}</p>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 text-neutral-600 dark:text-neutral-400">{{ $major->department->name ?? '-' }}</td>
                        <td class="px-6 py-4 text-neutral-600 dark:text-neutral-400">{{ $major->department?->faculty?->name ?? '-' }}</td>
                        <td class="px-6 py-4">
                            @switch($major->degree)
                                @case('bachelor')
                                <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                    {{ __('Bachelor') }}
                                </span>
                                @break
                                @case('master')
                                <span class="inline-flex items-center rounded-full bg-purple-100 px-2.5 py-0.5 text-xs font-medium text-purple-800 dark:bg-purple-900 dark:text-purple-200">
                                    {{ __('Master') }}
                                </span>
                                @break
                                @case('phd')
                                <span class="inline-flex items-center rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-medium text-amber-800 dark:bg-amber-900 dark:text-amber-200">
                                    {{ __('PhD') }}
                                </span>
                                @break
                                @default
                                <span class="inline-flex items-center rounded-full bg-neutral-100 px-2.5 py-0.5 text-xs font-medium text-neutral-800 dark:bg-neutral-700 dark:text-neutral-200">
                                    {{ __($major->degree) }}
                                </span>
                            @endswitch
                        </td>
                        <td class="px-6 py-4 text-neutral-600 dark:text-neutral-400">{{ $major->years }}</td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <flux:button size="sm" variant="ghost" onclick="document.getElementById('edit-major-{{ $major->id }}').showModal()">
                                    {{ __('Edit') }}
                                </flux:button>
                                <form method="POST" action="{{ route('admin.academic.majors.destroy', $major) }}">
                                    @csrf
                                    @method('DELETE')
                                    <x-button.submit size="sm" variant="danger" loading-text="{{ __('Deleting...') }}" confirm="{{ __('Are you sure you want to delete this major?') }}">
                                        {{ __('Delete') }}
                                    </x-button.submit>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-neutral-100 dark:bg-neutral-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="size-8 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                    </svg>
                                </div>
                                <h3 class="text-lg font-medium text-neutral-900 dark:text-neutral-100">{{ __('No majors') }}</h3>
                                <p class="text-neutral-500 dark:text-neutral-400">{{ __('Create your first major to get started') }}</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Create Major Modal -->
    <dialog id="create-major-modal" class="rounded-xl shadow-2xl backdrop:bg-black/60 p-0 w-full max-w-md">
        <div class="overflow-hidden bg-gradient-to-r from-emerald-600 via-teal-600 to-cyan-600 px-6 py-5">
            <div class="absolute -right-8 -top-8 h-24 w-24 rounded-full bg-white/10"></div>
            <div class="absolute -bottom-4 -left-4 h-16 w-16 rounded-full bg-white/10"></div>
            <div class="relative flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-white/20">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
                <div class="flex-1">
                    <h2 class="text-xl font-bold text-white">{{ __('Create New Major') }}</h2>
                    <p class="text-sm text-emerald-100">{{ __('Add a new major to a department') }}</p>
                </div>
                <button onclick="document.getElementById('create-major-modal').close()" class="rounded-full bg-white/20 p-2 text-white transition hover:bg-white/30">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
        <form method="POST" action="{{ route('admin.academic.majors.store') }}" class="p-6">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="mb-1 block text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Department') }}</label>
                    <select name="department_id" required class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-neutral-900 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-200 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white">
                        <option value="">{{ __('Select Department') }}</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Major Name') }}</label>
                    <input type="text" name="name" required placeholder="e.g., Computer Science"
                        class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-neutral-900 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-200 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white" />
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Arabic Name') }}</label>
                    <input type="text" name="name_ar" placeholder="e.g., علوم الحاسب الآلي"
                        class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-neutral-900 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-200 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white" />
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Code') }}</label>
                    <input type="text" name="code" required placeholder="e.g., CS"
                        class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-neutral-900 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-200 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white" />
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Degree') }}</label>
                    <select name="degree" required class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-neutral-900 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-200 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white">
                        <option value="bachelor">{{ __('Bachelor') }}</option>
                        <option value="master">{{ __('Master') }}</option>
                        <option value="phd">{{ __('PhD') }}</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Years of Study') }}</label>
                    <input type="number" name="years" required min="1" max="10" placeholder="e.g., 4"
                        class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-neutral-900 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-200 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white" />
                </div>
            </div>
            <div class="mt-6 flex gap-3">
                <button type="button" onclick="document.getElementById('create-major-modal').close()" class="flex-1 justify-center rounded-lg px-4 py-2 text-neutral-600 hover:bg-neutral-100 dark:text-neutral-400 dark:hover:bg-neutral-800">
                    {{ __('Cancel') }}
                </button>
                <x-button.submit loading-text="{{ __('Creating...') }}" class="flex-1">
                    {{ __('Create Major') }}
                </x-button.submit>
            </div>
        </form>
    </dialog>

    <!-- Edit Major Modals -->
    @forelse($majors as $major)
    <dialog id="edit-major-{{ $major->id }}" class="rounded-xl shadow-2xl backdrop:bg-black/60 p-0 w-full max-w-md">
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
                    <h2 class="text-xl font-bold text-white">{{ __('Edit Major') }}</h2>
                    <p class="text-sm text-amber-100">{{ __('Update major information') }}</p>
                </div>
                <button onclick="document.getElementById('edit-major-{{ $major->id }}').close()" class="rounded-full bg-white/20 p-2 text-white transition hover:bg-white/30">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
        <form method="POST" action="{{ route('admin.academic.majors.update', $major) }}" class="p-6">
            @csrf
            @method('PUT')
            <div class="space-y-4">
                <div>
                    <label class="mb-1 block text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Department') }}</label>
                    <select name="department_id" required class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-neutral-900 focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-200 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white">
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}" {{ $major->department_id == $department->id ? 'selected' : '' }}>{{ $department->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Major Name') }}</label>
                    <input type="text" name="name" value="{{ $major->name }}" required
                        class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-neutral-900 focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-200 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white" />
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Arabic Name') }}</label>
                    <input type="text" name="name_ar" value="{{ $major->name_ar }}" placeholder="e.g., علوم الحاسوب"
                        class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-neutral-900 focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-200 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white" />
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Code') }}</label>
                    <input type="text" name="code" value="{{ $major->code }}" required
                        class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-neutral-900 focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-200 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white" />
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Degree') }}</label>
                    <select name="degree" required class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-neutral-900 focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-200 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white">
                        <option value="bachelor" {{ $major->degree == 'bachelor' ? 'selected' : '' }}>{{ __('Bachelor') }}</option>
                        <option value="master" {{ $major->degree == 'master' ? 'selected' : '' }}>{{ __('Master') }}</option>
                        <option value="phd" {{ $major->degree == 'phd' ? 'selected' : '' }}>{{ __('PhD') }}</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Years of Study') }}</label>
                    <input type="number" name="years" value="{{ $major->years }}" min="1" max="10" required
                        class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-neutral-900 focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-200 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white" />
                </div>
            </div>
            <div class="mt-6 flex gap-3">
                <button type="button" onclick="document.getElementById('edit-major-{{ $major->id }}').close()" class="flex-1 justify-center rounded-lg px-4 py-2 text-neutral-600 hover:bg-neutral-100 dark:text-neutral-400 dark:hover:bg-neutral-800">
                    {{ __('Cancel') }}
                </button>
                <x-button.submit loading-text="{{ __('Updating...') }}" class="flex-1">
                    {{ __('Update Major') }}
                </x-button.submit>
            </div>
        </form>
    </dialog>
    @empty
    @endforelse
</x-layouts::app>
