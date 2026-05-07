{{--
    =============================================================================
    TEACHER COURSE DETAILS VIEW
    =============================================================================

    Purpose: Display detailed information about a specific course section for teachers.

    Route: teacher.courses.show
    Controller: Teacher\CourseController@show

    Components:
    - Header with course name, section number, semester info, back button
    - Quick Stats cards: Enrolled Students, Materials, Assessments, Average Grade
    - Quick Actions grid: Students, Attendance, Materials, Assessments, Grades, Announce
    - Live Meeting section (Microsoft Teams) with join button
    - Enrolled Students preview table (first 10)

    Required Data:
    - $section: CourseSection model with loaded relationships
    - $averageGrade: Average grade for the section

    Dependencies:
    - route('teacher.courses.index') - Back to courses list
    - route('teacher.courses.students', $section) - View students
    - route('teacher.courses.attendance', $section) - Manage attendance
    - route('teacher.courses.materials', $section) - Manage materials
    - route('teacher.courses.assessments', $section) - Manage assessments
    - route('teacher.courses.grades', $section) - Manage grades
    - route('teacher.courses.announcements', $section) - Manage announcements
    - $section->course->name - Course name
    - $section->sectionNumber - Section number accessor
    - $section->semester->name - Semester name
    - $section->enrollments - Section enrollments
    - $section->meeting_link - Teams meeting link

    =============================================================================
--}}
<x-layouts::app :title="__('Course Details')">
    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-neutral-900 dark:text-neutral-100">{{ $section->course?->name ?? __('Unknown Course') }}</h1>
            <p class="mt-1 text-neutral-500 dark:text-neutral-400">
                {{ __('Section') }} {{ $section->sectionNumber }}
                @if($section->semester)
                    - {{ $section->semester->name }}
                    @if($section->semester->academicYear)
                        {{ $section->semester->academicYear->name }}
                    @endif
                @endif
            </p>
        </div>
        <flux:button :href="route('teacher.courses.index')" variant="ghost">
            <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12" />
            </svg>
            {{ __('Back to Courses') }}
        </flux:button>
    </div>

    <!-- Quick Stats with Gradient -->
    <div class="mb-6 grid gap-4 md:grid-cols-4">
        <div class="group relative overflow-hidden rounded-xl border border-neutral-200 bg-white p-5 shadow-sm transition-all hover:shadow-md dark:border-neutral-700 dark:bg-neutral-800">
            <div class="absolute inset-0 bg-gradient-to-br from-emerald-50 to-teal-50 opacity-0 transition-opacity group-hover:opacity-100 dark:from-emerald-900/20 dark:to-teal-900/20"></div>
            <div class="relative flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-100 text-emerald-600 dark:bg-emerald-900 dark:text-emerald-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Enrolled Students') }}</p>
                    <p class="text-xl font-bold text-neutral-900 dark:text-neutral-100">{{ $section->enrollments->count() }}</p>
                </div>
            </div>
        </div>
        <div class="group relative overflow-hidden rounded-xl border border-neutral-200 bg-white p-5 shadow-sm transition-all hover:shadow-md dark:border-neutral-700 dark:bg-neutral-800">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-50 to-indigo-50 opacity-0 transition-opacity group-hover:opacity-100 dark:from-blue-900/20 dark:to-indigo-900/20"></div>
            <div class="relative flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100 text-blue-600 dark:bg-blue-900 dark:text-blue-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Materials') }}</p>
                    <p class="text-xl font-bold text-neutral-900 dark:text-neutral-100">{{ $section->materials->count() }}</p>
                </div>
            </div>
        </div>
        <div class="group relative overflow-hidden rounded-xl border border-neutral-200 bg-white p-5 shadow-sm transition-all hover:shadow-md dark:border-neutral-700 dark:bg-neutral-800">
            <div class="absolute inset-0 bg-gradient-to-br from-purple-50 to-violet-50 opacity-0 transition-opacity group-hover:opacity-100 dark:from-purple-900/20 dark:to-violet-900/20"></div>
            <div class="relative flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-purple-100 text-purple-600 dark:bg-purple-900 dark:text-purple-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Assessments') }}</p>
                    <p class="text-xl font-bold text-neutral-900 dark:text-neutral-100">{{ $section->assessments->count() }}</p>
                </div>
            </div>
        </div>
        <div class="group relative overflow-hidden rounded-xl border border-neutral-200 bg-white p-5 shadow-sm transition-all hover:shadow-md dark:border-neutral-700 dark:bg-neutral-800">
            <div class="absolute inset-0 bg-gradient-to-br from-amber-50 to-orange-50 opacity-0 transition-opacity group-hover:opacity-100 dark:from-amber-900/20 dark:to-orange-900/20"></div>
            <div class="relative flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-amber-100 text-amber-600 dark:bg-amber-900 dark:text-amber-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Average Grade') }}</p>
                    <p class="text-xl font-bold text-neutral-900 dark:text-neutral-100">
                        {{ $averageGrade ? $averageGrade . '%' : '--' }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="mb-6 grid gap-4 md:grid-cols-6">
        <a href="{{ route('teacher.courses.students', $section) }}" class="flex items-center justify-center gap-2 rounded-xl border border-neutral-200 bg-white p-4 text-center shadow-sm transition-all hover:border-emerald-500 hover:shadow-md dark:border-neutral-700 dark:bg-neutral-800">
            <svg xmlns="http://www.w3.org/2000/svg" class="size-5 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <span class="text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Students') }}</span>
        </a>
        <a href="{{ route('teacher.courses.attendance', $section) }}" class="flex items-center justify-center gap-2 rounded-xl border border-neutral-200 bg-white p-4 text-center shadow-sm transition-all hover:border-blue-500 hover:shadow-md dark:border-neutral-700 dark:bg-neutral-800">
            <svg xmlns="http://www.w3.org/2000/svg" class="size-5 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Attendance') }}</span>
        </a>
        <a href="{{ route('teacher.courses.materials', $section) }}" class="flex items-center justify-center gap-2 rounded-xl border border-neutral-200 bg-white p-4 text-center shadow-sm transition-all hover:border-indigo-500 hover:shadow-md dark:border-neutral-700 dark:bg-neutral-800">
            <svg xmlns="http://www.w3.org/2000/svg" class="size-5 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <span class="text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Materials') }}</span>
        </a>
        <a href="{{ route('teacher.courses.assessments', $section) }}" class="flex items-center justify-center gap-2 rounded-xl border border-neutral-200 bg-white p-4 text-center shadow-sm transition-all hover:border-purple-500 hover:shadow-md dark:border-neutral-700 dark:bg-neutral-800">
            <svg xmlns="http://www.w3.org/2000/svg" class="size-5 text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
            <span class="text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Assessments') }}</span>
        </a>
        <a href="{{ route('teacher.courses.grades', $section) }}" class="flex items-center justify-center gap-2 rounded-xl border border-neutral-200 bg-white p-4 text-center shadow-sm transition-all hover:border-amber-500 hover:shadow-md dark:border-neutral-700 dark:bg-neutral-800">
            <svg xmlns="http://www.w3.org/2000/svg" class="size-5 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
            </svg>
            <span class="text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Grades') }}</span>
        </a>
        <a href="{{ route('teacher.courses.announcements', $section) }}" class="flex items-center justify-center gap-2 rounded-xl border border-neutral-200 bg-white p-4 text-center shadow-sm transition-all hover:border-rose-500 hover:shadow-md dark:border-neutral-700 dark:bg-neutral-800">
            <svg xmlns="http://www.w3.org/2000/svg" class="size-5 text-rose-600 dark:text-rose-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
            </svg>
            <span class="text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Announce') }}</span>
        </a>
    </div>

    <!-- Microsoft Teams Meeting -->
    <div class="mb-6 rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
        <div class="flex items-center justify-between border-b border-neutral-200 px-6 py-4 dark:border-neutral-700">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-100 text-indigo-600 dark:bg-indigo-900 dark:text-indigo-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Live Meeting') }}</h2>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Microsoft Teams meeting link') }}</p>
                </div>
            </div>
            @if($section->meeting_link)
                <flux:button :href="$section->meeting_link" target="_blank" variant="primary" size="sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                    {{ __('Join Meeting') }}
                </flux:button>
            @endif
        </div>
        <div class="p-6">
            @if($section->meeting_link)
                <div class="grid gap-4 md:grid-cols-3">
                    <div>
                        <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Meeting Link') }}</p>
                        <p class="font-medium text-neutral-900 dark:text-neutral-100">{{ $section->meeting_link }}</p>
                    </div>
                    @if($section->meeting_id)
                        <div>
                            <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Meeting ID') }}</p>
                            <p class="font-medium text-neutral-900 dark:text-neutral-100">{{ $section->meeting_id }}</p>
                        </div>
                    @endif
                    @if($section->meeting_password)
                        <div>
                            <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Password') }}</p>
                            <p class="font-medium text-neutral-900 dark:text-neutral-100">{{ $section->meeting_password }}</p>
                        </div>
                    @endif
                </div>
            @else
                <p class="text-neutral-500 dark:text-neutral-400">{{ __('No meeting link configured. Contact admin to set up Teams meeting.') }}</p>
            @endif
        </div>
    </div>

    <!-- Enrolled Students Preview -->
    <div class="rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
        <div class="border-b border-neutral-200 px-6 py-4 dark:border-neutral-700">
            <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Enrolled Students') }}</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-neutral-50 text-neutral-500 dark:bg-neutral-700 dark:text-neutral-400">
                    <tr>
                        <th class="px-6 py-3">{{ __('Student') }}</th>
                        <th class="px-6 py-3">{{ __('Student ID') }}</th>
                        <th class="px-6 py-3">{{ __('Status') }}</th>
                        <th class="px-6 py-3 text-right">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                    @forelse($section->enrollments->take(10) as $enrollment)
                    <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-700/50">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 text-xs font-bold text-white">
                                    {{ substr($enrollment->student->profile?->first_name ?? 'S', 0, 1) }}{{ substr($enrollment->student->profile?->last_name ?? '', 0, 1) }}
                                </div>
                                <span class="font-medium text-neutral-900 dark:text-neutral-100">{{ $enrollment->student?->fullName ?? __('Unknown') }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-neutral-600 dark:text-neutral-400">{{ $enrollment->student->id }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800 dark:bg-green-900 dark:text-green-200">
                                {{ __($enrollment->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <flux:button size="sm" variant="subtle" :href="route('teacher.courses.students', $section)">
                                {{ __('View') }}
                            </flux:button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-neutral-100 dark:bg-neutral-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="size-8 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                </div>
                                <h3 class="text-lg font-medium text-neutral-900 dark:text-neutral-100">{{ __('No students enrolled yet') }}</h3>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts::app>
