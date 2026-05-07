<x-layouts::app :title="__('Reminders')">
    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-neutral-900 dark:text-neutral-100">{{ __('Reminders') }}</h1>
            <p class="mt-1 text-neutral-500 dark:text-neutral-400">{{ __('Manage your reminders') }}</p>
        </div>
        <flux:button variant="primary" onclick="document.getElementById('addReminderModal').showModal()">
            <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            {{ __('Add Reminder') }}
        </flux:button>
    </div>

    <!-- Stats Cards -->
    <div class="mb-8 grid gap-4 sm:grid-cols-3">
        <div class="rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-red-100 text-red-600 dark:bg-red-900 dark:text-red-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Unread') }}</p>
                    <p class="text-2xl font-bold text-neutral-900 dark:text-neutral-100">{{ $reminders->where('is_read', false)->count() }}</p>
                </div>
            </div>
        </div>
        <div class="rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-green-100 text-green-600 dark:bg-green-900 dark:text-green-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Read') }}</p>
                    <p class="text-2xl font-bold text-neutral-900 dark:text-neutral-100">{{ $reminders->where('is_read', true)->count() }}</p>
                </div>
            </div>
        </div>
        <div class="rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-blue-100 text-blue-600 dark:bg-blue-900 dark:text-blue-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Upcoming') }}</p>
                    <p class="text-2xl font-bold text-neutral-900 dark:text-neutral-100">{{ $reminders->where('remind_at', '>', now())->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Reminders List -->
    @if($reminders->isEmpty())
        <div class="rounded-xl border border-neutral-200 bg-white py-16 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="flex flex-col items-center">
                <div class="mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-neutral-100 dark:bg-neutral-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-10 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                </div>
                <h3 class="mb-2 text-xl font-semibold text-neutral-900 dark:text-neutral-100">{{ __('No reminders') }}</h3>
                <p class="mb-6 text-neutral-500 dark:text-neutral-400">{{ __('Create your first reminder to get started') }}</p>
                <flux:button variant="primary" onclick="document.getElementById('addReminderModal').showModal()">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    {{ __('Add Reminder') }}
                </flux:button>
            </div>
        </div>
    @else
        <div class="rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-neutral-50 text-neutral-500 dark:bg-neutral-700 dark:text-neutral-400">
                        <tr>
                            <th class="px-6 py-3">{{ __('Status') }}</th>
                            <th class="px-6 py-3">{{ __('Title') }}</th>
                            <th class="px-6 py-3">{{ __('Type') }}</th>
                            <th class="px-6 py-3">{{ __('Remind At') }}</th>
                            <th class="px-6 py-3 text-right">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                        @foreach($reminders as $reminder)
                            <tr class="{{ $reminder->is_read ? 'bg-neutral-50 dark:bg-neutral-800/50' : '' }}">
                                <td class="px-6 py-4">
                                    @if($reminder->is_read)
                                        <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800 dark:bg-green-900 dark:text-green-200">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 size-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            {{ __('Read') }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800 dark:bg-red-900 dark:text-red-200">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 size-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                            </svg>
                                            {{ __('New') }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <span class="{{ $reminder->is_read ? 'text-neutral-400' : 'font-medium text-neutral-900 dark:text-neutral-100' }}">
                                        {{ $reminder->title }}
                                    </span>
                                    @if($reminder->description)
                                        <p class="mt-1 text-xs text-neutral-500">{{ Str::limit($reminder->description, 50) }}</p>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                        {{ ucfirst($reminder->type) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="{{ $reminder->remind_at->isPast() ? 'text-red-600 dark:text-red-400' : 'text-neutral-500 dark:text-neutral-400' }}">
                                        {{ $reminder->remind_at->format('Y-m-d H:i') }}
                                    </span>
                                    @if($reminder->remind_at->isPast() && !$reminder->is_read)
                                        <span class="ml-2 text-xs text-red-600 dark:text-red-400">{{ __('Overdue') }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        @if(!$reminder->is_read)
                                            <form action="{{ route('reminders.markAsRead', $reminder->id) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <flux:button type="submit" size="sm" variant="subtle">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                </flux:button>
                                            </form>
                                        @endif
                                        <form action="{{ route('reminders.destroy', $reminder->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <flux:button type="submit" size="sm" variant="danger" onclick="return confirm('{{ __('Are you sure?') }}')">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </flux:button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Add Reminder Modal -->
    <dialog id="addReminderModal" class="rounded-xl border border-neutral-200 p-6 shadow-2xl dark:border-neutral-700 dark:bg-neutral-800 w-full max-w-md">
        <form method="POST" action="{{ route('reminders.store') }}">
            @csrf
            <h3 class="mb-4 text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Add Reminder') }}</h3>
            <div class="space-y-4">
                <flux:input name="title" :label="__('Title')" required />
                <flux:textarea name="description" :label="__('Description')" rows="3"></flux:textarea>
                <div class="grid grid-cols-2 gap-4">
                    <flux:input type="datetime-local" name="remind_at" :label="__('Remind At')" required />
                    <div>
                        <flux:label>{{ __('Type') }}</flux:label>
                        <select name="type" class="w-full rounded-lg border-neutral-300 dark:border-neutral-600 dark:bg-neutral-700 px-3 py-2">
                            <option value="general">{{ __('General') }}</option>
                            <option value="assignment">{{ __('Assignment') }}</option>
                            <option value="exam">{{ __('Exam') }}</option>
                            <option value="announcement">{{ __('Announcement') }}</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-3">
                <flux:button type="button" variant="ghost" onclick="document.getElementById('addReminderModal').close()">
                    {{ __('Cancel') }}
                </flux:button>
                <flux:button type="submit" variant="primary">
                    {{ __('Save') }}
                </flux:button>
            </div>
        </form>
    </dialog>
</x-layouts::app>
