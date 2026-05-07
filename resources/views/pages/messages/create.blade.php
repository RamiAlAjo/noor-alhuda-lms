<x-layouts::app :title="__('Compose')">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center gap-4 rounded-xl border border-blue-200 bg-gradient-to-r from-blue-600 to-blue-700 p-6 text-white shadow-lg dark:border-neutral-700">
            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-white/20">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
            </div>
            <div>
                <h1 class="text-2xl font-bold">{{ __('Compose') }}</h1>
                <p class="mt-1 text-blue-100">{{ __('Send a new message') }}</p>
            </div>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-4">
        <!-- Compose Form -->
        <div class="lg:col-span-3">
            <div class="rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                <div class="p-6">
                    <form action="{{ route('messages.store') }}" method="POST">
                        @csrf
                        <div class="mb-6">
                            <flux:label>{{ __('To') }} *</flux:label>
                            <select name="receiver_id" class="w-full rounded-lg border-neutral-300 dark:border-neutral-600 dark:bg-neutral-700" required>
                                <option value="">{{ __('Select recipient') }}</option>
                                @foreach(\App\Models\User::where('id', '!=', auth()->id())->get() as $user)
                                    <option value="{{ $user->id }}">
                                        {{ $user->full_name }} ({{ $user->roles->first()?->name ?? 'N/A' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-6">
                            <flux:input name="subject" :label="__('Subject')" placeholder="{{ __('Enter subject') }}" required />
                        </div>
                        <div class="mb-6">
                            <flux:textarea name="content" :label="__('Message')" rows="10" placeholder="{{ __('Write your message') }}" required></flux:textarea>
                        </div>
                        <div class="flex gap-3">
                            <flux:button type="submit" variant="primary">
                                <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                </svg>
                                {{ __('Send') }}
                            </flux:button>
                            <flux:button variant="ghost" href="{{ route('messages.index') }}">
                                {{ __('Cancel') }}
                            </flux:button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Tips Sidebar -->
        <div class="rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <h3 class="mb-4 font-semibold text-neutral-900 dark:text-neutral-100">
                <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 inline size-5 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                </svg>
                {{ __('Tips') }}
            </h3>
            <ul class="space-y-4">
                <li class="flex gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mt-0.5 size-5 flex-shrink-0 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-sm text-neutral-600 dark:text-neutral-400">{{ __('Select a recipient from the list') }}</span>
                </li>
                <li class="flex gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mt-0.5 size-5 flex-shrink-0 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-sm text-neutral-600 dark:text-neutral-400">{{ __('Use a clear subject line') }}</span>
                </li>
                <li class="flex gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mt-0.5 size-5 flex-shrink-0 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-sm text-neutral-600 dark:text-neutral-400">{{ __('Be clear and concise in your message') }}</span>
                </li>
            </ul>
        </div>
    </div>
</x-layouts::app>
