{{--
    =============================================================================
    TEACHER DASHBOARD VIEW
    =============================================================================

    Purpose: Main dashboard for teacher users showing their courses, students,
    and quick access to teaching management features.

    Route: teacher.dashboard
    Controller: Teacher\DashboardController@index

    Components:
    - Hero section with key stats (courses, students, active sections, pending grades)
    - Quick actions (my courses, attendance, quizzes, schedule)
    - Course cards grid showing enrolled sections

    Required Data:
    - $courses: Collection of teacher's course offerings
    - $total_students: Total number of students across all courses
    - $pendingGradesCount: Number of pending grades to review

    =============================================================================
--}}
<x-layouts::app :title="__('Teacher Dashboard')">
    <!-- Hero Section -->
    <div class="mb-8 rounded-2xl bg-gradient-to-r from-emerald-500 via-teal-500 to-cyan-500 p-8 text-white shadow-xl">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold">{{ __('Welcome, Teacher!') }}</h1>
                <p class="mt-2 text-emerald-100">{{ __('Manage your courses and students') }}</p>
            </div>
            <div class="hidden text-8xl opacity-20">📚</div>
        </div>

        <!-- Quick Stats -->
        <div class="mt-6 grid grid-cols-3 gap-4 md:grid-cols-5">
            <div class="rounded-xl bg-white/20 backdrop-blur-sm p-4">
                <p class="text-sm text-emerald-100">{{ __('My Courses') }}</p>
                <p class="text-2xl font-bold">{{ $courses->count() }}</p>
            </div>
            <div class="rounded-xl bg-white/20 backdrop-blur-sm p-4">
                <p class="text-sm text-emerald-100">{{ __('Total Students') }}</p>
                <p class="text-2xl font-bold">{{ $total_students }}</p>
            </div>
            <div class="rounded-xl bg-white/20 backdrop-blur-sm p-4">
                <p class="text-sm text-emerald-100">{{ __('Active Sections') }}</p>
                <p class="text-2xl font-bold">{{ $courses->where('is_active', true)->count() }}</p>
            </div>
            <div class="rounded-xl bg-white/20 backdrop-blur-sm p-4">
                <p class="text-sm text-emerald-100">{{ __('Pending Grades') }}</p>
                <p class="text-2xl font-bold">{{ $pendingGradesCount }}</p>
            </div>
            <div class="rounded-xl bg-white/20 backdrop-blur-sm p-4">
                <p class="text-sm text-emerald-100">{{ __('Materials') }}</p>
                <p class="text-2xl font-bold">{{ $courses->sum(fn($c) => $c->materials->count()) }}</p>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="mb-6 grid grid-cols-2 gap-4 md:grid-cols-4">
        <a href="{{ route('teacher.courses.index') }}" class="group relative overflow-hidden rounded-xl border border-neutral-200 bg-white p-5 transition-all hover:shadow-lg dark:border-neutral-700 dark:bg-neutral-800 hover:border-emerald-300 dark:hover:border-emerald-700">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-50 to-indigo-50 opacity-0 transition-opacity group-hover:opacity-100 dark:from-blue-900/20 dark:to-indigo-900/20"></div>
            <div class="relative">
                <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100 text-blue-600 dark:bg-blue-900 dark:text-blue-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
                <h3 class="font-semibold text-neutral-900 dark:text-neutral-100">{{ __('My Courses') }}</h3>
            </div>
        </a>

        <a href="{{ route('teacher.courses.index') }}" class="group relative overflow-hidden rounded-xl border border-neutral-200 bg-white p-5 transition-all hover:shadow-lg dark:border-neutral-700 dark:bg-neutral-800 hover:border-green-300 dark:hover:border-green-700">
            <div class="absolute inset-0 bg-gradient-to-br from-green-50 to-emerald-50 opacity-0 transition-opacity group-hover:opacity-100 dark:from-green-900/20 dark:to-emerald-900/20"></div>
            <div class="relative">
                <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-lg bg-green-100 text-green-600 dark:bg-green-900 dark:text-green-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                </div>
                <h3 class="font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Attendance') }}</h3>
            </div>
        </a>

        <a href="{{ route('teacher.courses.index') }}" class="group relative overflow-hidden rounded-xl border border-neutral-200 bg-white p-5 transition-all hover:shadow-lg dark:border-neutral-700 dark:bg-neutral-800 hover:border-purple-300 dark:hover:border-purple-700">
            <div class="absolute inset-0 bg-gradient-to-br from-purple-50 to-pink-50 opacity-0 transition-opacity group-hover:opacity-100 dark:from-purple-900/20 dark:to-pink-900/20"></div>
            <div class="relative">
                <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-lg bg-purple-100 text-purple-600 dark:bg-purple-900 dark:text-purple-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                </div>
                <h3 class="font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Quizzes & Tests') }}</h3>
            </div>
        </a>

        <a href="{{ route('teacher.courses.index') }}" class="group relative overflow-hidden rounded-xl border border-neutral-200 bg-white p-5 transition-all hover:shadow-lg dark:border-neutral-700 dark:bg-neutral-800 hover:border-orange-300 dark:hover:border-orange-700">
            <div class="absolute inset-0 bg-gradient-to-br from-orange-50 to-amber-50 opacity-0 transition-opacity group-hover:opacity-100 dark:from-orange-900/20 dark:to-amber-900/20"></div>
            <div class="relative">
                <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-lg bg-orange-100 text-orange-600 dark:bg-orange-900 dark:text-orange-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <h3 class="font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Schedule') }}</h3>
            </div>
        </a>
    </div>

    <!-- My Courses Grid -->
    <div class="rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
        <div class="border-b border-neutral-200 px-6 py-4 dark:border-neutral-700">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-neutral-900 dark:text-neutral-100">{{ __('My Courses') }}</h2>
                <flux:button size="sm" variant="primary" :href="route('teacher.courses.index')">
                    {{ __('View All') }}
                </flux:button>
            </div>
        </div>

        <div class="grid gap-4 p-6 md:grid-cols-2 lg:grid-cols-3">
            @forelse($courses as $section)
            <a href="{{ route('teacher.courses.show', $section) }}" class="group relative overflow-hidden rounded-xl border border-neutral-200 p-5 transition-all hover:shadow-lg hover:border-indigo-300 dark:border-neutral-700 dark:hover:border-indigo-600">
                <!-- Course Color Bar -->
                <div class="absolute left-0 top-0 h-full w-1 bg-gradient-to-b from-indigo-500 to-purple-500"></div>

                <div class="pl-3">
                    <div class="mb-2 flex items-start justify-between">
                        <div>
                            <h3 class="font-bold text-neutral-900 dark:text-neutral-100 group-hover:text-indigo-600 dark:group-hover:text-indigo-400">
                                {{ $section->course?->name ?? __('Unknown Course') }}
                            </h3>
                            <p class="text-sm text-neutral-500 dark:text-neutral-400">
                                {{ __('Section') }} {{ $section->section_name }}
                            </p>
                        </div>
                        <span class="rounded-full bg-indigo-100 px-2 py-1 text-xs font-medium text-indigo-600 dark:bg-indigo-900 dark:text-indigo-300">
                            {{ $section->semester?->name ?? '' }}
                        </span>
                    </div>

                    <!-- Stats -->
                    <div class="mt-4 flex items-center gap-4 text-sm text-neutral-500 dark:text-neutral-400">
                        <div class="flex items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            {{ $section->enrolled_count }}/{{ $section->capacity }}
                        </div>
                        <div class="flex items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            {{ $section->materials->count() }}
                        </div>
                        <div class="flex items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            {{ $section->assessments->count() }}
                        </div>
                    </div>
                </div>
            </a>
            @empty
            <div class="col-span-full py-12 text-center">
                <div class="mb-4 flex justify-center">
                    <div class="rounded-full bg-neutral-100 p-4 dark:bg-neutral-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-8 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                </div>
                <h3 class="text-lg font-medium text-neutral-900 dark:text-neutral-100">{{ __('No courses assigned yet') }}</h3>
                <p class="mt-1 text-neutral-500 dark:text-neutral-400">{{ __('Contact your administrator to get courses assigned') }}</p>
            </div>
            @endforelse
        </div>
    </div>
</x-layouts::app>
