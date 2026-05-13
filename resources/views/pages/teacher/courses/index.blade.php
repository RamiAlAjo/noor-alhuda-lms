{{--
    =============================================================================
    TEACHER COURSES INDEX VIEW
    =============================================================================

    Purpose: Display teacher's assigned course sections with quick access to management features.

    Route: teacher.courses.index
    Controller: Teacher\CourseController@index

    Components:
    - Header with title and description
    - Stats cards: Total Sections, Total Students, Active Assessments
    - Course cards grid with:
      * Course name, code, section name
      * Semester badge
      * Student count and schedule info
      * Quick action buttons: View, Students, Attendance, Materials, Assessments, Grades
    - Empty state when no courses assigned

    Required Data:
    - $sections: Collection of CourseSection models assigned to teacher

    Dependencies:
    - route('teacher.courses.show', $section) - View course details
    - route('teacher.courses.students', $section) - View enrolled students
    - route('teacher.courses.attendance', $section) - Manage attendance
    - route('teacher.courses.materials', $section) - Manage materials
    - route('teacher.courses.assessments', $section) - Manage assessments
    - route('teacher.courses.grades', $section) - Manage grades
    - $section->course->name - Course name
    - $section->course->code - Course code
    - $section->enrollments->count() - Enrolled students count
    - $section->assessments->count() - Assessments count
    - $section->semester->name - Semester name

    =============================================================================
--}}
<x-layouts::app :title="__('My Courses')">
    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-neutral-900 dark:text-neutral-100">{{ __('My Courses') }}</h1>
            <p class="mt-1 text-neutral-500 dark:text-neutral-400">{{ __('Manage your assigned course sections') }}</p>
        </div>
    </div>

    <!-- Enhanced Stats Cards -->
    <div class="mb-6 grid gap-4 md:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-xl border border-neutral-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-100 text-emerald-600 dark:bg-emerald-900 dark:text-emerald-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Total Sections') }}</p>
                    <p class="text-xl font-bold text-neutral-900 dark:text-neutral-100">{{ $sections->count() }}</p>
                    @if($sections->count() > 0)
                        <p class="text-xs text-emerald-600 dark:text-emerald-400">{{ __('Active') }}: {{ $sections->where('is_active', true)->count() }}</p>
                    @endif
                </div>
            </div>
        </div>
        <div class="rounded-xl border border-neutral-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100 text-blue-600 dark:bg-blue-900 dark:text-blue-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Total Students') }}</p>
                    <p class="text-xl font-bold text-neutral-900 dark:text-neutral-100">{{ $sections->sum(fn($s) => $s->enrollments->count()) }}</p>
                    @if($sections->sum(fn($s) => $s->enrollments->count()) > 0)
                        <p class="text-xs text-blue-600 dark:text-blue-400">{{ __('Enrolled') }}: {{ $sections->sum(fn($s) => $s->enrollments->where('status', 'approved')->count()) }}</p>
                    @endif
                </div>
            </div>
        </div>
        <div class="rounded-xl border border-neutral-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-purple-100 text-purple-600 dark:bg-purple-900 dark:text-purple-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Active Assessments') }}</p>
                    <p class="text-xl font-bold text-neutral-900 dark:text-neutral-100">{{ $sections->sum(fn($s) => $s->assessments->count()) }}</p>
                    @php
                        $upcomingAssessments = $sections->flatMap->assessments->where('due_date', '>=', now())->count();
                    @endphp
                    @if($upcomingAssessments > 0)
                        <p class="text-xs text-purple-600 dark:text-purple-400">{{ __('Due soon') }}: {{ $upcomingAssessments }}</p>
                    @endif
                </div>
            </div>
        </div>
        <div class="rounded-xl border border-neutral-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-orange-100 text-orange-600 dark:bg-orange-900 dark:text-orange-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Avg Class Grade') }}</p>
                    @php
                        $totalGrades = $sections->flatMap->enrollments->flatMap->grades;
                        $avgGrade = $totalGrades->count() > 0 ? round($totalGrades->avg('percentage'), 1) : null;
                    @endphp
                    <p class="text-xl font-bold text-neutral-900 dark:text-neutral-100">{{ $avgGrade ? $avgGrade . '%' : '-' }}</p>
                    @if($avgGrade)
                        <p class="text-xs text-orange-600 dark:text-orange-400">{{ __('Pass rate') }}: {{ $totalGrades->where('passed', true)->count() }}/{{ $totalGrades->count() }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity & Quick Actions -->
    <div class="mb-6 grid gap-6 lg:grid-cols-3">
        <!-- Quick Actions -->
        <div class="rounded-lg border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-800">
            <h3 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100 mb-4">{{ __('Quick Actions') }}</h3>
            <div class="space-y-3">
                <a href="{{ route('teacher.calendar') }}" class="flex items-center gap-3 p-3 rounded-lg border border-neutral-200 hover:bg-neutral-50 dark:border-neutral-700 dark:hover:bg-neutral-700 transition-colors">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-100 text-blue-600 dark:bg-blue-900 dark:text-blue-400">
                        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="font-medium text-neutral-900 dark:text-neutral-100">{{ __('View Calendar') }}</p>
                        <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Course schedules & deadlines') }}</p>
                    </div>
                </a>
                <a href="{{ route('teacher.messages') }}" class="flex items-center gap-3 p-3 rounded-lg border border-neutral-200 hover:bg-neutral-50 dark:border-neutral-700 dark:hover:bg-neutral-700 transition-colors">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-green-100 text-green-600 dark:bg-green-900 dark:text-green-400">
                        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="font-medium text-neutral-900 dark:text-neutral-100">{{ __('Messages') }}</p>
                        <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Communicate with students') }}</p>
                    </div>
                </a>
                <a href="{{ route('teacher.appeals.index') }}" class="flex items-center gap-3 p-3 rounded-lg border border-neutral-200 hover:bg-neutral-50 dark:border-neutral-700 dark:hover:bg-neutral-700 transition-colors">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-yellow-100 text-yellow-600 dark:bg-yellow-900 dark:text-yellow-400">
                        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="font-medium text-neutral-900 dark:text-neutral-100">{{ __('Grade Appeals') }}</p>
                        <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Review student appeals') }}</p>
                    </div>
                </a>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="lg:col-span-2 rounded-lg border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-800">
            <h3 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100 mb-4">{{ __('Recent Activity') }}</h3>
            <div class="space-y-4">
                @php
                    $recentActivities = collect();
                    // Get recent assessments due
                    $upcomingAssessments = $sections->flatMap->assessments->where('due_date', '>=', now())->sortBy('due_date')->take(3);
                    foreach($upcomingAssessments as $assessment) {
                        $recentActivities->push([
                            'type' => 'assessment_due',
                            'title' => $assessment->title,
                            'description' => $assessment->courseOffering->course->name . ' - ' . $assessment->courseOffering->section_name,
                            'date' => $assessment->due_date,
                            'icon' => 'document-text',
                            'color' => 'blue'
                        ]);
                    }
                    // Get recent enrollments
                    $recentEnrollments = $sections->flatMap->enrollments->where('created_at', '>=', now()->subDays(7))->sortByDesc('created_at')->take(2);
                    foreach($recentEnrollments as $enrollment) {
                        $recentActivities->push([
                            'type' => 'new_enrollment',
                            'title' => 'New enrollment',
                            'description' => $enrollment->student->name . ' in ' . $enrollment->offering->course->name,
                            'date' => $enrollment->created_at,
                            'icon' => 'user-plus',
                            'color' => 'green'
                        ]);
                    }
                    $recentActivities = $recentActivities->sortByDesc('date')->take(5);
                @endphp

                @forelse($recentActivities as $activity)
                <div class="flex items-start gap-3">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-{{ $activity['color'] }}-100 text-{{ $activity['color'] }}-600 dark:bg-{{ $activity['color'] }}-900 dark:text-{{ $activity['color'] }}-400">
                        @if($activity['icon'] === 'document-text')
                            <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        @elseif($activity['icon'] === 'user-plus')
                            <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                            </svg>
                        @endif
                    </div>
                    <div class="flex-1">
                        <p class="font-medium text-neutral-900 dark:text-neutral-100">{{ $activity['title'] }}</p>
                        <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ $activity['description'] }}</p>
                        <p class="text-xs text-neutral-400 dark:text-neutral-500">{{ $activity['date']->diffForHumans() }}</p>
                    </div>
                </div>
                @empty
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    <p class="mt-2 text-sm text-neutral-500 dark:text-neutral-400">{{ __('No recent activity') }}</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Course Cards -->
    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
        @forelse($sections as $section)
        <div class="group relative overflow-hidden rounded-2xl border border-neutral-200 bg-white shadow-sm transition-all duration-300 hover:shadow-lg dark:border-neutral-700 dark:bg-neutral-800">
            <!-- Gradient Bar -->
            <div class="h-1 bg-gradient-to-r from-emerald-500 via-teal-500 to-cyan-500"></div>

            <div class="p-6">
                <!-- Course Header -->
                <div class="mb-4 flex items-start justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-neutral-900 dark:text-neutral-100">{{ $section->course?->name ?? __('Unknown Course') }}</h3>
                        <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ $section->course?->code ?? '' }} - {{ $section->name }}</p>
                    </div>
                        <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-medium text-emerald-700 dark:bg-emerald-900 dark:text-emerald-300">
                            {{ $section->semester?->localized_name ?? __('Active') }}
                        </span>
                </div>

                <!-- Stats -->
                <div class="mb-4 flex items-center gap-4 text-sm">
                    <div class="flex items-center gap-1 text-neutral-500 dark:text-neutral-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        {{ $section->enrollments->count() }} {{ __('Students') }}
                    </div>
                    @if($section->schedule)
                    <div class="flex items-center gap-1 text-neutral-500 dark:text-neutral-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ $section->schedule }}
                    </div>
                    @endif
                </div>

                <!-- Action Buttons -->
                <div class="grid grid-cols-3 gap-2">
                    <flux:button size="sm" variant="subtle" :href="route('teacher.courses.show', $section)" class="justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </flux:button>
                    <flux:button size="sm" variant="ghost" :href="route('teacher.courses.students', $section)" class="justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </flux:button>
                    <flux:button size="sm" variant="ghost" :href="route('teacher.courses.attendance', $section)" class="justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </flux:button>
                </div>

                <div class="mt-2 grid grid-cols-3 gap-2">
                    <flux:button size="sm" variant="ghost" :href="route('teacher.courses.materials', $section)" class="justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </flux:button>
                    <flux:button size="sm" variant="ghost" :href="route('teacher.courses.assessments', $section)" class="justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                    </flux:button>
                    <flux:button size="sm" variant="ghost" :href="route('teacher.courses.grades', $section)" class="justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                        </svg>
                    </flux:button>
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
                <h3 class="text-xl font-semibold text-neutral-900 dark:text-neutral-100">{{ __('No courses assigned yet') }}</h3>
                <p class="mt-2 text-neutral-500 dark:text-neutral-400">{{ __('Contact your administrator to assign courses to your account') }}</p>
            </div>
        </div>
        @endforelse
    </div>
</x-layouts::app>

