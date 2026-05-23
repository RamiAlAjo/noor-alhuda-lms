<x-layouts::app :title="__('Faculties')">
    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-neutral-900 dark:text-neutral-100">{{ __('Faculties') }}</h1>
            <p class="mt-1 text-neutral-500 dark:text-neutral-400">{{ __('Manage faculties and departments') }}</p>
        </div>
        <flux:button variant="primary" onclick="document.getElementById('create-faculty-modal').showModal()">
            <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            {{ __('Add Faculty') }}
        </flux:button>
    </div>

    <!-- Faculty Cards -->
    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
        @forelse($faculties as $faculty)
        <div class="group relative overflow-hidden rounded-2xl border border-neutral-200 bg-white shadow-sm transition-all duration-300 hover:shadow-lg dark:border-neutral-700 dark:bg-neutral-800">
            <!-- Gradient Bar -->
            <div class="h-1 bg-gradient-to-r from-blue-500 via-indigo-500 to-purple-500"></div>

            <div class="p-6">
                <div class="mb-4">
                    <h3 class="text-lg font-bold text-neutral-900 dark:text-neutral-100">{{ $faculty->name }}</h3>
                    @if($faculty->name_ar)
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ $faculty->name_ar }}</p>
                    @endif
                </div>

                <div class="space-y-2 text-sm">
                    <div class="flex items-center gap-2 text-neutral-600 dark:text-neutral-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
                        </svg>
                        <span>{{ __('Code') }}: {{ $faculty->code }}</span>
                    </div>
                    @if($faculty->dean_name)
                    <div class="flex items-center gap-2 text-neutral-600 dark:text-neutral-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <span>{{ __('Dean') }}: {{ $faculty->dean_name }}</span>
                    </div>
                    @endif
                    <div class="flex items-center gap-2 text-neutral-600 dark:text-neutral-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5" />
                        </svg>
                        <span>{{ __('Departments') }}: {{ $faculty->departments->count() }}</span>
                    </div>
                </div>

                <div class="mt-4 flex items-center gap-2">
                    <flux:button size="sm" variant="ghost" onclick="document.getElementById('edit-faculty-{{ $faculty->id }}').showModal()" class="flex-1 justify-center">
                        {{ __('Edit') }}
                    </flux:button>
                    <form method="POST" action="{{ route('admin.academic.faculties.destroy', $faculty) }}">
                        @csrf
                        @method('DELETE')
                        <x-button.submit size="sm" variant="danger" loading-text="{{ __('Deleting...') }}" confirm="{{ __('Are you sure you want to delete this faculty?') }}">
                            {{ __('Delete') }}
                        </x-button.submit>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full">
            <div class="flex flex-col items-center justify-center rounded-2xl border-2 border-dashed border-neutral-300 py-16 dark:border-neutral-700">
                <div class="mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-neutral-100 dark:bg-neutral-800">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-10 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-neutral-900 dark:text-neutral-100">{{ __('No faculties') }}</h3>
                <p class="mt-2 text-neutral-500 dark:text-neutral-400">{{ __('Create your first faculty to get started') }}</p>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Create Faculty Modal -->
    <dialog id="create-faculty-modal" class="rounded-xl shadow-2xl backdrop:bg-black/60 p-0 w-full max-w-md">
        <div class="overflow-hidden bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 px-6 py-5">
            <div class="absolute -right-8 -top-8 h-24 w-24 rounded-full bg-white/10"></div>
            <div class="absolute -bottom-4 -left-4 h-16 w-16 rounded-full bg-white/10"></div>
            <div class="relative flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-white/20">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 2 2v0 00-16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <div class="flex-1">
                    <h2 class="text-xl font-bold text-white">{{ __('Create New Faculty') }}</h2>
                    <p class="text-sm text-indigo-100">{{ __('Add a new faculty to the system') }}</p>
                </div>
                <button onclick="document.getElementById('create-faculty-modal').close()" class="rounded-full bg-white/20 p-2 text-white transition hover:bg-white/30">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
        <form method="POST" action="{{ route('admin.academic.faculties.store') }}" class="p-6">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="mb-1 block text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Faculty Name') }}</label>
                    <input type="text" name="name" required placeholder="e.g., Faculty of Engineering"
                        class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-neutral-900 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white" />
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Arabic Name') }}</label>
                    <input type="text" name="name_ar" placeholder="e.g., كلية الهندسة"
                        class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-neutral-900 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white" />
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Code') }}</label>
                    <input type="text" name="code" required placeholder="e.g., ENG"
                        class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-neutral-900 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white" />
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Dean Name') }}</label>
                    <input type="text" name="dean_name" placeholder="e.g., Dr. John Smith"
                        class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-neutral-900 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white" />
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Email') }}</label>
                    <input type="email" name="email" placeholder="e.g., engineering@university.edu"
                        class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-neutral-900 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white" />
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Phone') }}</label>
                    <input type="text" name="phone" placeholder="e.g., +962-6-1234567"
                        class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-neutral-900 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white" />
                </div>
            </div>
            <div class="mt-6 flex gap-3">
                <button type="button" onclick="document.getElementById('create-faculty-modal').close()" class="flex-1 justify-center rounded-lg px-4 py-2 text-neutral-600 hover:bg-neutral-100 dark:text-neutral-400 dark:hover:bg-neutral-800">{{ __('Cancel') }}</button>
                <x-button.submit loading-text="{{ __('Creating...') }}" class="flex-1">{{ __('Create Faculty') }}</x-button.submit>
            </div>
        </form>
    </dialog>

    <!-- Edit Faculty Modals -->
    @forelse($faculties as $faculty)
    <dialog id="edit-faculty-{{ $faculty->id }}" class="rounded-xl shadow-2xl backdrop:bg-black/60 p-0 w-full max-w-md">
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
                    <h2 class="text-xl font-bold text-white">{{ __('Edit Faculty') }}</h2>
                    <p class="text-sm text-amber-100">{{ __('Update faculty information') }}</p>
                </div>
                <button onclick="document.getElementById('edit-faculty-{{ $faculty->id }}').close()" class="rounded-full bg-white/20 p-2 text-white transition hover:bg-white/30">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
        <form method="POST" action="{{ route('admin.academic.faculties.update', $faculty) }}" class="p-6">
            @csrf
            @method('PUT')
            <div class="space-y-4">
                <div>
                    <label class="mb-1 block text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Faculty Name') }}</label>
                    <input type="text" name="name" value="{{ $faculty->name }}" required
                        class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-neutral-900 focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-200 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white" />
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Arabic Name') }}</label>
                    <input type="text" name="name_ar" value="{{ $faculty->name_ar }}" placeholder="e.g., كلية الهندسة"
                        class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-neutral-900 focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-200 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white" />
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Code') }}</label>
                    <input type="text" name="code" value="{{ $faculty->code }}" required
                        class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-neutral-900 focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-200 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white" />
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Dean Name') }}</label>
                    <input type="text" name="dean_name" value="{{ $faculty->dean_name }}" placeholder="e.g., Dr. John Smith"
                        class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-neutral-900 focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-200 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white" />
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Email') }}</label>
                    <input type="email" name="email" value="{{ $faculty->email }}" placeholder="e.g., engineering@university.edu"
                        class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-neutral-900 focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-200 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white" />
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Phone') }}</label>
                    <input type="text" name="phone" value="{{ $faculty->phone }}" placeholder="e.g., +962-6-1234567"
                        class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-neutral-900 focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-200 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white" />
                </div>
            </div>
            <div class="mt-6 flex gap-3">
                <button type="button" onclick="document.getElementById('edit-faculty-{{ $faculty->id }}').close()" class="flex-1 justify-center rounded-lg px-4 py-2 text-neutral-600 hover:bg-neutral-100 dark:text-neutral-400 dark:hover:bg-neutral-800">
                    {{ __('Cancel') }}
                </button>
                <x-button.submit loading-text="{{ __('Updating...') }}" class="flex-1">{{ __('Update Faculty') }}</x-button.submit>
            </div>
        </form>
    </dialog>
    @empty
    @endforelse
</x-layouts::app>
