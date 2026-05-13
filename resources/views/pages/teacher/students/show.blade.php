{{--
    =============================================================================
    TEACHER STUDENT PROFILE VIEW
    =============================================================================

    Purpose: Display student profile information for teachers, including
    enrollment details, grades, and course-specific information.

    Route: teacher.students.show
    Controller: Teacher\CourseController@showStudent

    Components:
    - Student basic information (name, ID, email, contact)
    - Academic information (major, GPA, enrollment status)
    - Shared courses with teacher
    - Recent grades and performance
    - Enrollment history

    Required Data:
    - $student: User model with profile, enrollments, grades
    - $sharedCourses: Courses where teacher teaches and student is enrolled

    =============================================================================
--}}
<x-layouts::app :title="__('Student Profile')">
    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-neutral-900 dark:text-neutral-100">{{ __('Student Profile') }}</h1>
            <p class="mt-1 text-neutral-500 dark:text-neutral-400">{{ $student->fullName ?? $student->name }}</p>
        </div>
        <flux:button :href="route('teacher.courses.students', $sharedCourses->first()->offering ?? null)" variant="ghost">
            <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12" />
            </svg>
            {{ __('Back to Students') }}
        </flux:button>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Student Information -->
            <div class="rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                <div class="border-b border-neutral-200 px-6 py-4 dark:border-neutral-700">
                    <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Student Information') }}</h2>
                </div>
                <div class="p-6">
                    <div class="grid gap-6 md:grid-cols-2">
                        <div class="space-y-4">
                            <div class="flex items-center gap-3">
                                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 text-lg font-bold text-white">
                                    {{ substr($student->profile?->first_name ?? 'S', 0, 1) }}{{ substr($student->profile?->last_name ?? '', 0, 1) }}
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ $student->fullName ?? $student->name }}</h3>
                                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Student ID') }}: {{ $student->user_id ?? $student->id }}</p>
                                </div>
                            </div>

                            <div class="space-y-3">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-100 text-blue-600 dark:bg-blue-900 dark:text-blue-400">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('Email') }}</p>
                                        <p class="font-medium text-neutral-900 dark:text-neutral-100">{{ $student->email }}</p>
                                    </div>
                                </div>

                                @if($student->profile?->phone)
                                <div class="flex items-center gap-3">
                                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-green-100 text-green-600 dark:bg-green-900 dark:text-green-400">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('Phone') }}</p>
                                        <p class="font-medium text-neutral-900 dark:text-neutral-100">{{ $student->profile->phone }}</p>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>

                        <div class="space-y-3">
                            @if($student->profile?->major)
                            <div class="flex items-center gap-3">
                                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-purple-100 text-purple-600 dark:bg-purple-900 dark:text-purple-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('Major') }}</p>
                                    <p class="font-medium text-neutral-900 dark:text-neutral-100">{{ $student->profile->major->name ?? '-' }}</p>
                                </div>
                            </div>
                            @endif

                            <div class="flex items-center gap-3">
                                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-amber-100 text-amber-600 dark:bg-amber-900 dark:text-amber-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('Joined') }}</p>
                                    <p class="font-medium text-neutral-900 dark:text-neutral-100">{{ $student->created_at->format('M d, Y') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Shared Courses -->
            @if($sharedCourses->count() > 0)
            <div class="rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                <div class="border-b border-neutral-200 px-6 py-4 dark:border-neutral-700">
                    <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Shared Courses') }}</h2>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        @foreach($sharedCourses as $enrollment)
                        <div class="flex items-center justify-between rounded-lg border border-neutral-200 p-4 dark:border-neutral-700">
                            <div>
                                <h3 class="font-semibold text-neutral-900 dark:text-neutral-100">{{ $enrollment->offering->course->name }}</h3>
                                <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ $enrollment->offering->course->code }} - {{ $enrollment->offering->section_name }}</p>
                                <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ $enrollment->offering->semester->name }}</p>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800 dark:bg-green-900 dark:text-green-200">
                                    {{ __($enrollment->status) }}
                                </span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Recent Grades -->
            @if($student->grades->count() > 0)
            <div class="rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                <div class="border-b border-neutral-200 px-6 py-4 dark:border-neutral-700">
                    <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Recent Grades') }}</h2>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        @foreach($student->grades->take(5) as $grade)
                        <div class="flex items-center justify-between rounded-lg border border-neutral-200 p-3 dark:border-neutral-700">
                            <div>
                                <p class="font-medium text-neutral-900 dark:text-neutral-100">{{ $grade->assessment->title }}</p>
                                <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ $grade->assessment->offering->course->name }}</p>
                            </div>
                            <div class="text-right">
                                <span class="font-semibold text-neutral-900 dark:text-neutral-100">{{ number_format($grade->percentage, 1) }}%</span>
                                <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ $grade->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Academic Summary -->
            <div class="rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                <div class="border-b border-neutral-200 px-6 py-4 dark:border-neutral-700">
                    <h3 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Academic Summary') }}</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div class="text-center">
                        <p class="text-3xl font-bold text-indigo-600 dark:text-indigo-400">{{ $student->enrollments->count() }}</p>
                        <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Total Enrollments') }}</p>
                    </div>

                    @php
                        $avgGrade = $student->grades->avg('percentage') ?? 0;
                    @endphp
                    @if($avgGrade > 0)
                    <div class="text-center">
                        <p class="text-3xl font-bold text-green-600 dark:text-green-400">{{ number_format($avgGrade, 1) }}%</p>
                        <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Average Grade') }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layouts::app>