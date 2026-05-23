<x-layouts::app :title="__('Browse Courses')">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-neutral-900 dark:text-neutral-100">{{ __('Browse Courses') }}</h1>
                <p class="mt-1 text-neutral-500 dark:text-neutral-400">{{ __('Find and enroll in available courses') }}</p>
            </div>
            <flux:button :href="route('student.courses.index')" variant="ghost">
                <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12" />
                </svg>
                {{ __('Back to My Courses') }}
            </flux:button>
        </div>
    </div>

    <!-- Stats Summary -->
    <div class="mb-6 grid gap-4 md:grid-cols-3">
        <div class="rounded-xl border border-neutral-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-violet-100 text-violet-600 dark:bg-violet-900 dark:text-violet-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Available Courses') }}</p>
                    <p class="text-xl font-bold text-neutral-900 dark:text-neutral-100">{{ $sections->count() }}</p>
                </div>
            </div>
        </div>
        @if($currentSemester)
        <div class="rounded-xl border border-neutral-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100 text-blue-600 dark:bg-blue-900 dark:text-blue-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Current Semester') }}</p>
                    <p class="text-xl font-bold text-neutral-900 dark:text-neutral-100">{{ $currentSemester->name }}</p>
                </div>
            </div>
        </div>
        <div class="rounded-xl border border-neutral-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="flex items-center gap-3">
                @php
                    $enrollmentStatus = $currentSemester->getEnrollmentStatus();
                @endphp
                <div class="flex h-10 w-10 items-center justify-center rounded-lg @if($enrollmentStatus === 'open') bg-green-100 text-green-600 dark:bg-green-900 dark:text-green-400 @elseif($enrollmentStatus === 'upcoming') bg-amber-100 text-amber-600 dark:bg-amber-900 dark:text-amber-400 @else bg-red-100 text-red-600 dark:bg-red-900 dark:text-red-400 @endif">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Enrollment Status') }}</p>
                    <p class="text-xl font-bold @if($enrollmentStatus === 'open') text-green-600 dark:text-green-400 @elseif($enrollmentStatus === 'upcoming') text-amber-600 dark:text-amber-400 @else text-red-600 dark:text-red-400 @endif">
                        @if($enrollmentStatus === 'open') {{ __('Open') }} @elseif($enrollmentStatus === 'upcoming') {{ __('Upcoming') }} @else {{ __('Closed') }} @endif
                    </p>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Enrollment Period Status Banner -->
    @if($currentSemester)
        @php
            $enrollmentStatus = $currentSemester->getEnrollmentStatus();
            $dropStatus = $currentSemester->getDropStatus();
        @endphp

        <div class="mb-6 rounded-xl border overflow-hidden
            @if($enrollmentStatus === 'open')
                border-green-200 bg-gradient-to-r from-green-50 to-emerald-50 dark:border-green-800 dark:from-green-900/20 dark:to-emerald-900/20
            @elseif($enrollmentStatus === 'upcoming')
                border-amber-200 bg-gradient-to-r from-amber-50 to-yellow-50 dark:border-amber-800 dark:from-amber-900/20 dark:to-yellow-900/20
            @else
                border-red-200 bg-gradient-to-r from-red-50 to-rose-50 dark:border-red-800 dark:from-red-900/20 dark:to-rose-900/20
            @endif">
            <div class="p-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl
                            @if($enrollmentStatus === 'open')
                                bg-green-100 dark:bg-green-800
                            @elseif($enrollmentStatus === 'upcoming')
                                bg-amber-100 dark:bg-amber-800
                            @else
                                bg-red-100 dark:bg-red-800
                            @endif">
                            @if($enrollmentStatus === 'open')
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            @elseif($enrollmentStatus === 'upcoming')
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            @endif
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-medium
                                    @if($enrollmentStatus === 'open')
                                        text-green-700 dark:text-green-300
                                    @elseif($enrollmentStatus === 'upcoming')
                                        text-amber-700 dark:text-amber-300
                                    @else
                                        text-red-700 dark:text-red-300
                                    @endif">
                                    {{ __('Enrollment Period') }}:
                                </span>
                                @if($enrollmentStatus === 'open')
                                    <span class="inline-flex items-center rounded-full bg-green-100 px-3 py-1 text-sm font-semibold text-green-800 dark:bg-green-800 dark:text-green-200">
                                        <span class="mr-1.5 h-2 w-2 rounded-full bg-green-500 animate-pulse"></span>
                                        {{ __('Open') }}
                                    </span>
                                @elseif($enrollmentStatus === 'upcoming')
                                    <span class="inline-flex items-center rounded-full bg-amber-100 px-3 py-1 text-sm font-semibold text-amber-800 dark:bg-amber-800 dark:text-amber-200">
                                        {{ __('Upcoming') }}
                                    </span>
                                @elseif($enrollmentStatus === 'not_configured')
                                    <span class="inline-flex items-center rounded-full bg-neutral-100 px-3 py-1 text-sm font-semibold text-neutral-600 dark:bg-neutral-700 dark:text-neutral-300">
                                        {{ __('Not Configured') }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-red-100 px-3 py-1 text-sm font-semibold text-red-800 dark:bg-red-800 dark:text-red-200">
                                        {{ __('Closed') }}
                                    </span>
                                @endif
                            </div>
                            @if($currentSemester->enrollment_start_date && $currentSemester->enrollment_end_date)
                                <p class="mt-1 text-sm text-neutral-600 dark:text-neutral-400">
                                    {{ $currentSemester->enrollment_start_date->format('M d, Y') }} - {{ $currentSemester->enrollment_end_date->format('M d, Y') }}
                                </p>
                            @endif
                        </div>
                    </div>

                    <!-- Drop Period Status -->
                    <div class="text-right">
                        <div class="flex items-center justify-end gap-2">
                            <span class="text-sm font-medium
                                @if($dropStatus === 'open')
                                    text-green-700 dark:text-green-300
                                @elseif($dropStatus === 'upcoming')
                                    text-amber-700 dark:text-amber-300
                                @else
                                    text-neutral-600 dark:text-neutral-400
                                @endif">
                                {{ __('Drop Period') }}:
                            </span>
                            @if($dropStatus === 'open')
                                <span class="inline-flex items-center rounded-full bg-green-100 px-3 py-1 text-sm font-semibold text-green-800 dark:bg-green-800 dark:text-green-200">
                                    <span class="mr-1.5 h-2 w-2 rounded-full bg-green-500 animate-pulse"></span>
                                    {{ __('Open') }}
                                </span>
                            @elseif($dropStatus === 'upcoming')
                                <span class="inline-flex items-center rounded-full bg-amber-100 px-3 py-1 text-sm font-semibold text-amber-800 dark:bg-amber-800 dark:text-amber-200">
                                    {{ __('Upcoming') }}
                                </span>
                            @elseif($dropStatus === 'not_configured')
                                <span class="inline-flex items-center rounded-full bg-neutral-100 px-3 py-1 text-sm font-semibold text-neutral-600 dark:bg-neutral-700 dark:text-neutral-300">
                                    {{ __('Not Configured') }}
                                </span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-neutral-100 px-3 py-1 text-sm font-semibold text-neutral-600 dark:bg-neutral-700 dark:text-neutral-300">
                                    {{ __('Closed') }}
                                </span>
                            @endif
                        </div>
                        @if($currentSemester->drop_start_date && $currentSemester->drop_end_date)
                            <p class="mt-1 text-sm text-neutral-600 dark:text-neutral-400">
                                {{ $currentSemester->drop_start_date->format('M d, Y') }} - {{ $currentSemester->drop_end_date->format('M d, Y') }}
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Filters -->
    <div class="mb-6 rounded-xl border border-neutral-200 bg-white p-4 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
        <div class="flex flex-wrap items-center gap-4">
            <div class="relative flex-1 min-w-[200px]">
                <input type="text" placeholder="{{ __('Search courses...') }}" class="w-full rounded-lg border border-neutral-300 py-2 pl-10 pr-4 text-sm focus:border-indigo-500 focus:outline-none dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-100">
                <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-2.5 size-4 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <select class="rounded-lg border border-neutral-300 py-2 pl-3 pr-8 text-sm focus:border-indigo-500 focus:outline-none dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-100">
                <option value="">{{ __('All Departments') }}</option>
            </select>
            <select class="rounded-lg border border-neutral-300 py-2 pl-3 pr-8 text-sm focus:border-indigo-500 focus:outline-none dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-100">
                <option value="">{{ __('All Semesters') }}</option>
            </select>
        </div>
    </div>

    <!-- Course List -->
    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
        @forelse($sections as $section)
        <div class="group relative overflow-hidden rounded-2xl border border-neutral-200 bg-white shadow-sm transition-all duration-300 hover:shadow-lg dark:border-neutral-700 dark:bg-neutral-800">
            <!-- Gradient Bar -->
            <div class="h-1 bg-gradient-to-r from-violet-500 via-purple-500 to-fuchsia-500"></div>

            <div class="p-6">
                <!-- Course Header -->
                <div class="mb-4">
                    <h3 class="text-lg font-bold text-neutral-900 dark:text-neutral-100">{{ $section->course?->name ?? __('Unknown Course') }}</h3>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ $section->course?->code ?? '-' }} - {{ $section->section_name }}</p>
                </div>

                <!-- Details -->
                <div class="mb-4 space-y-2 text-sm">
                    <div class="flex items-center gap-2 text-neutral-600 dark:text-neutral-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        {{ $section->teacher?->full_name ?? __('Not assigned') }}
                    </div>
                    <div class="flex items-center gap-2 text-neutral-600 dark:text-neutral-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        {{ $section->enrollments->count() }}/{{ $section->capacity ?? '∞' }} {{ __('Students') }}
                    </div>
                    <div class="flex items-center gap-2 text-neutral-600 dark:text-neutral-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ $section->course?->credits ?? '0' }} {{ __('Credits') }}
                    </div>
                    @if($section->schedule)
                    <div class="flex items-center gap-2 text-neutral-600 dark:text-neutral-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ $section->schedule }}
                    </div>
                    @endif
                </div>

                <!-- Action -->
                @if($currentSemester && $currentSemester->isEnrollmentOpen())
                    <form method="POST" action="{{ route('student.courses.enroll') }}">
                        @csrf
                        <input type="hidden" name="offering_id" value="{{ $section->id }}">
                        <x-button.submit loading-text="Enrolling..." variant="primary" class="w-full justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Enroll Now
                        </x-button.submit>
                    </form>
                @else
                    <flux:button variant="subtle" disabled class="w-full justify-center cursor-not-allowed opacity-60">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        {{ __('Enrollment Closed') }}
                    </flux:button>
                @endif
            </div>
        </div>
        @empty
        <div class="col-span-full">
            <div class="flex flex-col items-center justify-center rounded-2xl border-2 border-dashed border-neutral-300 py-16 dark:border-neutral-700">
                <div class="mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-neutral-100 dark:bg-neutral-800">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-10 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-neutral-900 dark:text-neutral-100">{{ __('No courses available') }}</h3>
                <p class="mt-2 text-neutral-500 dark:text-neutral-400">{{ __('Check back later for new course offerings') }}</p>
            </div>
        </div>
        @endforelse
    </div>
</x-layouts::app>
