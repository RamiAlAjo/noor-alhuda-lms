{{--
    =============================================================================
    STUDENT COURSE DETAILS VIEW
    =============================================================================

    Purpose: Display detailed information about a specific course for students.

    Route: student.courses.show
    Controller: Student\CourseController@show

    Components:
    - Header with course name, code, section number, back button
    - Course Information section (collapsible): Instructor, Semester, Credits, Schedule, Room, Status
    - Weekly Content section (collapsible): Materials grouped by week with video/File downloads
    - Competencies section (if available)
    - Upcoming Assessments section with quiz/assignment/exam details
    - View All Materials button
    - Live Meeting link (Microsoft Teams)
    - Sidebar:
      * Course Stats: Attendance rate, Assessments count, Materials count
      * Quick Actions: View Grades, Attendance, Participants
      * Announcements Preview

    Required Data:
    - $offering: CourseOffering model
    - $enrollment: Student's enrollment record
    - $materialsByWeek: Materials grouped by week
    - $upcomingAssessments: Upcoming assessments
    - $attendance: Student's attendance records

    Dependencies:
    - route('student.courses.index') - Back to courses
    - route('student.courses.materials', $offering) - All materials
    - route('student.courses.grades', $offering) - View grades
    - route('student.courses.attendance', $offering) - View attendance
    - route('student.courses.participants', $offering) - View participants
    - $offering->teacher->full_name - Teacher's full name
    - $material->hasYouTubeVideo() - Check for YouTube video
    - $material->getYouTubeEmbedUrl() - Get embedded video URL

    =============================================================================
--}}
<x-layouts::app :title="$offering?->course?->name ?? __('Course Details')">
    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-neutral-900 dark:text-neutral-100">{{ $offering?->course?->name ?? __('Course') }}</h1>
            <p class="mt-1 text-neutral-500 dark:text-neutral-400">{{ $offering?->course?->code ?? '-' }} - {{ $offering?->section_name ?? '-' }}</p>
        </div>
        <flux:button :href="route('student.courses.index')" variant="ghost">
            <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12" />
            </svg>
            {{ __('Back to Courses') }}
        </flux:button>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Course Information - Collapsible -->
            <div class="rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800" x-data="{ open: true }">
                <button @click="open = !open" class="flex w-full items-center justify-between border-b border-neutral-200 px-6 py-4 dark:border-neutral-700">
                    <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Course Information') }}</h2>
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="open" x-collapse class="p-6">
                    <div class="grid gap-4 md:grid-cols-2">
                        <div class="space-y-3">
                            <div class="flex items-center gap-3">
                                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-100 text-indigo-600 dark:bg-indigo-900 dark:text-indigo-300">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('Instructor') }}</p>
                                    <p class="font-medium text-neutral-900 dark:text-neutral-100">{{ $offering->teacher?->full_name ?? __('Not assigned') }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-100 text-emerald-600 dark:bg-emerald-900 dark:text-emerald-300">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('Semester') }}</p>
                                    <p class="font-medium text-neutral-900 dark:text-neutral-100">{{ $offering->semester->name ?? '-' }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-violet-100 text-violet-600 dark:bg-violet-900 dark:text-violet-300">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('Credits') }}</p>
                                    <p class="font-medium text-neutral-900 dark:text-neutral-100">{{ $offering?->course?->credits ?? '-' }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="space-y-3">
                            <div class="flex items-center gap-3">
                                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-100 text-blue-600 dark:bg-blue-900 dark:text-blue-300">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('Schedule') }}</p>
                                    <p class="font-medium text-neutral-900 dark:text-neutral-100">{{ $offering->schedule ?? __('Not set') }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-amber-100 text-amber-600 dark:bg-amber-900 dark:text-amber-300">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('Room') }}</p>
                                    <p class="font-medium text-neutral-900 dark:text-neutral-100">{{ $offering->room ?? __('Not set') }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="flex h-8 w-8 items-center justify-center rounded-lg {{ $enrollment->status == 'approved' ? 'bg-green-100 text-green-600 dark:bg-green-900 dark:text-green-300' : 'bg-yellow-100 text-yellow-600 dark:bg-yellow-900 dark:text-yellow-300' }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('Status') }}</p>
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                        {{ $enrollment->status == 'approved' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' }}">
                                        {{ __($enrollment->status) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Weekly Content - Collapsible -->
            <div class="rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800" x-data="{ open: true }">
                <button @click="open = !open" class="flex w-full items-center justify-between border-b border-neutral-200 px-6 py-4 dark:border-neutral-700">
                    <div class="flex items-center gap-2">
                        <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Weekly Content') }}</h2>
                        @if($materialsByWeek->isNotEmpty())
                            <span class="rounded-full bg-indigo-100 px-2 py-0.5 text-xs font-medium text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200">
                                {{ $materialsByWeek->count() }} {{ __('weeks') }}
                            </span>
                        @endif
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="open" x-collapse>
                    @if($materialsByWeek->isNotEmpty())
                        <div class="divide-y divide-neutral-200 dark:divide-neutral-700">
                            @foreach($materialsByWeek as $week => $materials)
                                <div class="p-4" x-data="{ weekOpen: false }">
                                    <button @click="weekOpen = !weekOpen" class="flex w-full items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-100 text-indigo-600 dark:bg-indigo-900 dark:text-indigo-300">
                                                <span class="text-sm font-semibold">{{ $week }}</span>
                                            </div>
                                            <span class="font-medium text-neutral-900 dark:text-neutral-100">{{ __('Week') }} {{ $week }}</span>
                                            <span class="text-sm text-neutral-500 dark:text-neutral-400">({{ $materials->count() }} {{ __('items') }})</span>
                                        </div>
                                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4 transition-transform" :class="weekOpen ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>
                                    <div x-show="weekOpen" x-collapse class="mt-3 space-y-2 pl-11">
                                        @foreach($materials as $material)
                                            @if($material->hasYouTubeVideo())
                                                <button
                                                    type="button"
                                                    class="flex w-full items-center gap-3 rounded-lg p-3 hover:bg-neutral-50 dark:hover:bg-neutral-700"
                                                    onclick="document.getElementById('video-modal-{{ $material->id }}').showModal()"
                                                >
                                                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-red-100 text-red-600 dark:bg-red-900 dark:text-red-300">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="size-5" viewBox="0 0 24 24" fill="currentColor">
                                                            <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                                                        </svg>
                                                    </div>
                                                    <div class="flex-1 text-left">
                                                        <p class="font-medium text-neutral-900 dark:text-neutral-100">{{ $material->title }}</p>
                                                        @if($material->description)
                                                            <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ Str::limit($material->description, 60) }}</p>
                                                        @endif
                                                    </div>
                                                </button>
                                                <dialog id="video-modal-{{ $material->id }}" class="modal" onclick="if(event.target === this) { this.close(); document.getElementById('video-iframe-{{ $material->id }}').src = document.getElementById('video-iframe-{{ $material->id }}').src; }">
                                                    <div class="modal-box max-w-4xl bg-neutral-900">
                                                        <h3 class="mb-4 text-lg font-bold text-white">{{ $material->title }}</h3>
                                                        <div class="aspect-video w-full">
                                                            <iframe
                                                                id="video-iframe-{{ $material->id }}"
                                                                class="h-full w-full"
                                                                src="{{ $material->getYouTubeEmbedUrl() }}"
                                                                title="{{ $material->title }}"
                                                                frameborder="0"
                                                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                                                allowfullscreen>
                                                            </iframe>
                                                        </div>
                                                        <form method="dialog" class="mt-4">
                                                            <flux:button type="submit" variant="subtle">{{ __('Close') }}</flux:button>
                                                        </form>
                                                    </div>
                                                </dialog>
                                            @elseif($material->file_path)
                                                <a href="{{ asset('storage/' . $material->file_path) }}" target="_blank" class="flex items-center gap-3 rounded-lg p-3 hover:bg-neutral-50 dark:hover:bg-neutral-700">
                                                    <div class="flex h-10 w-10 items-center justify-center rounded-lg
                                                        @switch($material->material_type)
                                                            @case('lecture') bg-blue-100 text-blue-600 dark:bg-blue-900 dark:text-blue-300 @break
                                                            @case('assignment') bg-amber-100 text-amber-600 dark:bg-amber-900 dark:text-amber-300 @break
                                                            @case('exam') bg-red-100 text-red-600 dark:bg-red-900 dark:text-red-300 @break
                                                            @case('resource') bg-emerald-100 text-emerald-600 dark:bg-emerald-900 dark:text-emerald-300 @break
                                                            @default bg-neutral-100 text-neutral-600 dark:bg-neutral-700 dark:text-neutral-300
                                                        @endswitch">
                                                        @switch($material->material_type)
                                                            @case('lecture')
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                                </svg>
                                                                @break
                                                            @case('assignment')
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                                                </svg>
                                                                @break
                                                            @case('exam')
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                                                </svg>
                                                                @break
                                                            @default
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                                                </svg>
                                                        @endswitch
                                                    </div>
                                                    <div class="flex-1">
                                                        <p class="font-medium text-neutral-900 dark:text-neutral-100">{{ $material->title }}</p>
                                                        @if($material->description)
                                                            <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ Str::limit($material->description, 60) }}</p>
                                                        @endif
                                                    </div>
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                    </svg>
                                                </a>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-6 text-center">
                            <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-neutral-100 mx-auto dark:bg-neutral-700">
                                <svg xmlns="http://www.w3.org/2000/svg" class="size-6 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <p class="text-neutral-500 dark:text-neutral-400">{{ __('No materials available yet') }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Competencies -->
            @php
                $hasCompetencies = false;
                try {
                    $hasCompetencies = $offering->competencies->isNotEmpty();
                } catch (\Exception $e) {
                    // competencies table not ready
                }
            @endphp
            @if($hasCompetencies)
                <div class="rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800" x-data="{ open: true }">
                    <button @click="open = !open" class="flex w-full items-center justify-between border-b border-neutral-200 px-6 py-4 dark:border-neutral-700">
                        <div class="flex items-center gap-2">
                            <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Competencies') }}</h2>
                            <span class="rounded-full bg-violet-100 px-2 py-0.5 text-xs font-medium text-violet-800 dark:bg-violet-900 dark:text-violet-200">
                                {{ $offering->competencies->count() }}
                            </span>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-5 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open" x-collapse class="divide-y divide-neutral-200 dark:divide-neutral-700">
                        @foreach($offering->competencies as $competency)
                            <div class="p-4">
                                <div class="flex items-start gap-3">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-violet-100 text-violet-600 dark:bg-violet-900 dark:text-violet-300">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <h3 class="font-medium text-neutral-900 dark:text-neutral-100">{{ $competency->name }}</h3>
                                        @if($competency->code)
                                            <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Code') }}: {{ $competency->code }}</p>
                                        @endif
                                        @if($competency->description)
                                            <p class="mt-1 text-sm text-neutral-600 dark:text-neutral-300">{{ $competency->description }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-neutral-100 text-neutral-500 dark:bg-neutral-700 dark:text-neutral-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-medium text-neutral-900 dark:text-neutral-100">{{ __('Competencies') }}</h3>
                            <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('No competencies linked to this course') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Upcoming Assessments -->
            @if($upcomingAssessments->isNotEmpty())
                <div class="rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800" x-data="{ open: true }">
                    <button @click="open = !open" class="flex w-full items-center justify-between border-b border-neutral-200 px-6 py-4 dark:border-neutral-700">
                        <div class="flex items-center gap-2">
                            <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Upcoming Assessments') }}</h2>
                            <span class="rounded-full bg-amber-100 px-2 py-0.5 text-xs font-medium text-amber-800 dark:bg-amber-900 dark:text-amber-200">
                                {{ $upcomingAssessments->count() }}
                            </span>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-5 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open" x-collapse class="divide-y divide-neutral-200 dark:divide-neutral-700">
                        @foreach($upcomingAssessments as $assessment)
                            <div class="flex items-center gap-4 p-4">
                                <div class="flex h-12 w-12 items-center justify-center rounded-lg
                                    @switch($assessment->assessment_type)
                                        @case('quiz') bg-indigo-100 text-indigo-600 dark:bg-indigo-900 dark:text-indigo-300 @break
                                        @case('assignment') bg-amber-100 text-amber-600 dark:bg-amber-900 dark:text-amber-300 @break
                                        @case('exam') bg-red-100 text-red-600 dark:bg-red-900 dark:text-red-300 @break
                                        @case('midterm') bg-orange-100 text-orange-600 dark:bg-orange-900 dark:text-orange-300 @break
                                        @case('final') bg-purple-100 text-purple-600 dark:bg-purple-900 dark:text-purple-300 @break
                                        @default bg-neutral-100 text-neutral-600 dark:bg-neutral-700 dark:text-neutral-300
                                    @endswitch">
                                    @if($assessment->assessment_type == 'quiz')
                                        <svg xmlns="http://www.w3.org/2000/svg" class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-v3h2m2 0h2m-2 4h2m-2-8h2m-2 4h2m-2-4h2m-2 4h2m-4-8h2m-2 4h2m-2-4h2" />
                                        </svg>
                                    @elseif($assessment->assessment_type == 'assignment')
                                        <svg xmlns="http://www.w3.org/2000/svg" class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                        </svg>
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                        </svg>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <p class="font-medium text-neutral-900 dark:text-neutral-100">{{ $assessment->title }}</p>
                                    <div class="flex flex-wrap items-center gap-2 mt-1">
                                        <span class="text-sm text-neutral-500 dark:text-neutral-400">
                                            <span class="@if($assessment->due_date && $assessment->due_date->isPast()) text-red-500 @elseif($assessment->due_date && $assessment->due_date->diffInDays(now()) <= 3) text-amber-500 @endif">
                                                {{ $assessment->due_date ? $assessment->due_date->format('M d, Y - h:i A') : __('No due date') }}
                                            </span>
                                        </span>
                                        @if($assessment->quiz_type && $assessment->quiz_type !== 'none')
                                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium
                                                @if($assessment->quiz_type === 'quiz') bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-200
                                                @elseif($assessment->quiz_type === 'pre_quiz') bg-cyan-100 text-cyan-800 dark:bg-cyan-900 dark:text-cyan-200
                                                @elseif($assessment->quiz_type === 'post_quiz') bg-teal-100 text-teal-800 dark:bg-teal-900 dark:text-teal-200
                                                @endif">
                                                @if($assessment->quiz_type === 'quiz')
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                    </svg>
                                                    {{ __('Quiz') }}
                                                @elseif($assessment->quiz_type === 'pre_quiz')
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    {{ __('Pre-Quiz') }}
                                                @else
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    {{ __('Post-Quiz') }}
                                                @endif
                                            </span>
                                        @endif
                                        @if($assessment->time_limit_minutes && $assessment->time_limit_minutes > 0)
                                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                {{ $assessment->time_limit_minutes }} {{ __('min') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <span class="rounded-full px-2.5 py-0.5 text-xs font-medium
                                    @switch($assessment->assessment_type)
                                        @case('quiz') bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200 @break
                                        @case('assignment') bg-amber-100 text-amber-800 dark:bg-amber-900 dark:text-amber-200 @break
                                        @case('exam') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 @break
                                        @case('midterm') bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200 @break
                                        @case('final') bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200 @break
                                        @default bg-neutral-100 text-neutral-800 dark:bg-neutral-700 dark:text-neutral-200
                                    @endswitch">
                                    {{ __($assessment->assessment_type) }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Course Materials Button -->
            <div class="rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                <div class="p-4">
                    <flux:button :href="route('student.courses.materials', $offering)" variant="ghost" class="w-full justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        {{ __('View All Materials') }}
                    </flux:button>
                </div>
            </div>

            <!-- Live Meeting Link -->
            @if($offering->meeting_link)
                <div class="rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                    <div class="border-b border-neutral-200 px-6 py-4 dark:border-neutral-700">
                        <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Live Meeting') }}</h2>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center gap-4">
                            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-blue-100 text-blue-600 dark:bg-blue-900 dark:text-blue-300">
                                <svg xmlns="http://www.w3.org/2000/svg" class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="font-medium text-neutral-900 dark:text-neutral-100">{{ __('Join Microsoft Teams Meeting') }}</p>
                                @if($offering->meeting_id)
                                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Meeting ID') }}: {{ $offering->meeting_id }}</p>
                                @endif
                            </div>
                            <flux:button :href="$offering->meeting_link" target="_blank" variant="primary">
                                <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                </svg>
                                {{ __('Join') }}
                            </flux:button>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Course Stats -->
            <div class="rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                <div class="border-b border-neutral-200 px-6 py-4 dark:border-neutral-700">
                    <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Course Stats') }}</h2>
                </div>
                <div class="p-4 space-y-4">
                    @php
                        $totalAttendance = $attendance->count();
                        $presentCount = $attendance->where('status', 'present')->count();
                        $attendanceRate = $totalAttendance > 0 ? round(($presentCount / $totalAttendance) * 100) : 0;
                    @endphp
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-100 text-emerald-600 dark:bg-emerald-900 dark:text-emerald-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Attendance') }}</p>
                                <p class="text-lg font-bold text-neutral-900 dark:text-neutral-100">{{ $attendanceRate }}%</p>
                            </div>
                        </div>
                        <span class="text-sm text-neutral-500">{{ $presentCount }}/{{ $totalAttendance }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-amber-100 text-amber-600 dark:bg-amber-900 dark:text-amber-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Assessments') }}</p>
                                <p class="text-lg font-bold text-neutral-900 dark:text-neutral-100">{{ $offering->assessments->count() }}</p>
                            </div>
                        </div>
                        <span class="text-sm text-neutral-500">{{ $offering->assessments->where('is_published', true)->count() }} {{ __('published') }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100 text-blue-600 dark:bg-blue-900 dark:text-blue-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Materials') }}</p>
                                <p class="text-lg font-bold text-neutral-900 dark:text-neutral-100">{{ $offering->materials->count() }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                <div class="border-b border-neutral-200 px-6 py-4 dark:border-neutral-700">
                    <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Quick Actions') }}</h2>
                </div>
                <div class="space-y-2 p-4">
                    <a href="{{ route('student.courses.grades', $offering) }}" class="flex items-center gap-3 rounded-lg p-3 text-neutral-700 hover:bg-neutral-100 dark:text-neutral-300 dark:hover:bg-neutral-700">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-amber-100 text-amber-600 dark:bg-amber-900 dark:text-amber-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                            </svg>
                        </div>
                        <span class="font-medium">{{ __('View Grades') }}</span>
                    </a>
                    <a href="{{ route('student.courses.attendance', $offering) }}" class="flex items-center gap-3 rounded-lg p-3 text-neutral-700 hover:bg-neutral-100 dark:text-neutral-300 dark:hover:bg-neutral-700">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-100 text-emerald-600 dark:bg-emerald-900 dark:text-emerald-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <span class="font-medium">{{ __('Attendance') }}</span>
                    </a>
                    <a href="{{ route('student.courses.participants', $offering) }}" class="flex items-center gap-3 rounded-lg p-3 text-neutral-700 hover:bg-neutral-100 dark:text-neutral-300 dark:hover:bg-neutral-700">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-100 text-blue-600 dark:bg-blue-900 dark:text-blue-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                        <span class="font-medium">{{ __('Participants') }}</span>
                    </a>
                </div>
            </div>

            <!-- Announcements Preview -->
            @if($offering->announcements->isNotEmpty())
                <div class="rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                    <div class="border-b border-neutral-200 px-6 py-4 dark:border-neutral-700">
                        <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Recent Announcements') }}</h2>
                    </div>
                    <div class="divide-y divide-neutral-200 dark:divide-neutral-700">
                        @foreach($offering->announcements->take(3) as $announcement)
                            <div class="p-4">
                                <h3 class="font-medium text-neutral-900 dark:text-neutral-100">{{ $announcement->title }}</h3>
                                <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">{{ Str::limit($announcement->content, 80) }}</p>
                                <p class="mt-2 text-xs text-neutral-400 dark:text-neutral-500">{{ $announcement->created_at->diffForHumans() }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                    <div class="border-b border-neutral-200 px-6 py-4 dark:border-neutral-700">
                        <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Recent Announcements') }}</h2>
                    </div>
                    <div class="p-6 text-center">
                        <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-neutral-100 mx-auto dark:bg-neutral-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-6 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                            </svg>
                        </div>
                        <p class="text-neutral-500 dark:text-neutral-400">{{ __('No announcements yet') }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-layouts::app>
