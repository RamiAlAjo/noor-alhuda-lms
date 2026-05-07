<x-layouts::app :title="__('Announcements')">
    <div class="space-y-6">
        <!-- Header -->
        <div class="rounded-2xl bg-gradient-to-r from-indigo-600 to-purple-600 p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="rounded-xl bg-white/20 p-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                        </svg>
                    </div>
                    <div>
                        <nav class="flex text-sm text-gray-300">
                            <a href="{{ route('teacher.courses.index') }}" class="hover:text-white">{{ __('teacher.courses.index') }}</a>
                            <span class="mx-2">/</span>
                            <a href="{{ route('teacher.courses.show', $section) }}" class="hover:text-white">{{ $section->course?->name ?? __('Unknown Course') . ' - ' . $section->name }}</a>
                            <span class="mx-2">/</span>
                            <span class="text-white">{{ __('Announcements') }}</span>
                        </nav>
                        <h1 class="mt-1 text-2xl font-bold text-white">
                            {{ __('Course Announcements') }}
                        </h1>
                        <p class="mt-1 text-indigo-100">{{ $section->course?->name ?? __('Unknown Course') }} - {{ __('Section') }} {{ $section->section_name }}</p>
                    </div>
                </div>
                <flux:button :href="route('teacher.courses.show', $section)" variant="ghost" class="!text-white hover:!bg-white/20">
                    {{ __('Back to Course') }}
                    <svg xmlns="http://www.w3.org/2000/svg" class="ml-2 size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </flux:button>
            </div>
        </div>

        <!-- Create Announcement -->
        <div class="rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="border-b border-neutral-200 px-6 py-4 dark:border-neutral-700">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-100 text-indigo-600 dark:bg-indigo-900 dark:text-indigo-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Post New Announcement') }}</h2>
                        <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Communicate with your students') }}</p>
                    </div>
                </div>
            </div>
            <form method="POST" action="#" class="p-6 space-y-4">
                @csrf
                <flux:input
                    name="title"
                    :label="__('Title')"
                    placeholder="{{ __('Announcement title') }}"
                    required
                />
                <flux:textarea
                    name="content"
                    :label="__('Content')"
                    placeholder="{{ __('Write your announcement here') }}"
                    rows="4"
                    required
                />
                <div class="flex items-center gap-4">
                    <flux:checkbox name="is_pinned" :label="__('Pin this announcement')" />
                    <flux:checkbox name="send_notification" :label="__('Send notification to students')" checked />
                </div>
                <flux:button type="submit" variant="primary">
                    {{ __('Post Announcement') }}
                </flux:button>
            </form>
        </div>

        <!-- Announcements List -->
        <div class="rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="border-b border-neutral-200 px-6 py-4 dark:border-neutral-700">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Previous Announcements') }}</h2>
                    <span class="rounded-full bg-indigo-100 px-3 py-1 text-xs font-medium text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200">
                        {{ $section->announcements->count() }} {{ __('items') }}
                    </span>
                </div>
            </div>
            @if($section->announcements->isEmpty())
                <div class="p-12 text-center">
                    <div class="mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-neutral-100 mx-auto dark:bg-neutral-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-10 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-neutral-900 dark:text-neutral-100">{{ __('No announcements yet') }}</h3>
                    <p class="mt-2 text-neutral-600 dark:text-neutral-400">{{ __('Post your first announcement to communicate with students') }}</p>
                </div>
            @else
                <ul class="divide-y divide-neutral-200 dark:divide-neutral-700">
                    @foreach($section->announcements as $announcement)
                        <li class="p-6">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2">
                                        @if($announcement->is_pinned)
                                            <span class="inline-flex items-center rounded-full bg-yellow-100 px-2 py-0.5 text-xs font-medium text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                                📌 {{ __('Pinned') }}
                                            </span>
                                        @endif
                                        <span class="text-sm text-neutral-500 dark:text-neutral-400">
                                            {{ $announcement->created_at->format('M d, Y H:i') }}
                                        </span>
                                    </div>
                                    <h3 class="mt-2 text-lg font-medium text-neutral-900 dark:text-neutral-100">
                                        {{ $announcement->title }}
                                    </h3>
                                    <p class="mt-2 text-neutral-600 dark:text-neutral-400">
                                        {{ $announcement->content }}
                                    </p>
                                </div>
                                <div class="flex gap-2">
                                    <form method="POST" action="#">
                                        @csrf
                                        <flux:button size="sm" variant="subtle">
                                            {{ $announcement->is_pinned ? __('Unpin') : __('Pin') }}
                                        </flux:button>
                                    </form>
                                    <form method="POST" action="#" onsubmit="return confirm('{{ __('Delete this announcement?') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <flux:button size="sm" variant="danger">
                                            {{ __('Delete') }}
                                        </flux:button>
                                    </form>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</x-layouts.app>
