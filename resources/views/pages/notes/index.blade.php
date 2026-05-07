<x-layouts::app :title="__('Notes')">
    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-neutral-900 dark:text-neutral-100">{{ __('Notes') }}</h1>
            <p class="mt-1 text-neutral-500 dark:text-neutral-400">{{ __('Create and manage your personal notes') }}</p>
        </div>
        <flux:button variant="primary" onclick="document.getElementById('addNoteModal').showModal()">
            <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            {{ __('Add Note') }}
        </flux:button>
    </div>

    <!-- Stats Cards -->
    @if(!$notes->isEmpty())
    <div class="mb-8 grid gap-4 md:grid-cols-3">
        <div class="rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="flex items-center gap-4">
                <div class="rounded-full bg-indigo-100 p-3 dark:bg-indigo-900/30">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-6 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Total Notes') }}</p>
                    <p class="text-2xl font-bold text-neutral-900 dark:text-neutral-100">{{ $notes->count() }}</p>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="flex items-center gap-4">
                <div class="rounded-full bg-yellow-100 p-3 dark:bg-yellow-900/30">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-6 text-yellow-600 dark:text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Pinned') }}</p>
                    <p class="text-2xl font-bold text-neutral-900 dark:text-neutral-100">{{ $notes->where('is_pinned', true)->count() }}</p>
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
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Recent (7 days)') }}</p>
                    <p class="text-2xl font-bold text-neutral-900 dark:text-neutral-100">{{ $notes->where('updated_at', '>=', now()->subDays(7))->count() }}</p>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if($notes->isEmpty())
        <div class="rounded-xl border border-neutral-200 bg-white py-16 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="flex flex-col items-center">
                <div class="mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-neutral-100 dark:bg-neutral-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-10 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <h3 class="mb-2 text-xl font-semibold text-neutral-900 dark:text-neutral-100">{{ __('No notes yet') }}</h3>
                <p class="mb-6 text-neutral-500 dark:text-neutral-400">{{ __('Create your first note to get started') }}</p>
                <flux:button variant="primary" onclick="document.getElementById('addNoteModal').showModal()">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    {{ __('Create First Note') }}
                </flux:button>
            </div>
        </div>
    @else
        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @foreach($notes as $note)
                @php
                    $colorClasses = match($note->color) {
                        'yellow' => 'bg-yellow-50 border-yellow-200 dark:bg-yellow-900/30 dark:border-yellow-800',
                        'green' => 'bg-green-50 border-green-200 dark:bg-green-900/30 dark:border-green-800',
                        'blue' => 'bg-blue-50 border-blue-200 dark:bg-blue-900/30 dark:border-blue-800',
                        'pink' => 'bg-pink-50 border-pink-200 dark:bg-pink-900/30 dark:border-pink-800',
                        'purple' => 'bg-purple-50 border-purple-200 dark:bg-purple-900/30 dark:border-purple-800',
                        'orange' => 'bg-orange-50 border-orange-200 dark:bg-orange-900/30 dark:border-orange-800',
                        'red' => 'bg-red-50 border-red-200 dark:bg-red-900/30 dark:border-red-800',
                        default => 'bg-white border-neutral-200 dark:bg-neutral-800 dark:border-neutral-700'
                    };
                    $titleColor = match($note->color) {
                        'yellow' => 'text-yellow-800 dark:text-yellow-200',
                        'green' => 'text-green-800 dark:text-green-200',
                        'blue' => 'text-blue-800 dark:text-blue-200',
                        'pink' => 'text-pink-800 dark:text-pink-200',
                        'purple' => 'text-purple-800 dark:text-purple-200',
                        'orange' => 'text-orange-800 dark:text-orange-200',
                        'red' => 'text-red-800 dark:text-red-200',
                        default => 'text-neutral-800 dark:text-neutral-200'
                    };
                    $contentColor = match($note->color) {
                        'yellow' => 'text-yellow-700 dark:text-yellow-300',
                        'green' => 'text-green-700 dark:text-green-300',
                        'blue' => 'text-blue-700 dark:text-blue-300',
                        'pink' => 'text-pink-700 dark:text-pink-300',
                        'purple' => 'text-purple-700 dark:text-purple-300',
                        'orange' => 'text-orange-700 dark:text-orange-300',
                        'red' => 'text-red-700 dark:text-red-300',
                        default => 'text-neutral-700 dark:text-neutral-300'
                    };
                    $mutedColor = match($note->color) {
                        'yellow' => 'text-yellow-600 dark:text-yellow-400',
                        'green' => 'text-green-600 dark:text-green-400',
                        'blue' => 'text-blue-600 dark:text-blue-400',
                        'pink' => 'text-pink-600 dark:text-pink-400',
                        'purple' => 'text-purple-600 dark:text-purple-400',
                        'orange' => 'text-orange-600 dark:text-orange-400',
                        'red' => 'text-red-600 dark:text-red-400',
                        default => 'text-neutral-500 dark:text-neutral-400'
                    };
                @endphp
                <div class="group relative rounded-xl border p-6 shadow-sm transition-all hover:shadow-md {{ $colorClasses }}">
                    <div class="mb-3 flex items-start justify-between">
                        <h3 class="text-lg font-semibold {{ $titleColor }} {{ $note->is_pinned ? 'text-yellow-600 dark:text-yellow-400' : '' }}">
                            @if($note->is_pinned)
                                <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 inline size-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M16 12V4h1V2H7v2h1v8l-2 2v2h5.2v6h1.6v-6H18v-2l-2-2z"/>
                                </svg>
                            @endif
                            {{ $note->title }}
                        </h3>
                        <flux:dropdown>
                                <flux:button variant="ghost" size="sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                    </svg>
                                </flux:button>
                            <flux:menu>
                                <form action="{{ route('notes.togglePin', $note->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <flux:menu.item type="submit">
                                        {{ $note->is_pinned ? __('Unpin') : __('Pin') }}
                                    </flux:menu.item>
                                </form>
                                <flux:menu.item as="button" onclick="document.getElementById('editNoteModal{{ $note->id }}').showModal()">
                                    {{ __('Edit') }}
                                </flux:menu.item>
                                <flux:menu.separator />
                                <form action="{{ route('notes.destroy', $note->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <flux:menu.item type="submit" class="text-red-600 w-full text-left" onclick="return confirm('{{ __('Are you sure?') }}')">
                                        {{ __('Delete') }}
                                    </flux:menu.item>
                                </form>
                            </flux:menu>
                        </flux:dropdown>
                    </div>
                    <p class="mb-4 {{ $contentColor }}">{{ $note->content }}</p>
                    <p class="text-sm {{ $mutedColor }}">{{ $note->updated_at->format('Y-m-d H:i') }}</p>
                </div>

                <!-- Edit Note Modal -->
                <dialog id="editNoteModal{{ $note->id }}" class="rounded-xl border border-neutral-200 p-6 shadow-2xl dark:border-neutral-700 dark:bg-neutral-800">
                    <form action="{{ route('notes.update', $note->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <h3 class="mb-4 text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Edit Note') }}</h3>
                        <div class="space-y-4">
                            <flux:input name="title" :label="__('Title')" :value="$note->title" required />
                            <flux:textarea name="content" :label="__('Content')" rows="5">{{ $note->content }}</flux:textarea>
                            <div>
                                <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">{{ __('Color') }}</label>
                                <select name="color" class="w-full rounded-lg border-neutral-300 dark:border-neutral-600 dark:bg-neutral-700">
                                    <option value="white" {{ $note->color == 'white' ? 'selected' : '' }}>{{ __('White') }}</option>
                                    <option value="yellow" {{ $note->color == 'yellow' ? 'selected' : '' }}>{{ __('Yellow') }}</option>
                                    <option value="green" {{ $note->color == 'green' ? 'selected' : '' }}>{{ __('Green') }}</option>
                                    <option value="blue" {{ $note->color == 'blue' ? 'selected' : '' }}>{{ __('Blue') }}</option>
                                    <option value="pink" {{ $note->color == 'pink' ? 'selected' : '' }}>{{ __('Pink') }}</option>
                                    <option value="purple" {{ $note->color == 'purple' ? 'selected' : '' }}>{{ __('Purple') }}</option>
                                    <option value="orange" {{ $note->color == 'orange' ? 'selected' : '' }}>{{ __('Orange') }}</option>
                                    <option value="red" {{ $note->color == 'red' ? 'selected' : '' }}>{{ __('Red') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="mt-6 flex justify-end gap-3">
                            <flux:button type="button" variant="ghost" onclick="document.getElementById('editNoteModal{{ $note->id }}').close()">
                                {{ __('Cancel') }}
                            </flux:button>
                            <flux:button type="submit" variant="primary">
                                {{ __('Update Note') }}
                            </flux:button>
                        </div>
                    </form>
                </dialog>
            @endforeach
        </div>
    @endif

    <!-- Add Note Modal -->
    <dialog id="addNoteModal" class="rounded-xl border border-neutral-200 p-6 shadow-2xl dark:border-neutral-700 dark:bg-neutral-800">
        <form method="POST" action="{{ route('notes.store') }}">
            @csrf
            <h3 class="mb-4 text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Add New Note') }}</h3>
            <div class="space-y-4">
                <flux:input name="title" :label="__('Title')" required />
                <flux:textarea name="content" :label="__('Content')" rows="5"></flux:textarea>
                <flux:select name="color" :label="__('Color')">
                    <flux:select.option value="white">{{ __('White') }}</flux:select.option>
                    <flux:select.option value="yellow">{{ __('Yellow') }}</flux:select.option>
                    <flux:select.option value="green">{{ __('Green') }}</flux:select.option>
                    <flux:select.option value="blue">{{ __('Blue') }}</flux:select.option>
                    <flux:select.option value="pink">{{ __('Pink') }}</flux:select.option>
                    <flux:select.option value="purple">{{ __('Purple') }}</flux:select.option>
                    <flux:select.option value="orange">{{ __('Orange') }}</flux:select.option>
                    <flux:select.option value="red">{{ __('Red') }}</flux:select.option>
                </flux:select>
            </div>
            <div class="mt-6 flex justify-end gap-3">
                <flux:button type="button" variant="ghost" onclick="document.getElementById('addNoteModal').close()">
                    {{ __('Cancel') }}
                </flux:button>
                <flux:button type="submit" variant="primary">
                    {{ __('Add Note') }}
                </flux:button>
            </div>
        </form>
    </dialog>
</x-layouts::app>
