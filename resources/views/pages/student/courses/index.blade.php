{{--
    =============================================================================
    STUDENT COURSES INDEX VIEW
    =============================================================================

    Purpose: Display student's enrolled courses with filtering, sorting, and stats.

    Route: student.courses.index
    Controller: Student\CourseController@index

    Components:
    - Header with "Browse Courses" button
    - Search and Filters: Search, Status filter (All/Active/Pending/Dropped), Sort options
    - Stats cards: Enrolled Courses, Credits, Current GPA, Pending Fees
    - Course cards grid with:
      * Course name, code, section number
      * Status badge
      * Progress bar (if available)
      * Teacher name, semester, schedule info
      * Action buttons: View Course, Drop Course
    - Empty state with browse button when no enrollments

    Required Data:
    - $enrollments: Collection of student's Enrollment models
    - $gpa: Student's current GPA
    - $pendingFees: Number of pending fee invoices

    Dependencies:
    - route('student.courses.browse') - Browse available courses
    - route('student.courses.show', $offering) - View course details
    - route('student.courses.drop', $enrollment) - Drop course
    - request('search') - Search filter
    - request('filter') - Status filter
    - request('sort') - Sort option
    - $enrollment->offering->course->name - Course name
    - $enrollment->offering->teacher->full_name - Teacher name

    =============================================================================
--}}
<x-layouts::app :title="__('My Courses')">
    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-neutral-900 dark:text-neutral-100">{{ __('My Courses') }}</h1>
            <p class="mt-1 text-neutral-500 dark:text-neutral-400">{{ __('View and manage your enrolled courses') }}</p>
        </div>
        <flux:button :href="route('student.courses.browse')" variant="primary">
            <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            {{ __('Browse Courses') }}
        </flux:button>
    </div>

    <!-- Search and Filters -->
    <div class="mb-6 rounded-xl border border-neutral-200 bg-white p-4 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
        <form method="GET" class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div class="flex flex-1 gap-4">
                <!-- Search -->
                <div class="relative flex-1 max-w-md">
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="{{ __('Search courses...') }}"
                        class="w-full rounded-lg border border-neutral-300 bg-white px-4 py-2 pl-10 text-neutral-900 focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500 dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-100"
                    >
                    <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-2.5 size-5 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>

                <!-- Status Filter -->
                <select
                    name="filter"
                    class="rounded-lg border border-neutral-300 bg-white px-4 py-2 text-neutral-900 focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500 dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-100"
                >
                    <option value="">{{ __('All Status') }}</option>
                    <option value="approved" {{ request('filter') === 'approved' ? 'selected' : '' }}>{{ __('Active') }}</option>
                    <option value="pending" {{ request('filter') === 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                    <option value="dropped" {{ request('filter') === 'dropped' ? 'selected' : '' }}>{{ __('Dropped') }}</option>
                </select>
            </div>

            <div class="flex gap-2">
                <!-- Sort -->
                <select
                    name="sort"
                    class="rounded-lg border border-neutral-300 bg-white px-4 py-2 text-neutral-900 focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500 dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-100"
                >
                    <option value="newest" {{ request('sort', 'newest') === 'newest' ? 'selected' : '' }}>{{ __('Newest First') }}</option>
                    <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>{{ __('Oldest First') }}</option>
                    <option value="name_asc" {{ request('sort') === 'name_asc' ? 'selected' : '' }}>{{ __('Name A-Z') }}</option>
                    <option value="name_desc" {{ request('sort') === 'name_desc' ? 'selected' : '' }}>{{ __('Name Z-A') }}</option>
                </select>

                <x-button.submit loading-text="Applying..." variant="primary">
                    Filter
                </x-button.submit>

                @if(request()->anyFilled(['search', 'filter', 'sort']))
                <flux:button :href="route('student.courses.index')" variant="ghost">
                    {{ __('Clear') }}
                </flux:button>
                @endif
            </div>
        </form>
    </div>

    <!-- Stats Cards -->
    <div class="mb-6 grid gap-4 md:grid-cols-4">
        <div class="rounded-xl border border-neutral-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-violet-100 text-violet-600 dark:bg-violet-900 dark:text-violet-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Enrolled Courses') }}</p>
                    <p class="text-xl font-bold text-neutral-900 dark:text-neutral-100">{{ $enrollments->count() }}</p>
                </div>
            </div>
        </div>
        <div class="rounded-xl border border-neutral-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-green-100 text-green-600 dark:bg-green-900 dark:text-green-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Credits') }}</p>
                    <p class="text-xl font-bold text-neutral-900 dark:text-neutral-100">{{ $enrollments->sum(fn($e) => $e->offering?->course?->credits ?? 0) }}</p>
                </div>
            </div>
        </div>
        <div class="rounded-xl border border-neutral-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-amber-100 text-amber-600 dark:bg-amber-900 dark:text-amber-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Current GPA') }}</p>
                    <p class="text-xl font-bold text-neutral-900 dark:text-neutral-100">{{ number_format($gpa ?? 0, 2) }}</p>
                </div>
            </div>
        </div>
        <div class="rounded-xl border border-neutral-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-rose-100 text-rose-600 dark:bg-rose-900 dark:text-rose-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Pending Fees') }}</p>
                    <p class="text-xl font-bold text-neutral-900 dark:text-neutral-100">{{ $pendingFees ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Bulk Actions (when courses are selected) -->
    <div id="bulk-actions" class="mb-6 hidden rounded-xl border border-neutral-200 bg-blue-50 p-4 dark:border-neutral-700 dark:bg-blue-900/20">
        <div class="flex items-center justify-between">
            <span class="text-sm font-medium text-blue-900 dark:text-blue-100">
                <span id="selected-count">0</span> {{ __('courses selected') }}
            </span>
            <div class="flex gap-2">
                <flux:button size="sm" variant="outline" onclick="clearSelection()">
                    {{ __('Clear Selection') }}
                </flux:button>
                <flux:button size="sm" variant="primary" onclick="bulkExport()">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    {{ __('Export Data') }}
                </flux:button>
            </div>
        </div>
    </div>

    <!-- Course Cards Grid -->
    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
        @forelse($enrollments as $enrollment)
        <div class="group relative overflow-hidden rounded-2xl border border-neutral-200 bg-white shadow-sm transition-all duration-300 hover:shadow-lg dark:border-neutral-700 dark:bg-neutral-800">
            <!-- Selection Checkbox -->
            <div class="absolute right-4 top-4 z-10">
                <input
                    type="checkbox"
                    class="course-checkbox size-4 rounded border-neutral-300 text-violet-600 focus:ring-violet-500 dark:border-neutral-600 dark:bg-neutral-700"
                    value="{{ $enrollment->id }}"
                    onchange="updateBulkActions()"
                >
            </div>

            <!-- Gradient Bar -->
            <div class="h-1 bg-gradient-to-r from-violet-500 via-purple-500 to-fuchsia-500"></div>

            <div class="p-6">
                <!-- Course Header -->
                <div class="mb-4 flex items-start justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-neutral-900 dark:text-neutral-100">{{ $enrollment->offering?->course?->name ?? __('Unknown Course') }}</h3>
                        <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ $enrollment->offering?->course?->code ?? '' }} - {{ $enrollment->offering?->section_name ?? '' }}</p>
                    </div>
                    <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-700 dark:bg-green-900 dark:text-green-300">
                        {{ __($enrollment->status) }}
                    </span>
                </div>

                <!-- Progress Bar -->
                @if(isset($enrollment->progress))
                <div class="mb-4">
                    <div class="flex justify-between text-sm">
                        <span class="text-neutral-500 dark:text-neutral-400">{{ __('Progress') }}</span>
                        <span class="font-medium text-neutral-900 dark:text-neutral-100">{{ $enrollment->progress }}%</span>
                    </div>
                    <div class="mt-1 h-2 w-full overflow-hidden rounded-full bg-neutral-200 dark:bg-neutral-700">
                        <div class="h-full rounded-full bg-gradient-to-r from-violet-500 to-fuchsia-500 transition-all" style="width: {{ $enrollment->progress }}%"></div>
                    </div>
                </div>
                @endif

                <!-- Details -->
                <div class="mb-4 space-y-2 text-sm">
                    <div class="flex items-center gap-2 text-neutral-600 dark:text-neutral-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        {{ $enrollment->offering?->teacher?->full_name ?? __('Not assigned') }}
                    </div>
                    <div class="flex items-center gap-2 text-neutral-600 dark:text-neutral-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        {{ $enrollment->offering?->semester?->name ?? __('Active') }}
                    </div>
                    @if($enrollment->offering?->schedule)
                    <div class="flex items-center gap-2 text-neutral-600 dark:text-neutral-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ $enrollment->offering?->schedule }}
                    </div>
                    @endif
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center gap-2">
                    @if($enrollment->offering)
                    <flux:button size="sm" variant="primary" :href="route('student.courses.show', $enrollment->offering)" class="flex-1 justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        {{ __('View Course') }}
                    </flux:button>
                    @endif
                    @if($enrollment->offering)
                    <form method="POST" action="{{ route('student.courses.drop', $enrollment) }}">
                        @csrf
                        @method('DELETE')
                        <flux:button size="sm" variant="danger" type="submit" onclick="return confirm('{{ __('Are you sure you want to drop this course?') }}')" class="justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6" />
                            </svg>
                        </flux:button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full">
            <div class="flex flex-col items-center justify-center rounded-2xl border-2 border-dashed border-neutral-300 py-16 dark:border-neutral-700">
                <div class="mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-neutral-100 dark:bg-neutral-800">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-10 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-neutral-900 dark:text-neutral-100">{{ __('No enrolled courses') }}</h3>
                <p class="mt-2 text-neutral-500 dark:text-neutral-400">{{ __('Browse available courses and enroll') }}</p>
                <flux:button :href="route('student.courses.browse')" variant="primary" class="mt-4">
                    {{ __('Browse Courses') }}
                </flux:button>
            </div>
        </div>
        @endforelse
    </div>

    <script>
        function updateBulkActions() {
            const checkboxes = document.querySelectorAll('.course-checkbox:checked');
            const bulkActions = document.getElementById('bulk-actions');
            const selectedCount = document.getElementById('selected-count');

            if (checkboxes.length > 0) {
                bulkActions.classList.remove('hidden');
                selectedCount.textContent = checkboxes.length;
            } else {
                bulkActions.classList.add('hidden');
            }
        }

        function clearSelection() {
            document.querySelectorAll('.course-checkbox').forEach(checkbox => {
                checkbox.checked = false;
            });
            updateBulkActions();
        }

        function bulkExport() {
            const selectedCourses = Array.from(document.querySelectorAll('.course-checkbox:checked')).map(cb => cb.value);

            if (selectedCourses.length === 0) {
                alert('{{ __("Please select at least one course") }}');
                return;
            }

            // Create form and submit
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("student.courses.bulk-export") }}';

            // Add CSRF token
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            form.appendChild(csrfToken);

            // Add selected courses
            selectedCourses.forEach(courseId => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'courses[]';
                input.value = courseId;
                form.appendChild(input);
            });

            document.body.appendChild(form);
            form.submit();
        }
    </script>
</x-layouts::app>
