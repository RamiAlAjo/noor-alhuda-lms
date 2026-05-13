{{--
    =============================================================================
    TEACHER CALENDAR VIEW
    =============================================================================

    Purpose: Display a calendar view showing course schedules, assessment deadlines,
    and important dates for the teacher's courses.

    Route: teacher.calendar
    Controller: Teacher\DashboardController@calendar

    Features:
    - Monthly calendar view with course events
    - Assessment deadlines with links to grading
    - Course start/end dates
    - Color-coded event types
    - Quick navigation between months

    Required Data:
    - $courses: Collection of teacher's course offerings
    - $allEvents: Array of calendar events (assessments, course dates)

    =============================================================================
--}}
<x-layouts::app :title="__('Course Calendar')">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-neutral-900 dark:text-neutral-100">{{ __('Course Calendar') }}</h1>
                <p class="mt-1 text-neutral-500 dark:text-neutral-400">{{ __('View your course schedules and important dates') }}</p>
            </div>
            <div class="flex items-center space-x-3">
                <flux:button href="{{ route('teacher.dashboard') }}" variant="outline">
                    {{ __('Back to Dashboard') }}
                </flux:button>
            </div>
        </div>
    </div>

    <!-- Calendar Container -->
    <div class="bg-white dark:bg-neutral-800 rounded-lg shadow-sm border border-neutral-200 dark:border-neutral-700 overflow-hidden">
        <div class="p-6">
            <div id="calendar" class="w-full"></div>
        </div>
    </div>

    <!-- Event Details Modal -->
    <div id="eventModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white dark:bg-neutral-800 rounded-lg max-w-md w-full p-6">
                <div class="flex items-start justify-between mb-4">
                    <h3 id="eventTitle" class="text-lg font-semibold text-neutral-900 dark:text-neutral-100"></h3>
                    <button id="closeModal" class="text-neutral-400 hover:text-neutral-600 dark:hover:text-neutral-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div id="eventDetails" class="space-y-3">
                    <!-- Event details will be populated by JavaScript -->
                </div>
                <div class="mt-6 flex justify-end space-x-3">
                    <button id="viewEvent" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 hidden">
                        {{ __('View Details') }}
                    </button>
                    <button id="closeModalBtn" class="px-4 py-2 bg-neutral-200 dark:bg-neutral-700 text-neutral-800 dark:text-neutral-200 rounded-lg hover:bg-neutral-300 dark:hover:bg-neutral-600">
                        {{ __('Close') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Include FullCalendar CSS and JS -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const calendarEl = document.getElementById('calendar');
            const modal = document.getElementById('eventModal');
            const modalTitle = document.getElementById('eventTitle');
            const modalDetails = document.getElementById('eventDetails');
            const viewEventBtn = document.getElementById('viewEvent');
            const closeModalBtns = [document.getElementById('closeModal'), document.getElementById('closeModalBtn')];

            // Calendar events data
            const events = @json($allEvents);

            // Initialize FullCalendar
            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                events: events.map(event => ({
                    id: event.id,
                    title: event.title,
                    start: event.start,
                    end: event.end,
                    extendedProps: event,
                    className: getEventClass(event.type)
                })),
                eventClick: function(info) {
                    const event = info.event.extendedProps;

                    modalTitle.textContent = event.title;

                    modalDetails.innerHTML = `
                        <div class="flex items-center space-x-2">
                            <span class="inline-block w-3 h-3 rounded-full ${getEventColor(event.type)}"></span>
                            <span class="text-sm font-medium text-neutral-600 dark:text-neutral-400 capitalize">${event.type}</span>
                        </div>
                        ${event.course ? `<p class="text-sm text-neutral-700 dark:text-neutral-300"><strong>{{ __('Course') }}:</strong> ${event.course}</p>` : ''}
                        ${event.section ? `<p class="text-sm text-neutral-700 dark:text-neutral-300"><strong>{{ __('Section') }}:</strong> ${event.section}</p>` : ''}
                        ${event.description ? `<p class="text-sm text-neutral-700 dark:text-neutral-300"><strong>{{ __('Description') }}:</strong> ${event.description}</p>` : ''}
                        <p class="text-sm text-neutral-700 dark:text-neutral-300"><strong>{{ __('Date') }}:</strong> ${new Date(event.start).toLocaleDateString()}</p>
                    `;

                    if (event.url) {
                        viewEventBtn.classList.remove('hidden');
                        viewEventBtn.onclick = () => window.location.href = event.url;
                    } else {
                        viewEventBtn.classList.add('hidden');
                    }

                    modal.classList.remove('hidden');
                },
                height: 'auto',
                dayMaxEvents: true,
                moreLinkClick: 'popover',
                locale: '{{ app()->getLocale() }}'
            });

            calendar.render();

            // Close modal handlers
            closeModalBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    modal.classList.add('hidden');
                });
            });

            // Close modal on outside click
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.classList.add('hidden');
                }
            });
        });

        function getEventClass(type) {
            switch (type) {
                case 'assessment':
                    return 'bg-red-500 text-white border-red-600';
                case 'course':
                    return 'bg-blue-500 text-white border-blue-600';
                default:
                    return 'bg-neutral-500 text-white border-neutral-600';
            }
        }

        function getEventColor(type) {
            switch (type) {
                case 'assessment':
                    return 'bg-red-500';
                case 'course':
                    return 'bg-blue-500';
                default:
                    return 'bg-neutral-500';
            }
        }
    </script>

    <!-- Add navigation link to sidebar -->
    <script>
        // Add calendar link to teacher sidebar if it exists
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.querySelector('.sidebar, [role="navigation"]');
            if (sidebar) {
                const calendarLink = document.createElement('a');
                calendarLink.href = '{{ route("teacher.calendar") }}';
                calendarLink.className = 'flex items-center px-4 py-2 text-sm font-medium text-neutral-700 dark:text-neutral-300 rounded-lg hover:bg-neutral-100 dark:hover:bg-neutral-700';
                calendarLink.innerHTML = `
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    {{ __('Calendar') }}
                `;
                // Insert after dashboard link or at appropriate position
                const dashboardLink = sidebar.querySelector('a[href*="dashboard"]');
                if (dashboardLink && dashboardLink.parentElement) {
                    dashboardLink.parentElement.after(calendarLink);
                }
            }
        });
    </script>
</x-layouts::app>