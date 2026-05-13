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
        <div class="mt-6 grid grid-cols-2 gap-4 md:grid-cols-4 lg:grid-cols-6">
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
            <div class="rounded-xl bg-white/20 backdrop-blur-sm p-4">
                <p class="text-sm text-emerald-100">{{ __('Assessments') }}</p>
                <p class="text-2xl font-bold">{{ $courses->sum(fn($c) => $c->assessments->count()) }}</p>
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Attendance') }}</h3>
                <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('Take attendance for your classes') }}</p>
            </div>
        </a>

        <a href="{{ route('teacher.calendar') }}" class="group relative overflow-hidden rounded-xl border border-neutral-200 bg-white p-5 transition-all hover:shadow-lg dark:border-neutral-700 dark:bg-neutral-800 hover:border-orange-300 dark:hover:border-orange-700">
            <div class="absolute inset-0 bg-gradient-to-br from-orange-50 to-red-50 opacity-0 transition-opacity group-hover:opacity-100 dark:from-orange-900/20 dark:to-red-900/20"></div>
            <div class="relative">
                <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-lg bg-orange-100 text-orange-600 dark:bg-orange-900 dark:text-orange-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <h3 class="font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Calendar') }}</h3>
                <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('View schedules and deadlines') }}</p>
            </div>
        </a>

        <a href="{{ route('teacher.quizzes.all') }}" class="group relative overflow-hidden rounded-xl border border-neutral-200 bg-white p-5 transition-all hover:shadow-lg dark:border-neutral-700 dark:bg-neutral-800 hover:border-purple-300 dark:hover:border-purple-700">
            <div class="absolute inset-0 bg-gradient-to-br from-purple-50 to-pink-50 opacity-0 transition-opacity group-hover:opacity-100 dark:from-purple-900/20 dark:to-pink-900/20"></div>
            <div class="relative">
                <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-lg bg-purple-100 text-purple-600 dark:bg-purple-900 dark:text-purple-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                </div>
                <h3 class="font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Quizzes & Tests') }}</h3>
                <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('Create and manage assessments') }}</p>
            </div>
        </a>

        <a href="{{ route('teacher.messages') }}" class="group relative overflow-hidden rounded-xl border border-neutral-200 bg-white p-5 transition-all hover:shadow-lg dark:border-neutral-700 dark:bg-neutral-800 hover:border-cyan-300 dark:hover:border-cyan-700">
            <div class="absolute inset-0 bg-gradient-to-br from-cyan-50 to-blue-50 opacity-0 transition-opacity group-hover:opacity-100 dark:from-cyan-900/20 dark:to-blue-900/20"></div>
            <div class="relative">
                <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-lg bg-cyan-100 text-cyan-600 dark:bg-cyan-900 dark:text-cyan-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                </div>
                <h3 class="font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Messages') }}</h3>
                <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('Communicate with students') }}</p>
            </div>
        </a>

        <a href="{{ route('teacher.reports.index') }}" class="group relative overflow-hidden rounded-xl border border-neutral-200 bg-white p-5 transition-all hover:shadow-lg dark:border-neutral-700 dark:bg-neutral-800 hover:border-orange-300 dark:hover:border-orange-700">
            <div class="absolute inset-0 bg-gradient-to-br from-orange-50 to-amber-50 opacity-0 transition-opacity group-hover:opacity-100 dark:from-orange-900/20 dark:to-amber-900/20"></div>
            <div class="relative">
                <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-lg bg-orange-100 text-orange-600 dark:bg-orange-900 dark:text-orange-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
                <h3 class="font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Reports & Analytics') }}</h3>
                <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('View student progress and analytics') }}</p>
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
                            {{ $section->enrolled_count }}/{{ $section->max_students ?? '∞' }}
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

    <!-- Upcoming Deadlines & Recent Activity -->
    <div class="mt-8 grid gap-6 lg:grid-cols-2">
        <!-- Upcoming Assessments -->
        <div class="rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="border-b border-neutral-200 px-6 py-4 dark:border-neutral-700">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Upcoming Deadlines') }}</h2>
                    <flux:button size="sm" variant="ghost" :href="route('teacher.quizzes.all')">
                        {{ __('View All') }}
                    </flux:button>
                </div>
            </div>

            <div class="p-6">
                @if($upcomingAssessments->count() > 0)
                    <div class="space-y-4">
                        @foreach($upcomingAssessments as $assessment)
                        <div class="flex items-start gap-4 p-4 rounded-lg border border-neutral-200 dark:border-neutral-700">
                            <div class="flex-shrink-0">
                                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-red-100 text-red-600 dark:bg-red-900 dark:text-red-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1">
                                <h3 class="font-semibold text-neutral-900 dark:text-neutral-100">{{ $assessment->title }}</h3>
                                <p class="text-sm text-neutral-600 dark:text-neutral-400">
                                    {{ $assessment->offering->course->name }} - {{ $assessment->offering->name }}
                                </p>
                                <p class="text-sm text-neutral-500 dark:text-neutral-500 mt-1">
                                    {{ __('Due: :date at :time', [
                                        'date' => $assessment->due_date->locale(app()->getLocale())->isoFormat('MMM D, YYYY'),
                                        'time' => $assessment->due_date->format('g:i A')
                                    ]) }}
                                </p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <div class="flex justify-center mb-4">
                            <div class="rounded-full bg-neutral-100 p-3 dark:bg-neutral-700">
                                <svg xmlns="http://www.w3.org/2000/svg" class="size-6 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                        </div>
                        <h3 class="text-lg font-medium text-neutral-900 dark:text-neutral-100">{{ __('No upcoming deadlines') }}</h3>
                        <p class="text-neutral-500 dark:text-neutral-400">{{ __('All caught up!') }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Recent Announcements -->
        <div class="rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="border-b border-neutral-200 px-6 py-4 dark:border-neutral-700">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Recent Announcements') }}</h2>
                    <flux:button size="sm" variant="ghost" :href="route('teacher.courses.index')">
                        {{ __('Create') }}
                    </flux:button>
                </div>
            </div>

            <div class="p-6">
                @if($recentAnnouncements->count() > 0)
                    <div class="space-y-4">
                        @foreach($recentAnnouncements as $announcement)
                        <div class="flex items-start gap-4 p-4 rounded-lg border border-neutral-200 dark:border-neutral-700">
                            <div class="flex-shrink-0">
                                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100 text-blue-600 dark:bg-blue-900 dark:text-blue-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1">
                                <h3 class="font-semibold text-neutral-900 dark:text-neutral-100">{{ $announcement->title }}</h3>
                                <p class="text-sm text-neutral-600 dark:text-neutral-400 line-clamp-2">{{ Str::limit($announcement->content, 100) }}</p>
                                <p class="text-sm text-neutral-500 dark:text-neutral-500 mt-1">
                                    {{ $announcement->created_at->diffForHumans() }}
                                </p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <div class="flex justify-center mb-4">
                            <div class="rounded-full bg-neutral-100 p-3 dark:bg-neutral-700">
                                <svg xmlns="http://www.w3.org/2000/svg" class="size-6 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                                </svg>
                            </div>
                        </div>
                        <h3 class="text-lg font-medium text-neutral-900 dark:text-neutral-100">{{ __('No recent announcements') }}</h3>
                        <p class="text-neutral-500 dark:text-neutral-400">{{ __('Create an announcement to keep students informed') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts::app>
