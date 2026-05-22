{{--
    =============================================================================
    ADMIN COURSES INDEX VIEW
    =============================================================================

    Purpose: List all courses in the system with stats and management options.

    Route: admin.courses.index
    Controller: Admin\CourseController@index

    Components:
    - Header with title and "Add Course" button
    - Stats cards: Total Courses, Active, Total Sections, Enrolled
    - Search bar for filtering courses
    - Courses table with: Course Code, Name, Credits, Department, Sections, Status
    - Action buttons (View, Edit)
    - Empty state when no courses found
    - Pagination

    Required Data:
    - $courses: Paginated collection of Course models
    - $totalEnrolled: Total number of enrolled students

    Dependencies:
    - route('admin.courses.create') - Create new course
    - route('admin.courses.show', $course) - View course details
    - route('admin.courses.edit', $course) - Edit course
    - $course->department->name - Course department
    - $course->sections - Course sections relationship
    - $course->is_active - Course active status

    =============================================================================
--}}
<x-layouts::app :title="__('Course Management')">
    <x-page-header
        :title="__('Course Management')"
        :description="__('Manage all courses and sections in your institution')"
    >
        <flux:button :href="route('admin.courses.create')" variant="primary" class="shadow-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            {{ __('Add Course') }}
        </flux:button>
    </x-page-header>

    @if (session('success'))
        <div class="mb-6 rounded-xl border border-green-200 bg-green-50 p-4 text-green-700 dark:border-green-800 dark:bg-green-900/20 dark:text-green-300">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-red-700 dark:border-red-800 dark:bg-red-900/20 dark:text-red-300">
            {{ session('error') }}
        </div>
    @endif

    <!-- Stats Cards -->
    <div class="mb-8 grid grid-cols-2 gap-4 md:grid-cols-4">
        <x-stat-card
            icon="book-open"
            :label="__('Total Courses')"
            :value="$courses->total()"
            color="blue"
        />
        <x-stat-card
            icon="check-circle"
            :label="__('Active')"
            :value="$courses->where('is_active', true)->count()"
            color="green"
        />
        <x-stat-card
            icon="folder"
            :label="__('Total Sections')"
            :value="$courses->sum(fn($c) => $c->sections->count())"
            color="purple"
        />
        <x-stat-card
            icon="users"
            :label="__('Enrolled')"
            :value="$totalEnrolled ?? 0"
            color="orange"
        />
    </div>

    <!-- Course List -->
    <div class="rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
        <div class="border-b border-neutral-200 px-6 py-4 dark:border-neutral-700">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('All Courses') }}</h2>
                <!-- Search -->
                <div class="relative">
                    <input type="text" placeholder="{{ __('Search courses...') }}" class="w-64 rounded-lg border border-neutral-300 py-2 pl-10 pr-4 text-sm focus:border-indigo-500 focus:outline-none dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-100">
                    <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-2.5 size-4 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-neutral-50 text-neutral-500 dark:bg-neutral-700 dark:text-neutral-400">
                    <tr>
                        <th class="px-6 py-3">{{ __('Course Code') }}</th>
                        <th class="px-6 py-3">{{ __('Course Name') }}</th>
                        <th class="px-6 py-3">{{ __('Credits') }}</th>
                        <th class="px-6 py-3">{{ __('Department') }}</th>
                        <th class="px-6 py-3">{{ __('Sections') }}</th>
                        <th class="px-6 py-3">{{ __('Status') }}</th>
                        <th class="px-6 py-3 text-right">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                    @forelse($courses as $course)
                    <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-700/50">
                        <td class="px-6 py-4">
                            <span class="font-mono text-sm font-medium text-indigo-600 dark:text-indigo-400">{{ $course->code }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-100 text-xs font-bold text-indigo-600 dark:bg-indigo-900 dark:text-indigo-300">
                                    {{ substr($course->name, 0, 2) }}
                                </div>
                                <span class="font-medium text-neutral-900 dark:text-neutral-100">{{ $course->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-neutral-600 dark:text-neutral-400">{{ $course->credits }}</td>
                        <td class="px-6 py-4 text-neutral-600 dark:text-neutral-400">{{ $course->department?->name ?? '-' }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center rounded-full bg-purple-100 px-2.5 py-0.5 text-xs font-medium text-purple-800 dark:bg-purple-900 dark:text-purple-200">
                                {{ $course->sections->count() }} {{ __('sections') }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @if($course->is_active)
                            <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800 dark:bg-green-900 dark:text-green-200">
                                {{ __('Active') }}
                            </span>
                            @else
                            <span class="inline-flex items-center rounded-full bg-neutral-100 px-2.5 py-0.5 text-xs font-medium text-neutral-800 dark:bg-neutral-700 dark:text-neutral-200">
                                {{ __('Inactive') }}
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <flux:button size="sm" variant="subtle" :href="route('admin.courses.show', $course)">
                                    {{ __('View') }}
                                </flux:button>
                                <flux:button size="sm" variant="ghost" :href="route('admin.courses.edit', $course)">
                                    {{ __('Edit') }}
                                </flux:button>
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
                                <h3 class="text-lg font-medium text-neutral-900 dark:text-neutral-100">{{ __('No courses found') }}</h3>
                                <p class="mt-1 text-neutral-500 dark:text-neutral-400">{{ __('Create your first course to get started') }}</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($courses->hasPages())
        <div class="border-t border-neutral-200 px-6 py-4 dark:border-neutral-700">
            {{ $courses->links() }}
        </div>
        @endif
    </div>
</x-layouts::app>
