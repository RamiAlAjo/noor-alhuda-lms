{{--
    =============================================================================
    ADMIN ANNOUNCEMENTS INDEX VIEW
    =============================================================================

    Purpose: List and manage system-wide announcements.

    Route: admin.announcements.index
    Controller: Admin\AnnouncementController@index

    Components:
    - Header with "Create Announcement" button
    - Stats cards: Total Announcements, Pinned, Active
    - Announcements list with:
      * Pinned/Inactive/Type badges
      * Title and content preview
      * Author and date info
      * Action buttons (Toggle Status, Pin, Edit, Delete)
    - Empty state with create button
    - Pagination

    Required Data:
    - $announcements: Paginated collection of Announcement models

    Dependencies:
    - route('admin.announcements.create') - Create announcement
    - route('admin.announcements.toggle', $announcement) - Toggle active status
    - route('admin.announcements.pin', $announcement) - Toggle pin status
    - route('admin.announcements.edit', $announcement) - Edit announcement
    - route('admin.announcements.destroy', $announcement) - Delete announcement
    - $announcement->author->name - Author name
    - $announcement->is_pinned - Pin status
    - $announcement->is_active - Active status

    =============================================================================
--}}
<x-layouts::app :title="__('Announcements')">
    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-neutral-900 dark:text-neutral-100">{{ __('Announcements') }}</h1>
            <p class="mt-1 text-neutral-500 dark:text-neutral-400">{{ __('Manage system-wide and targeted announcements') }}</p>
        </div>
        <flux:button :href="route('admin.announcements.create')" variant="primary">
            <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
            </svg>
            {{ __('Create Announcement') }}
        </flux:button>
    </div>

    <!-- Stats Cards -->
    <div class="mb-6 grid gap-4 md:grid-cols-3">
        <div class="rounded-xl border border-neutral-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100 text-blue-600 dark:bg-blue-900 dark:text-blue-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Total Announcements') }}</p>
                    <p class="text-xl font-bold text-neutral-900 dark:text-neutral-100">{{ $announcements->total() }}</p>
                </div>
            </div>
        </div>
        <div class="rounded-xl border border-neutral-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-yellow-100 text-yellow-600 dark:bg-yellow-900 dark:text-yellow-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Pinned') }}</p>
                    <p class="text-xl font-bold text-neutral-900 dark:text-neutral-100">{{ $announcements->where('is_pinned', true)->count() }}</p>
                </div>
            </div>
        </div>
        <div class="rounded-xl border border-neutral-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-green-100 text-green-600 dark:bg-green-900 dark:text-green-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Active') }}</p>
                    <p class="text-xl font-bold text-neutral-900 dark:text-neutral-100">{{ $announcements->where('is_active', true)->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Announcements List -->
    <div class="rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
        @if($announcements->isEmpty())
            <div class="p-12 text-center">
                <div class="mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-neutral-100 mx-auto dark:bg-neutral-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-10 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">
                    {{ __('No announcements yet') }}
                </h3>
                <p class="mt-2 text-neutral-600 dark:text-neutral-400">
                    {{ __('Create your first announcement to communicate with users') }}
                </p>
                <flux:button :href="route('admin.announcements.create')" variant="primary" class="mt-4">
                    {{ __('Create Announcement') }}
                </flux:button>
            </div>
        @else
            <div class="divide-y divide-neutral-200 dark:divide-neutral-700">
                @foreach($announcements as $announcement)
                    <div class="p-6 hover:bg-neutral-50 dark:hover:bg-neutral-700/50 transition-colors">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex-1">
                                <div class="flex items-center gap-2">
                                    @if($announcement->is_pinned)
                                        <span class="inline-flex items-center rounded-full bg-yellow-100 px-2.5 py-0.5 text-xs font-medium text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                            📌 {{ __('Pinned') }}
                                        </span>
                                    @endif
                                    @if(!$announcement->is_active)
                                        <span class="inline-flex items-center rounded-full bg-neutral-100 px-2.5 py-0.5 text-xs font-medium text-neutral-600 dark:bg-neutral-700 dark:text-neutral-300">
                                            {{ __('Inactive') }}
                                        </span>
                                    @endif
                                    <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800 dark:bg-blue-900 dark:text-blue-200 capitalize">
                                        {{ __($announcement->type) }}
                                    </span>
                                </div>
                                <h3 class="mt-2 text-lg font-semibold text-neutral-900 dark:text-neutral-100">
                                    {{ $announcement->title }}
                                </h3>
                                <p class="mt-2 text-neutral-600 dark:text-neutral-400 line-clamp-2">
                                    {{ $announcement->content }}
                                </p>
                                <div class="mt-3 flex items-center gap-4 text-sm text-neutral-500 dark:text-neutral-400">
                                    <span>{{ __('By') }}: {{ $announcement->author->name ?? __('Unknown') }}</span>
                                    <span>{{ $announcement->created_at->format('M d, Y H:i') }}</span>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <form method="POST" action="{{ route('admin.announcements.toggle', $announcement) }}">
                                    @csrf
                                    @method('POST')
                                    <flux:button size="sm" variant="subtle" :title="__('Toggle Status')">
                                        {{ $announcement->is_active ? __('Disable') : __('Enable') }}
                                    </flux:button>
                                </form>
                                <form method="POST" action="{{ route('admin.announcements.pin', $announcement) }}">
                                    @csrf
                                    @method('POST')
                                    <flux:button size="sm" variant="subtle" :title="__('Pin')">
                                        {{ $announcement->is_pinned ? __('Unpin') : __('Pin') }}
                                    </flux:button>
                                </form>
                                <flux:button size="sm" variant="ghost" :href="route('admin.announcements.edit', $announcement)">
                                    {{ __('Edit') }}
                                </flux:button>
                                <form method="POST" action="{{ route('admin.announcements.destroy', $announcement) }}">
                                    @csrf
                                    @method('DELETE')
                                     <x-button.submit size="sm" variant="danger" loading-text="{{ __('Deleting...') }}">
                                         {{ __('Delete') }}
                                     </x-button.submit>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
        @if($announcements->hasPages())
        <div class="border-t border-neutral-200 px-6 py-4 dark:border-neutral-700">
            {{ $announcements->links() }}
        </div>
        @endif
    </div>
</x-layouts::app>

