<x-layouts::app :title="__('Course Materials')">
    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-neutral-900 dark:text-neutral-100">{{ $offering->course?->name ?? __('Unknown Course') }}</h1>
            <p class="mt-1 text-neutral-500 dark:text-neutral-400">{{ __('Section') }} {{ $offering->section_name }} - {{ __('Materials') }}</p>
        </div>
        <flux:button :href="route('student.courses.show', $offering)" variant="ghost">
            <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12" />
            </svg>
            {{ __('Back to Course') }}
        </flux:button>
    </div>

    <div class="rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
        <div class="border-b border-neutral-200 px-6 py-4 dark:border-neutral-700">
            <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Course Materials') }}</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-neutral-50 text-neutral-500 dark:bg-neutral-700 dark:text-neutral-400">
                    <tr>
                        <th class="px-6 py-3">{{ __('Title') }}</th>
                        <th class="px-6 py-3">{{ __('Type') }}</th>
                        <th class="px-6 py-3">{{ __('Description') }}</th>
                        <th class="px-6 py-3">{{ __('Uploaded') }}</th>
                        <th class="px-6 py-3 text-right">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                    @forelse($materials as $material)
                    <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-700/50">
                        <td class="px-6 py-4 font-medium text-neutral-900 dark:text-neutral-100">
                            @if($material->hasYouTubeVideo())
                                <div class="flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5 text-red-600" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                                    </svg>
                                    {{ $material->title }}
                                </div>
                            @else
                                {{ $material->title }}
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                {{ __($material->type) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-neutral-500 dark:text-neutral-400">{{ $material->description ?? '-' }}</td>
                        <td class="px-6 py-4 text-neutral-500 dark:text-neutral-400">{{ $material->created_at->format('Y-m-d') }}</td>
                        <td class="px-6 py-4 text-right">
                            @if($material->hasYouTubeVideo())
                                <flux:button size="sm" variant="subtle" onclick="document.getElementById('video-modal-{{ $material->id }}').showModal()">
                                    {{ __('Watch Video') }}
                                </flux:button>
                                <dialog id="video-modal-{{ $material->id }}" class="modal" onclick="if(event.target === this) { this.close(); document.getElementById('video-iframe-{{ $material->id }}').src = document.getElementById('video-iframe-{{ $material->id }}').src; }">
                                    <div class="modal-box max-w-4xl bg-neutral-900">
                                        <h3 class="mb-4 text-lg font-bold text-white">{{ $material->title }}</h3>
                                        <div class="aspect-video w-full">
                                            <iframe
                                                id="video-iframe-{{ $material->id }}"
                                                class="h-full w-full"
                                                src="{{ $material->getYouTubeEmbedUrl() }}"
                                                title="{{ $material->title }}"
                                                frameborder="0"
                                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                                allowfullscreen>
                                            </iframe>
                                        </div>
                                        <form method="dialog" class="mt-4">
                                            <flux:button type="submit" variant="subtle">{{ __('Close') }}</flux:button>
                                        </form>
                                    </div>
                                </dialog>
                            @endif
                            @if($material->file_path)
                            <a href="{{ asset('storage/' . ltrim($material->file_path, '/')) }}" target="_blank" class="text-blue-600 hover:text-blue-800 dark:text-blue-400">
                                {{ __('Download') }}
                            </a>
                            @elseif(!$material->hasYouTubeVideo())
                            <span class="text-neutral-400">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-neutral-100 dark:bg-neutral-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="size-8 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <h3 class="text-lg font-medium text-neutral-900 dark:text-neutral-100">{{ __('No materials available') }}</h3>
                                <p class="text-neutral-500 dark:text-neutral-400">{{ __('Course materials will appear here') }}</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts::app>
