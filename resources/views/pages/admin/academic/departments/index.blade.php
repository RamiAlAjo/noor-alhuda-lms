<x-layouts::app :title="__('Departments')">
    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-neutral-900 dark:text-neutral-100">{{ __('Departments') }}</h1>
            <p class="mt-1 text-neutral-500 dark:text-neutral-400">{{ __('Manage departments within faculties') }}</p>
        </div>
        <flux:button variant="primary" onclick="document.getElementById('create-department-modal').showModal()">
            <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            {{ __('Add Department') }}
        </flux:button>
    </div>

    <!-- Department Table -->
    <div class="rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-neutral-50 text-neutral-500 dark:bg-neutral-700 dark:text-neutral-400">
                    <tr>
                        <th class="px-6 py-3">{{ __('Code') }}</th>
                        <th class="px-6 py-3">{{ __('Department Name') }}</th>
                        <th class="px-6 py-3">{{ __('Faculty') }}</th>
                        <th class="px-6 py-3">{{ __('Majors') }}</th>
                        <th class="px-6 py-3">{{ __('Head') }}</th>
                        <th class="px-6 py-3 text-right">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                    @forelse($departments as $department)
                    <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-700/50">
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center rounded-full bg-neutral-100 px-2.5 py-0.5 text-xs font-medium text-neutral-800 dark:bg-neutral-700 dark:text-neutral-200">
                                {{ $department->code }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div>
                                <p class="font-medium text-neutral-900 dark:text-neutral-100">{{ $department->name }}</p>
                                @if($department->name_ar)
                                <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ $department->name_ar }}</p>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 text-neutral-600 dark:text-neutral-400">{{ $department->faculty?->name ?? '-' }}</td>
                        <td class="px-6 py-4 text-neutral-600 dark:text-neutral-400">{{ $department->majors->count() }}</td>
                        <td class="px-6 py-4 text-neutral-600 dark:text-neutral-400">{{ $department->head_name ?? '-' }}</td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <flux:button size="sm" variant="ghost" onclick="document.getElementById('edit-department-{{ $department->id }}').showModal()">
                                    {{ __('Edit') }}
                                </flux:button>
                                <form method="POST" action="{{ route('admin.academic.departments.destroy', $department) }}">
                                    @csrf
                                    @method('DELETE')
                                    <flux:button size="sm" variant="danger" type="submit" onclick="return confirm('{{ __('Are you sure you want to delete this department?') }}')">
                                        {{ __('Delete') }}
                                    </flux:button>
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
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5" />
                                    </svg>
                                </div>
                                <h3 class="text-lg font-medium text-neutral-900 dark:text-neutral-100">{{ __('No departments') }}</h3>
                                <p class="text-neutral-500 dark:text-neutral-400">{{ __('Create your first department to get started') }}</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Create Department Modal -->
    <dialog id="create-department-modal" class="rounded-xl shadow-2xl backdrop:bg-black/60 p-0 w-full max-w-md">
        <div class="overflow-hidden bg-gradient-to-r from-pink-600 via-purple-600 to-indigo-600 px-6 py-5">
            <div class="absolute -right-8 -top-8 h-24 w-24 rounded-full bg-white/10"></div>
            <div class="absolute -bottom-4 -left-4 h-16 w-16 rounded-full bg-white/10"></div>
            <div class="relative flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-white/20">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <div class="flex-1">
                    <h2 class="text-xl font-bold text-white">{{ __('Create New Department') }}</h2>
                    <p class="text-sm text-indigo-100">{{ __('Add a new department to a faculty') }}</p>
                </div>
                <button onclick="document.getElementById('create-department-modal').close()" class="rounded-full bg-white/20 p-2 text-white transition hover:bg-white/30">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
        <form method="POST" action="{{ route('admin.academic.departments.store') }}" class="p-6">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="mb-1 block text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Faculty') }}</label>
                    <select name="faculty_id" required class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-neutral-900 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white">
                        <option value="">{{ __('Select Faculty') }}</option>
                        @foreach($faculties as $faculty)
                            <option value="{{ $faculty->id }}">{{ $faculty->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Department Name') }}</label>
                    <input type="text" name="name" required placeholder="e.g., Computer Science"
                        class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-neutral-900 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white" />
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Arabic Name') }}</label>
                    <input type="text" name="name_ar" placeholder="e.g., علوم الحاسب"
                        class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-neutral-900 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white" />
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Code') }}</label>
                    <input type="text" name="code" required placeholder="e.g., CS"
                        class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-neutral-900 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white" />
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Department Head') }}</label>
                    <input type="text" name="head_name" placeholder="e.g., Dr. Jane Doe"
                        class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-neutral-900 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white" />
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Email') }}</label>
                    <input type="email" name="email" placeholder="e.g., cs@university.edu"
                        class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-neutral-900 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white" />
                </div>
            </div>
            <div class="mt-6 flex gap-3">
                <button type="button" onclick="document.getElementById('create-department-modal').close()" class="flex-1 justify-center rounded-lg px-4 py-2 text-neutral-600 hover:bg-neutral-100 dark:text-neutral-400 dark:hover:bg-neutral-800">
                    {{ __('Cancel') }}
                </button>
                <button type="submit" class="flex-1 justify-center rounded-lg bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700">
                    {{ __('Create Department') }}
                </button>
            </div>
        </form>
    </dialog>

    <!-- Edit Department Modals -->
    @forelse($departments as $department)
    <dialog id="edit-department-{{ $department->id }}" class="rounded-xl shadow-2xl backdrop:bg-black/60 p-0 w-full max-w-md">
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
                    <h2 class="text-xl font-bold text-white">{{ __('Edit Department') }}</h2>
                    <p class="text-sm text-amber-100">{{ __('Update department information') }}</p>
                </div>
                <button onclick="document.getElementById('edit-department-{{ $department->id }}').close()" class="rounded-full bg-white/20 p-2 text-white transition hover:bg-white/30">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
        <form method="POST" action="{{ route('admin.academic.departments.update', $department) }}" class="p-6">
            @csrf
            @method('PUT')
            <div class="space-y-4">
                <div>
                    <label class="mb-1 block text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Faculty') }}</label>
                    <select name="faculty_id" required class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-neutral-900 focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-200 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white">
                        @foreach($faculties as $faculty)
                            <option value="{{ $faculty->id }}" {{ $department->faculty_id == $faculty->id ? 'selected' : '' }}>{{ $faculty->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Department Name') }}</label>
                    <input type="text" name="name" value="{{ $department->name }}" required
                        class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-neutral-900 focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-200 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white" />
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Arabic Name') }}</label>
                    <input type="text" name="name_ar" value="{{ $department->name_ar }}" placeholder="e.g., قسم علوم الحاسوب"
                        class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-neutral-900 focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-200 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white" />
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Code') }}</label>
                    <input type="text" name="code" value="{{ $department->code }}" required
                        class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-neutral-900 focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-200 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white" />
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Head Name') }}</label>
                    <input type="text" name="head_name" value="{{ $department->head_name }}" placeholder="e.g., Dr. John Smith"
                        class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-neutral-900 focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-200 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white" />
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Email') }}</label>
                    <input type="email" name="email" value="{{ $department->email }}" placeholder="e.g., cs@university.edu"
                        class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-neutral-900 focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-200 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white" />
                </div>
            </div>
            <div class="mt-6 flex gap-3">
                <button type="button" onclick="document.getElementById('edit-department-{{ $department->id }}').close()" class="flex-1 justify-center rounded-lg px-4 py-2 text-neutral-600 hover:bg-neutral-100 dark:text-neutral-400 dark:hover:bg-neutral-800">
                    {{ __('Cancel') }}
                </button>
                <button type="submit" class="flex-1 justify-center rounded-lg px-4 py-2 text-white" style="background: linear-gradient(to right, #f59e0b, #ef4444); border: none;">
                    {{ __('Update Department') }}
                </button>
            </div>
        </form>
    </dialog>
    @empty
    @endforelse
</x-layouts::app>
