{{--
    =============================================================================
    TEACHER MESSAGES VIEW
    =============================================================================

    Purpose: Provide teachers with a messaging interface to communicate with
    students from their courses.

    Route: teacher.messages
    Controller: TeacherDashboardController@messages

    Features:
    - List of conversations with students
    - Filter conversations by course
    - Start new conversations with students
    - View unread message counts
    - Quick access to message templates

    Required Data:
    - $conversations: Paginated conversations with students
    - $courses: Teacher's course offerings for filtering

    =============================================================================
--}}
<x-layouts::app :title="__('Messages')">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-neutral-900 dark:text-neutral-100">{{ __('Messages') }}</h1>
                <p class="mt-1 text-neutral-500 dark:text-neutral-400">{{ __('Communicate with your students') }}</p>
            </div>
            <div class="flex items-center space-x-3">
                <flux:button href="{{ route('teacher.dashboard') }}" variant="outline">
                    {{ __('Back to Dashboard') }}
                </flux:button>
                <flux:button href="{{ route('messages.create') }}" variant="primary">
                    {{ __('New Message') }}
                </flux:button>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-4">
        <!-- Filters Sidebar -->
        <div class="lg:col-span-1">
            <div class="sticky top-6 space-y-4">
                <!-- Course Filter -->
                <div class="rounded-lg border border-neutral-200 bg-white p-4 dark:border-neutral-700 dark:bg-neutral-800">
                    <h3 class="font-semibold text-neutral-900 dark:text-neutral-100 mb-3">{{ __('Filter by Course') }}</h3>
                    <div class="space-y-2">
                        <a href="{{ route('teacher.messages', request()->except('course')) }}"
                           class="block rounded px-3 py-2 text-sm {{ !request('course') ? 'bg-blue-50 text-blue-700 dark:bg-blue-900 dark:text-blue-300' : 'text-neutral-700 hover:bg-neutral-50 dark:text-neutral-300 dark:hover:bg-neutral-700' }}">
                            {{ __('All Courses') }}
                        </a>
                        @foreach($courses as $course)
                        <a href="{{ route('teacher.messages', array_merge(request()->all(), ['course' => $course->id])) }}"
                           class="block rounded px-3 py-2 text-sm {{ request('course') == $course->id ? 'bg-blue-50 text-blue-700 dark:bg-blue-900 dark:text-blue-300' : 'text-neutral-700 hover:bg-neutral-50 dark:text-neutral-300 dark:hover:bg-neutral-700' }}">
                            {{ $course->course->name }} - {{ $course->section_name }}
                        </a>
                        @endforeach
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="rounded-lg border border-neutral-200 bg-white p-4 dark:border-neutral-700 dark:bg-neutral-800">
                    <h3 class="font-semibold text-neutral-900 dark:text-neutral-100 mb-3">{{ __('Message Stats') }}</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-neutral-600 dark:text-neutral-400">{{ __('Total Conversations') }}:</span>
                            <span class="font-medium text-neutral-900 dark:text-neutral-100">{{ $conversations->total() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-neutral-600 dark:text-neutral-400">{{ __('Unread Messages') }}:</span>
                            <span class="font-medium text-red-600 dark:text-red-400">{{ $conversations->sum('unread_count') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Conversations List -->
        <div class="lg:col-span-3">
            <div class="rounded-lg border border-neutral-200 bg-white dark:border-neutral-700 dark:bg-neutral-800">
                @if($conversations->count() > 0)
                    <div class="divide-y divide-neutral-200 dark:divide-neutral-700">
                        @foreach($conversations as $conversation)
                        <a href="{{ route('messages.conversation', $conversation) }}"
                           class="block p-4 hover:bg-neutral-50 dark:hover:bg-neutral-700 transition-colors">
                            <div class="flex items-start space-x-3">
                                <!-- Avatar -->
                                <div class="flex-shrink-0">
                                    <div class="h-10 w-10 rounded-full bg-gradient-to-br from-blue-500 to-purple-500 flex items-center justify-center text-white font-medium text-sm">
                                        @if($conversation->participants->count() > 0)
                                            {{ substr($conversation->participants->first()->name, 0, 2) }}
                                        @else
                                            ??
                                        @endif
                                    </div>
                                </div>

                                <!-- Message Content -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-2">
                                            <h4 class="text-sm font-medium text-neutral-900 dark:text-neutral-100 truncate">
                                                @if($conversation->participants->count() > 0)
                                                    {{ $conversation->participants->first()->name }}
                                                @else
                                                    {{ __('Unknown Student') }}
                                                @endif
                                            </h4>
                                            @if($conversation->unreadMessages->count() > 0)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                                    {{ $conversation->unreadMessages->count() }}
                                                </span>
                                            @endif
                                        </div>
                                        <span class="text-xs text-neutral-500 dark:text-neutral-400">
                                            {{ $conversation->lastMessage ? $conversation->lastMessage->created_at->diffForHumans() : __('No messages yet') }}
                                        </span>
                                    </div>

                                    @if($conversation->lastMessage)
                                        <p class="mt-1 text-sm text-neutral-600 dark:text-neutral-400 truncate">
                                            <span class="font-medium">
                                                {{ $conversation->lastMessage->sender_id === auth()->id() ? __('You') : $conversation->lastMessage->sender->name }}:
                                            </span>
                                            {{ Str::limit($conversation->lastMessage->content, 100) }}
                                        </p>
                                    @else
                                        <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400 italic">
                                            {{ __('No messages in this conversation yet') }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </a>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="border-t border-neutral-200 px-4 py-3 dark:border-neutral-700">
                        {{ $conversations->links() }}
                    </div>
                @else
                    <div class="p-8 text-center">
                        <div class="mb-4">
                            <svg class="mx-auto h-12 w-12 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-neutral-900 dark:text-neutral-100 mb-2">{{ __('No conversations yet') }}</h3>
                        <p class="text-neutral-500 dark:text-neutral-400 mb-4">{{ __('Start a conversation with your students to communicate important information') }}</p>
                        <flux:button href="{{ route('messages.create') }}" variant="primary">
                            {{ __('Start Your First Conversation') }}
                        </flux:button>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Quick Message Templates -->
    <div class="mt-8">
        <div class="rounded-lg border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-800">
            <h3 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100 mb-4">{{ __('Quick Message Templates') }}</h3>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                <div class="rounded-lg border border-neutral-200 p-4 dark:border-neutral-700">
                    <h4 class="font-medium text-neutral-900 dark:text-neutral-100 mb-2">{{ __('Assignment Reminder') }}</h4>
                    <p class="text-sm text-neutral-600 dark:text-neutral-400 mb-3">{{ __('Remind students about upcoming assignments') }}</p>
                    <button onclick="useTemplate('assignment_reminder')" class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                        {{ __('Use Template') }}
                    </button>
                </div>

                <div class="rounded-lg border border-neutral-200 p-4 dark:border-neutral-700">
                    <h4 class="font-medium text-neutral-900 dark:text-neutral-100 mb-2">{{ __('Grade Notification') }}</h4>
                    <p class="text-sm text-neutral-600 dark:text-neutral-400 mb-3">{{ __('Inform students about grade availability') }}</p>
                    <button onclick="useTemplate('grade_notification')" class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                        {{ __('Use Template') }}
                    </button>
                </div>

                <div class="rounded-lg border border-neutral-200 p-4 dark:border-neutral-700">
                    <h4 class="font-medium text-neutral-900 dark:text-neutral-100 mb-2">{{ __('Class Update') }}</h4>
                    <p class="text-sm text-neutral-600 dark:text-neutral-400 mb-3">{{ __('Share important class information') }}</p>
                    <button onclick="useTemplate('class_update')" class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                        {{ __('Use Template') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function useTemplate(templateType) {
            const templates = {
                'assignment_reminder': '{{ __("Hi! Just a reminder that the assignment is due on [DATE]. Please make sure to submit it before the deadline.") }}',
                'grade_notification': '{{ __("Hello! Your grades for the recent assessment have been posted. Please check the grades section for details.") }}',
                'class_update': '{{ __("Important update: [UPDATE_DETAILS]. Please review and let me know if you have any questions.") }}'
            };

            // Store template in sessionStorage for use in create message form
            sessionStorage.setItem('messageTemplate', templates[templateType]);

            // Redirect to create message page
            window.location.href = '{{ route("messages.create") }}';
        }

        // Load template if returning from create page
        document.addEventListener('DOMContentLoaded', function() {
            const template = sessionStorage.getItem('messageTemplate');
            if (template && window.location.search.includes('template')) {
                // Could populate a form here if needed
                sessionStorage.removeItem('messageTemplate');
            }
        });
    </script>
</x-layouts::app>