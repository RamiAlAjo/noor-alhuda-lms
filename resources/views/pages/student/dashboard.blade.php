{{--
    =============================================================================
    STUDENT DASHBOARD VIEW
    =============================================================================

    Purpose: Main dashboard for student users showing enrolled courses,
    grades overview, upcoming tasks, and quick access to learning features.

    Route: student.dashboard
    Controller: Student\DashboardController@index

    Components:
    - Hero section with personalized greeting and key stats
    - Quick actions (browse courses, my courses, grades, transcript)
    - My Courses section showing enrolled offerings
    - Timeline section for upcoming activities
    - Recent notifications section

    Required Data:
    - $enrolled_courses: Collection of student's course enrollments
    - $gpa: Student's current GPA
    - $upcoming_assessments: Upcoming quizzes/assignments
    - $timeline: Upcoming activities and events
    - $recentNotifications: Recent notification items

    =============================================================================
--}}
<x-layouts::app :title="__('Student Dashboard')">
    <!-- Hero Section with Personalized Greeting -->
    <div class="mb-8 rounded-2xl bg-gradient-to-r from-violet-500 via-purple-500 to-fuchsia-500 p-8 text-white shadow-xl">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold">{{ __('Welcome back, :name!', ['name' => auth()->user()->first_name ?? auth()->user()->name]) }}</h1>
                <p class="mt-2 text-violet-100">{{ __('Track your courses and academic progress') }}</p>
            </div>
            <div class="hidden text-8xl opacity-20">🎓</div>
        </div>

        <!-- Quick Stats -->
        <div class="mt-6 grid grid-cols-2 gap-4 md:grid-cols-4">
            <div class="rounded-xl bg-white/20 backdrop-blur-sm p-4">
                <p class="text-sm text-violet-100">{{ __('Enrolled Courses') }}</p>
                <p class="text-2xl font-bold">{{ $enrolled_courses->count() }}</p>
            </div>
            <div class="rounded-xl bg-white/20 backdrop-blur-sm p-4">
                <p class="text-sm text-violet-100">{{ __('Current GPA') }}</p>
                <p class="text-2xl font-bold">{{ number_format($gpa, 2) }}</p>
            </div>
            <div class="rounded-xl bg-white/20 backdrop-blur-sm p-4">
                <p class="text-sm text-violet-100">{{ __('Overall Progress') }}</p>
                <p class="text-2xl font-bold">{{ $progress ?? 0 }}%</p>
            </div>
            <div class="rounded-xl bg-white/20 backdrop-blur-sm p-4">
                <p class="text-sm text-violet-100">{{ __('Financial Balance') }}</p>
                <p class="text-2xl font-bold">{{ number_format($financialBalance['balance'] ?? 0, 2) }}</p>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="mb-6 grid grid-cols-2 gap-4 md:grid-cols-4">
        <a href="{{ route('student.courses.browse') }}" class="group relative overflow-hidden rounded-xl border border-neutral-200 bg-white p-5 transition-all hover:shadow-lg hover:shadow-indigo-500/20 dark:border-neutral-700 dark:bg-neutral-800 hover:border-indigo-300 dark:hover:border-indigo-700">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-50 to-indigo-50 opacity-0 transition-opacity group-hover:opacity-100 dark:from-blue-900/20 dark:to-indigo-900/20"></div>
            <div class="relative">
                <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100 text-blue-600 dark:bg-blue-900 dark:text-blue-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <h3 class="font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Browse Courses') }}</h3>
            </div>
        </a>

        <a href="{{ route('student.courses.index') }}" class="group relative overflow-hidden rounded-xl border border-neutral-200 bg-white p-5 transition-all hover:shadow-lg dark:border-neutral-700 dark:bg-neutral-800">
            <div class="absolute inset-0 bg-gradient-to-br from-green-50 to-emerald-50 opacity-0 transition-opacity group-hover:opacity-100 dark:from-green-900/20 dark:to-emerald-900/20"></div>
            <div class="relative">
                <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-lg bg-green-100 text-green-600 dark:bg-green-900 dark:text-green-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
                <h3 class="font-semibold text-neutral-900 dark:text-neutral-100">{{ __('My Courses') }}</h3>
            </div>
        </a>

        <a href="{{ route('student.grades') }}" class="group relative overflow-hidden rounded-xl border border-neutral-200 bg-white p-5 transition-all hover:shadow-lg dark:border-neutral-700 dark:bg-neutral-800">
            <div class="absolute inset-0 bg-gradient-to-br from-purple-50 to-pink-50 opacity-0 transition-opacity group-hover:opacity-100 dark:from-purple-900/20 dark:to-pink-900/20"></div>
            <div class="relative">
                <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-lg bg-purple-100 text-purple-600 dark:bg-purple-900 dark:text-purple-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
                <h3 class="font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Grades') }}</h3>
            </div>
        </a>

        <a href="{{ route('student.transcript.index') }}" class="group relative overflow-hidden rounded-xl border border-neutral-200 bg-white p-5 transition-all hover:shadow-lg dark:border-neutral-700 dark:bg-neutral-800">
            <div class="absolute inset-0 bg-gradient-to-br from-orange-50 to-amber-50 opacity-0 transition-opacity group-hover:opacity-100 dark:from-orange-900/20 dark:to-amber-900/20"></div>
            <div class="relative">
                <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-lg bg-orange-100 text-orange-600 dark:bg-orange-900 dark:text-orange-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <h3 class="font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Transcript') }}</h3>
            </div>
        </a>

        <a href="{{ route('messages.index') }}" class="group relative overflow-hidden rounded-xl border border-neutral-200 bg-white p-5 transition-all hover:shadow-lg dark:border-neutral-700 dark:bg-neutral-800">
            <div class="absolute inset-0 bg-gradient-to-br from-purple-50 to-pink-50 opacity-0 transition-opacity group-hover:opacity-100 dark:from-purple-900/20 dark:to-pink-900/20"></div>
            <div class="relative">
                <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-lg bg-purple-100 text-purple-600 dark:bg-purple-900 dark:text-purple-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                </div>
                <h3 class="font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Messages') }}</h3>
            </div>
        </a>
    </div>

    <!-- Main Content Grid -->
    <div class="grid gap-6 lg:grid-cols-3">
        <!-- My Courses -->
        <div class="rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800 lg:col-span-2">

        <!-- Weekly Schedule -->
        @if(isset($weeklySchedule) && count($weeklySchedule) > 0)
        <div class="rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800 mb-6">
            <div class="border-b border-neutral-200 px-6 py-4 dark:border-neutral-700">
                <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Weekly Schedule') }}</h2>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @php
                        $days = [__('Sunday'), __('Monday'), __('Tuesday'), __('Wednesday'), __('Thursday'), __('Friday'), __('Saturday')];
                    @endphp
                    @for($i = 0; $i < 7; $i++)
                        @if(isset($weeklySchedule[$i]) && count($weeklySchedule[$i]) > 0)
                        <div class="border-b border-neutral-100 pb-3 last:border-b-0 dark:border-neutral-700">
                            <h3 class="font-medium text-neutral-900 dark:text-neutral-100 mb-2">{{ $days[$i] }}</h3>
                            <div class="space-y-2">
                                @foreach($weeklySchedule[$i] as $slot)
                                <div class="flex items-center gap-3 rounded-lg bg-neutral-50 p-2 dark:bg-neutral-700/50">
                                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-100 text-blue-600 dark:bg-blue-900 dark:text-blue-400">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-neutral-900 dark:text-neutral-100">{{ $slot['course'] }}</p>
                                        <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ $slot['time'] }} @if($slot['room']) - {{ $slot['room'] }} @endif @if($slot['is_online']) ({{ __('Online') }}) @endif</p>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    @endfor
                </div>
            </div>
        </div>
        @endif

        <!-- My Courses -->
            <div class="border-b border-neutral-200 px-6 py-4 dark:border-neutral-700">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('My Courses') }}</h2>
                    <flux:button size="sm" variant="subtle" :href="route('student.courses.index')">{{ __('View All') }}</flux:button>
                </div>
            </div>
            <div class="p-6">
                @forelse($enrolled_courses->take(4) as $enrollment)
                @if($enrollment->offering)
                <a href="{{ route('student.courses.show', $enrollment->offering) }}" class="mb-4 flex items-center gap-4 rounded-lg border border-neutral-200 p-4 transition-all hover:border-violet-300 hover:shadow-md dark:border-neutral-700 dark:hover:border-violet-600">
                    <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-violet-100 text-violet-600 dark:bg-violet-900 dark:text-violet-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-semibold text-neutral-900 dark:text-neutral-100">{{ $enrollment->offering?->course?->name ?? __('Unknown Course') }}</h3>
                        <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Section') }} {{ $enrollment->offering?->section_name ?? '' }} - {{ $enrollment->offering?->course?->code ?? '' }}</p>
                    </div>
                    <div class="text-right">
                        <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800 dark:bg-green-900 dark:text-green-200">
                            {{ $enrollment->status }}
                        </span>
                    </div>
                </a>
                @endif
                @empty
                <div class="py-8 text-center">
                    <div class="mb-4 flex justify-center">
                        <div class="rounded-full bg-neutral-100 p-4 dark:bg-neutral-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-8 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                    </div>
                    <h3 class="font-medium text-neutral-900 dark:text-neutral-100">{{ __('No enrolled courses') }}</h3>
                    <p class="mt-1 text-neutral-500 dark:text-neutral-400">{{ __('Browse courses to enroll') }}</p>
                    <flux:button class="mt-4" :href="route('student.courses.browse')">{{ __('Browse Courses') }}</flux:button>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Timeline / Upcoming Activities -->
        <div class="rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="border-b border-neutral-200 px-6 py-4 dark:border-neutral-700">
                <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Timeline') }}</h2>
            </div>
            <div class="p-6">
                @forelse($timeline as $item)
                <div class="mb-4 flex items-start gap-3 rounded-lg border border-neutral-200 p-3 dark:border-neutral-700">
                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-{{ $item['color'] }}-100 text-{{ $item['color'] }}-600 dark:bg-{{ $item['color'] }}-900 dark:text-{{ $item['color'] }}-300">
                        @if($item['icon'] === 'clipboard')
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        @else
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        @endif
                    </div>
                    <div class="flex-1">
                        <h4 class="font-medium text-neutral-900 dark:text-neutral-100">{{ $item['title'] }}</h4>
                        @if($item['course'])
                        <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ $item['course'] }}</p>
                        @endif
                        @if($item['date'])
                        <p class="mt-1 text-xs font-medium text-{{ $item['color'] }}-600 dark:text-{{ $item['color'] }}-400">{{ \Carbon\Carbon::parse($item['date'])->format('M d, Y') }}</p>
                        @endif
                    </div>
                </div>
                @empty
                <div class="py-6 text-center">
                    <p class="text-neutral-500 dark:text-neutral-400">{{ __('No activities require action') }}</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Recent Notifications Section -->
    @if($recentNotifications->count() > 0)
    <div class="mt-6 rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
        <div class="border-b border-neutral-200 px-6 py-4 dark:border-neutral-700">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Recent Notifications') }}</h2>
                @if(isset($unreadNotificationsCount) && $unreadNotificationsCount > 0)
                <span class="inline-flex items-center rounded-full bg-violet-100 px-2.5 py-0.5 text-xs font-medium text-violet-800 dark:bg-violet-900 dark:text-violet-200">
                    {{ $unreadNotificationsCount }} {{ __('unread') }}
                </span>
                @endif
            </div>
        </div>
        <div class="p-6">
            <div class="space-y-3">
                @foreach($recentNotifications as $notification)
                <div class="flex items-start gap-3 rounded-lg p-3 {{ $notification->is_read ? 'bg-transparent' : 'bg-violet-50 dark:bg-violet-900/20' }}">
                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-{{ $notification->is_read ? 'neutral' : 'violet' }}-100 text-{{ $notification->is_read ? 'neutral' : 'violet' }}-600 dark:bg-{{ $notification->is_read ? 'neutral' : 'violet' }}-900 dark:text-{{ $notification->is_read ? 'neutral' : 'violet' }}-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-medium text-neutral-900 dark:text-neutral-100">{{ $notification->title }}</h4>
                        <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ $notification->content }}</p>
                        <p class="mt-1 text-xs text-neutral-400">{{ $notification->created_at->diffForHumans() }}</p>
                    </div>
                    @if(!$notification->is_read)
                    <span class="inline-flex h-2 w-2 rounded-full bg-violet-500"></span>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- News & Announcements Section -->
    @if(isset($news) && $news->count() > 0)
    <div class="mt-6 rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
        <div class="border-b border-neutral-200 px-6 py-4 dark:border-neutral-700">
            <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('News & Announcements') }}</h2>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                @foreach($news as $announcement)
                <div class="rounded-lg border border-neutral-200 p-4 dark:border-neutral-700">
                    <div class="flex items-start gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 text-blue-600 dark:bg-blue-900 dark:text-blue-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-semibold text-neutral-900 dark:text-neutral-100">{{ $announcement->title }}</h4>
                            <p class="mt-1 text-sm text-neutral-600 dark:text-neutral-400">{{ Str::limit($announcement->content, 150) }}</p>
                            <p class="mt-2 text-xs text-neutral-500 dark:text-neutral-400">{{ $announcement->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Analytics Section -->
    <div class="mt-6 grid gap-6 md:grid-cols-2 lg:grid-cols-4">
        <!-- GPA Trend -->
        <div class="rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-green-100 text-green-600 dark:bg-green-900 dark:text-green-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('GPA Trend') }}</p>
                    <p class="text-2xl font-bold text-neutral-900 dark:text-neutral-100">{{ number_format($gpa, 2) }}</p>
                    <p class="text-xs text-green-600 dark:text-green-400">{{ __('+0.2 from last semester') }}</p>
                </div>
            </div>
        </div>

        <!-- Course Completion Rate -->
        <div class="rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100 text-blue-600 dark:bg-blue-900 dark:text-blue-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Completion Rate') }}</p>
                    <p class="text-2xl font-bold text-neutral-900 dark:text-neutral-100">{{ $progress ?? 0 }}%</p>
                    <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('Current semester') }}</p>
                </div>
            </div>
        </div>

        <!-- Attendance Rate -->
        <div class="rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-purple-100 text-purple-600 dark:bg-purple-900 dark:text-purple-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Attendance Rate') }}</p>
                    <p class="text-2xl font-bold text-neutral-900 dark:text-neutral-100">95%</p>
                    <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('This semester') }}</p>
                </div>
            </div>
        </div>

        <!-- Study Hours -->
        <div class="rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-orange-100 text-orange-600 dark:bg-orange-900 dark:text-orange-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Study Hours') }}</p>
                    <p class="text-2xl font-bold text-neutral-900 dark:text-neutral-100">24h</p>
                    <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('This week') }}</p>
                </div>
            </div>
        </div>
    </div>
</x-layouts::app>
