<x-layouts::app :title="__('Tasks')">
    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-neutral-900 dark:text-neutral-100">{{ __('Tasks') }}</h1>
            <p class="mt-1 text-neutral-500 dark:text-neutral-400">{{ __('Manage your to-do list') }}</p>
        </div>
        <flux:button variant="primary" onclick="document.getElementById('addTaskModal').showModal()">
            <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            {{ __('Add Task') }}
        </flux:button>
    </div>

    <!-- Stats -->
    <div class="mb-8 grid gap-4 md:grid-cols-3">
        <div class="rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-yellow-100 text-yellow-600 dark:bg-yellow-900 dark:text-yellow-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Pending') }}</p>
                    <p class="text-2xl font-bold text-neutral-900 dark:text-neutral-100">{{ $tasks->where('is_completed', false)->count() }}</p>
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
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Completed') }}</p>
                    <p class="text-2xl font-bold text-neutral-900 dark:text-neutral-100">{{ $tasks->where('is_completed', true)->count() }}</p>
                </div>
            </div>
        </div>
        <div class="rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-red-100 text-red-600 dark:bg-red-900 dark:text-red-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('High Priority') }}</p>
                    <p class="text-2xl font-bold text-neutral-900 dark:text-neutral-100">{{ $tasks->where('priority', 3)->where('is_completed', false)->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tasks List -->
    @if($tasks->isEmpty())
        <div class="rounded-xl border border-neutral-200 bg-white py-16 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="flex flex-col items-center">
                <div class="mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-neutral-100 dark:bg-neutral-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-10 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                </div>
                <h3 class="mb-2 text-xl font-semibold text-neutral-900 dark:text-neutral-100">{{ __('No tasks yet') }}</h3>
                <p class="mb-6 text-neutral-500 dark:text-neutral-400">{{ __('Create your first task to get started') }}</p>
                <flux:button variant="primary" onclick="document.getElementById('addTaskModal').showModal()">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    {{ __('Create First Task') }}
                </flux:button>
            </div>
        </div>
    @else
        <div class="rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-neutral-50 text-neutral-500 dark:bg-neutral-700 dark:text-neutral-400">
                        <tr>
                            <th class="px-6 py-3">{{ __('Status') }}</th>
                            <th class="px-6 py-3">{{ __('Title') }}</th>
                            <th class="px-6 py-3">{{ __('Priority') }}</th>
                            <th class="px-6 py-3">{{ __('Due Date') }}</th>
                            <th class="px-6 py-3 text-right">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                        @foreach($tasks as $task)
                            <tr class="{{ $task->is_completed ? 'bg-neutral-50 dark:bg-neutral-800/50' : '' }}">
                                <td class="px-6 py-4">
                                    <form action="{{ route('tasks.toggle', $task->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="flex items-center justify-center rounded-full p-1 hover:bg-neutral-100 dark:hover:bg-neutral-700">
                                            @if($task->is_completed)
                                                <svg xmlns="http://www.w3.org/2000/svg" class="size-6 text-green-500" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                                </svg>
                                            @else
                                                <svg xmlns="http://www.w3.org/2000/svg" class="size-6 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12V4m0 0l-4 4m4-4l4 4" />
                                                </svg>
                                            @endif
                                        </button>
                                    </form>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="{{ $task->is_completed ? 'text-neutral-400 line-through' : 'font-medium text-neutral-900 dark:text-neutral-100' }}">
                                        {{ $task->title }}
                                    </span>
                                    @if($task->description)
                                        <p class="mt-1 text-xs text-neutral-500">{{ Str::limit($task->description, 50) }}</p>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @switch($task->priority)
                                        @case(3)
                                            <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800 dark:bg-red-900 dark:text-red-200">
                                                {{ __('High') }}
                                            </span>
                                            @break
                                        @case(2)
                                            <span class="inline-flex items-center rounded-full bg-yellow-100 px-2.5 py-0.5 text-xs font-medium text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                                {{ __('Medium') }}
                                            </span>
                                            @break
                                        @case(1)
                                            <span class="inline-flex items-center rounded-full bg-neutral-100 px-2.5 py-0.5 text-xs font-medium text-neutral-800 dark:bg-neutral-700 dark:text-neutral-300">
                                                {{ __('Low') }}
                                            </span>
                                            @break
                                    @endswitch
                                </td>
                                <td class="px-6 py-4 text-neutral-500 dark:text-neutral-400">
                                    {{ $task->due_date ? $task->due_date->format('Y-m-d') : '-' }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <flux:button size="sm" variant="subtle" onclick="document.getElementById('editTaskModal-{{ $task->id }}').showModal()">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </flux:button>
                                        <form action="{{ route('tasks.destroy', $task->id) }}" method="POST">
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

    <!-- Add Task Modal -->
    <dialog id="addTaskModal" class="rounded-xl border border-neutral-200 p-6 shadow-2xl dark:border-neutral-700 dark:bg-neutral-800">
        <form method="POST" action="{{ route('tasks.store') }}">
            @csrf
            <h3 class="mb-4 text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Add New Task') }}</h3>
            <div class="space-y-4">
                <flux:input name="title" :label="__('Title')" required />
                <flux:textarea name="description" :label="__('Description')" rows="3"></flux:textarea>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">{{ __('Priority') }}</label>
                        <select name="priority" class="w-full rounded-lg border-neutral-300 dark:border-neutral-600 dark:bg-neutral-700">
                            <option value="low">{{ __('Low') }}</option>
                            <option value="medium">{{ __('Medium') }}</option>
                            <option value="high">{{ __('High') }}</option>
                        </select>
                    </div>
                    <flux:input type="date" name="due_date" :label="__('Due Date')" />
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-3">
                <flux:button type="button" variant="ghost" onclick="document.getElementById('addTaskModal').close()">
                    {{ __('Cancel') }}
                </flux:button>
                <flux:button type="submit" variant="primary">
                    {{ __('Add Task') }}
                </flux:button>
            </div>
        </form>
    </dialog>

    <!-- Edit Task Modals -->
    @foreach($tasks as $task)
    <dialog id="editTaskModal-{{ $task->id }}" class="rounded-xl border border-neutral-200 p-6 shadow-2xl dark:border-neutral-700 dark:bg-neutral-800">
        <form method="POST" action="{{ route('tasks.update', $task->id) }}">
            @csrf
            @method('PUT')
            <h3 class="mb-4 text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Edit Task') }}</h3>
            <div class="space-y-4">
                <flux:input name="title" :label="__('Title')" value="{{ $task->title }}" required />
                <flux:textarea name="description" :label="__('Description')" rows="3">{{ $task->description }}</flux:textarea>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">{{ __('Priority') }}</label>
                        <select name="priority" class="w-full rounded-lg border-neutral-300 dark:border-neutral-600 dark:bg-neutral-700">
                            <option value="low" {{ $task->priority == 1 ? 'selected' : '' }}>{{ __('Low') }}</option>
                            <option value="medium" {{ $task->priority == 2 ? 'selected' : '' }}>{{ __('Medium') }}</option>
                            <option value="high" {{ $task->priority == 3 ? 'selected' : '' }}>{{ __('High') }}</option>
                        </select>
                    </div>
                    <flux:input type="date" name="due_date" :label="__('Due Date')" value="{{ $task->due_date ? $task->due_date->format('Y-m-d') : '' }}" />
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_completed" id="is_completed-{{ $task->id }}" value="1" {{ $task->is_completed ? 'checked' : '' }} class="rounded border-neutral-300">
                    <label for="is_completed-{{ $task->id }}" class="text-sm text-neutral-700 dark:text-neutral-300">{{ __('Mark as completed') }}</label>
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-3">
                <flux:button type="button" variant="ghost" onclick="document.getElementById('editTaskModal-{{ $task->id }}').close()">
                    {{ __('Cancel') }}
                </flux:button>
                <flux:button type="submit" variant="primary">
                    {{ __('Save Changes') }}
                </flux:button>
            </div>
        </form>
    </dialog>
    @endforeach
</x-layouts::app>
