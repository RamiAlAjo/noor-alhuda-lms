<x-layouts::app :title="__('Calendar')">
    <!-- Hero Header -->
    <div class="relative mb-8 overflow-hidden rounded-2xl bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-500 px-8 py-10 shadow-xl">
        <div class="absolute inset-0 bg-white/10"></div>
        <div class="relative flex items-center justify-between">
            <div class="text-white">
                <h1 class="text-4xl font-bold">{{ __('Calendar') }}</h1>
                <p class="mt-2 text-indigo-100">{{ __('View and manage your events') }}</p>
            </div>
            <div class="flex items-center gap-4">
                <div class="hidden items-center gap-2 rounded-xl bg-white/20 px-4 py-2 text-white md:flex">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span class="font-medium">{{ \Carbon\Carbon::createFromDate($year, $month)->format('F Y') }}</span>
                </div>
                <flux:button variant="primary" onclick="document.getElementById('addEventModal').showModal()">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    {{ __('Add Event') }}
                </flux:button>
            </div>
        </div>
        <!-- Wave decoration -->
        <div class="absolute bottom-0 left-0 right-0 h-4 bg-white/20"></div>
    </div>

    <!-- Month Navigation -->
    <div class="mb-6 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <flux:button :href="route('calendar.index', ['month' => $month - 1, 'year' => $year])" variant="ghost">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </flux:button>
            <h2 class="min-w-[180px] text-center text-2xl font-bold text-neutral-900 dark:text-neutral-100">
                {{ \Carbon\Carbon::createFromDate($year, $month)->format('F Y') }}
            </h2>
            <flux:button :href="route('calendar.index', ['month' => $month + 1, 'year' => $year])" variant="ghost">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </flux:button>
        </div>
        <flux:button :href="route('calendar.index', ['month' => date('n'), 'year' => date('Y')])" variant="subtle" size="sm">
            {{ __('Today') }}
        </flux:button>
    </div>

    <!-- Calendar Grid -->
    <div class="overflow-hidden rounded-2xl border border-neutral-200 bg-white shadow-lg dark:border-neutral-700 dark:bg-neutral-800">
        <!-- Day Headers -->
        <div class="grid grid-cols-7 border-b border-neutral-200 bg-gradient-to-r from-neutral-50 to-neutral-100 dark:border-neutral-700 dark:from-neutral-800 dark:to-neutral-700">
            @foreach([__('Sun'), __('Mon'), __('Tue'), __('Wed'), __('Thu'), __('Fri'), __('Sat')] as $day)
                <div class="px-2 py-4 text-center text-sm font-bold uppercase tracking-wider text-neutral-600 dark:text-neutral-400">
                    {{ $day }}
                </div>
            @endforeach
        </div>

        <!-- Calendar Days -->
        <div class="grid grid-cols-7 divide-x divide-neutral-200 dark:divide-neutral-700">
            @php
                $firstDay = \Carbon\Carbon::createFromDate($year, $month, 1);
                $daysInMonth = $firstDay->daysInMonth;
                $startDayOfWeek = $firstDay->dayOfWeek;
                $currentDay = 1;
            @endphp

            @for($week = 0; $week < 6; $week++)
                @if($currentDay > $daysInMonth) @break @endif
                @for($day = 0; $day < 7; $day++)
                    @if(($week == 0 && $day < $startDayOfWeek) || $currentDay > $daysInMonth)
                        <div class="min-h-[140px] bg-neutral-50/50 p-2 dark:bg-neutral-800/50"></div>
                    @else
                        @php
                            $date = \Carbon\Carbon::createFromDate($year, $month, $currentDay);
                            $dayEvents = $events->filter(function($event) use ($date) {
                                return $event->start_date->format('Y-m-d') == $date->format('Y-m-d');
                            });
                            $isToday = $date->isToday();
                        @endphp
                        <div class="min-h-[140px] p-2 transition-colors hover:bg-neutral-50 dark:hover:bg-neutral-700/30
                            {{ $isToday ? 'bg-indigo-50/50 dark:bg-indigo-900/20' : '' }}">
                            <div class="mb-2 flex items-center justify-between">
                                <span class="flex h-8 w-8 items-center justify-center rounded-full text-sm font-semibold
                                    {{ $isToday
                                        ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-300 dark:shadow-indigo-900'
                                        : 'text-neutral-700 dark:text-neutral-300' }}">
                                    {{ $currentDay }}
                                </span>
                                @if($dayEvents->count() > 0)
                                    <span class="rounded-full bg-neutral-200 px-2 py-0.5 text-xs font-medium text-neutral-600 dark:bg-neutral-700 dark:text-neutral-400">
                                        {{ $dayEvents->count() }}
                                    </span>
                                @endif
                            </div>
                            <div class="space-y-1.5">
                                @foreach($dayEvents->take(3) as $event)
                                    @php
                                        $colorClass = match($event->event_type) {
                                            'exam' => 'bg-red-500 text-white border-red-600',
                                            'class' => 'bg-blue-500 text-white border-blue-600',
                                            'assignment' => 'bg-yellow-500 text-white border-yellow-600',
                                            'meeting' => 'bg-purple-500 text-white border-purple-600',
                                            'personal' => 'bg-green-500 text-white border-green-600',
                                            default => 'bg-neutral-500 text-white border-neutral-600'
                                        };
                                    @endphp
                                    <div class="rounded-md border-l-4 px-2 py-1.5 text-xs font-medium truncate {{ $colorClass }}"
                                        title="{{ $event->title }}">
                                        {{ Str::limit($event->title, 18) }}
                                    </div>
                                @endforeach
                                @if($dayEvents->count() > 3)
                                    <div class="text-center text-xs font-medium text-neutral-500 dark:text-neutral-400">
                                        +{{ $dayEvents->count() - 3 }} {{ __('more') }}
                                    </div>
                                @endif
                            </div>
                        </div>
                        @php $currentDay++; @endphp
                    @endif
                @endfor
            @endfor
        </div>
    </div>

    <!-- Calendar Stats -->
    <div class="mb-8 grid gap-4 md:grid-cols-4">
        <div class="rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="flex items-center gap-4">
                <div class="rounded-full bg-indigo-100 p-3 dark:bg-indigo-900/30">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-6 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Total Events') }}</p>
                    <p class="text-2xl font-bold text-neutral-900 dark:text-neutral-100">{{ $events->count() }}</p>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="flex items-center gap-4">
                <div class="rounded-full bg-red-100 p-3 dark:bg-red-900/30">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-6 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Exams') }}</p>
                    <p class="text-2xl font-bold text-neutral-900 dark:text-neutral-100">{{ $events->where('event_type', 'exam')->count() }}</p>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="flex items-center gap-4">
                <div class="rounded-full bg-blue-100 p-3 dark:bg-blue-900/30">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-6 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Classes') }}</p>
                    <p class="text-2xl font-bold text-neutral-900 dark:text-neutral-100">{{ $events->where('event_type', 'class')->count() }}</p>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="flex items-center gap-4">
                <div class="rounded-full bg-green-100 p-3 dark:bg-green-900/30">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-6 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Upcoming') }}</p>
                    <p class="text-2xl font-bold text-neutral-900 dark:text-neutral-100">{{ $events->where('start_date', '>=', now())->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Upcoming Events Section -->
    <div class="mt-10">
        <div class="mb-6 flex items-center justify-between">
            <h2 class="text-2xl font-bold text-neutral-900 dark:text-neutral-100">{{ __('Upcoming Events') }}</h2>
            <flux:badge variant="filled">{{ $events->where('start_date', '>=', now())->count() }} {{ __('events') }}</flux:badge>
        </div>

        @if($events->where('start_date', '>=', now())->isEmpty())
            <div class="rounded-2xl border-2 border-dashed border-neutral-200 bg-white p-12 text-center dark:border-neutral-700 dark:bg-neutral-800">
                <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-neutral-100 dark:bg-neutral-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-8 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <h3 class="mb-2 text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('No upcoming events') }}</h3>
                <p class="text-neutral-500 dark:text-neutral-400">{{ __('Create your first event to get started') }}</p>
            </div>
        @else
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                @foreach($events->where('start_date', '>=', now())->sortBy('start_date')->take(6) as $event)
                    @php
                        $cardClass = match($event->event_type) {
                            'exam' => 'border-l-red-500 bg-red-50 dark:bg-red-900/20',
                            'class' => 'border-l-blue-500 bg-blue-50 dark:bg-blue-900/20',
                            'assignment' => 'border-l-yellow-500 bg-yellow-50 dark:bg-yellow-900/20',
                            'meeting' => 'border-l-purple-500 bg-purple-50 dark:bg-purple-900/20',
                            'personal' => 'border-l-green-500 bg-green-50 dark:bg-green-900/20',
                            default => 'border-l-neutral-500 bg-neutral-50 dark:bg-neutral-800'
                        };
                        $badgeClass = match($event->event_type) {
                            'exam' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                            'class' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                            'assignment' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                            'meeting' => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200',
                            'personal' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                            default => 'bg-neutral-100 text-neutral-800 dark:bg-neutral-700 dark:text-neutral-300'
                        };
                    @endphp
                    <div class="group relative overflow-hidden rounded-xl border-l-4 {{ $cardClass }} border border-neutral-200 p-5 shadow-sm transition-all hover:shadow-md dark:border-neutral-700">
                        <div class="mb-3 flex items-start justify-between">
                            <flux:badge variant="filled" class="{{ $badgeClass }}">
                                {{ __($event->event_type) }}
                            </flux:badge>
                            <form action="{{ route('calendar.destroy', $event->id) }}" method="POST" class="opacity-0 transition-opacity group-hover:opacity-100">
                                @csrf
                                @method('DELETE')
                                <flux:button type="submit" size="xs" variant="danger" icon="trash" />
                            </form>
                        </div>
                        <h3 class="mb-2 text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ $event->title }}</h3>
                        <div class="flex items-center gap-2 text-sm text-neutral-500 dark:text-neutral-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            {{ $event->start_date->format('M d, Y') }}
                        </div>
                        @if($event->description)
                            <p class="mt-2 text-sm text-neutral-500 dark:text-neutral-400">{{ Str::limit($event->description, 60) }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Add Event Modal -->
    <dialog id="addEventModal" class="rounded-2xl border border-neutral-200 p-0 shadow-2xl dark:border-neutral-700 dark:bg-neutral-800" style="padding: 0; max-width: 500px;">
        <div class="rounded-2xl bg-white dark:bg-neutral-800">
            <div class="border-b border-neutral-200 bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-4 dark:border-neutral-700">
                <h3 class="text-xl font-bold text-white">{{ __('Add New Event') }}</h3>
                <p class="mt-1 text-indigo-100">{{ __('Create a new calendar event') }}</p>
            </div>
            <form method="POST" action="{{ route('calendar.store') }}" class="p-6">
                @csrf
                <div class="space-y-5">
                    <div>
                        <flux:label>{{ __('Title') }} *</flux:label>
                        <flux:input name="title" placeholder="{{ __('Event title') }}" required />
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <flux:label>{{ __('Start Date') }} *</flux:label>
                            <flux:input type="date" name="start_date" required />
                        </div>
                        <div>
                            <flux:label>{{ __('End Date') }}</flux:label>
                            <flux:input type="date" name="end_date" />
                        </div>
                    </div>
                    <div>
                        <flux:label>{{ __('Event Type') }}</flux:label>
                        <flux:select name="event_type">
                            <flux:select.option value="personal">{{ __('Personal') }}</flux:select.option>
                            <flux:select.option value="exam">{{ __('Exam') }}</flux:select.option>
                            <flux:select.option value="assignment">{{ __('Assignment') }}</flux:select.option>
                            <flux:select.option value="class">{{ __('Class') }}</flux:select.option>
                            <flux:select.option value="meeting">{{ __('Meeting') }}</flux:select.option>
                            <flux:select.option value="other">{{ __('Other') }}</flux:select.option>
                        </flux:select>
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <flux:button type="button" variant="ghost" onclick="document.getElementById('addEventModal').close()">
                        {{ __('Cancel') }}
                    </flux:button>
                    <flux:button type="submit" variant="primary">
                        {{ __('Create Event') }}
                    </flux:button>
                </div>
            </form>
        </div>
    </dialog>
</x-layouts::app>
