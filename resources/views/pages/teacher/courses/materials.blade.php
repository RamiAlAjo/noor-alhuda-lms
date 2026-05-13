<x-layouts::app :title="__('Course Materials')">
    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-neutral-900 dark:text-neutral-100">{{ __('Course Materials') }}</h1>
            <p class="mt-1 text-neutral-500 dark:text-neutral-400">{{ $section->course?->name ?? __('Unknown Course') }} - {{ __('Section') }} {{ $section->section_name }}</p>
        </div>
        <flux:button :href="route('teacher.courses.show', $section)" variant="ghost">
            <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12" />
            </svg>
            {{ __('Back to Course') }}
        </flux:button>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <!-- Upload Form -->
        <div class="lg:col-span-1">
            <div class="rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                <div class="border-b border-neutral-200 px-6 py-4 dark:border-neutral-700">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100 text-blue-600 dark:bg-blue-900 dark:text-blue-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Add New Material') }}</h2>
                            <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('File or YouTube video') }}</p>
                        </div>
                    </div>
                </div>
                <form method="POST" action="{{ route('teacher.courses.materials.store', $section) }}" enctype="multipart/form-data" class="p-6">
                    @csrf
                    <div class="space-y-4">
                        <flux:input
                            name="title"
                            :label="__('Title')"
                            placeholder="{{ __('Material title') }}"
                            required
                        />

                        <flux:select name="type" :label="__('Type')" required>
                            <flux:select.option value="lecture">{{ __('Lecture Notes') }}</flux:select.option>
                            <flux:select.option value="slide">{{ __('Slides') }}</flux:select.option>
                            <flux:select.option value="assignment">{{ __('Assignment') }}</flux:select.option>
                            <flux:select.option value="reading">{{ __('Reading Material') }}</flux:select.option>
                            <flux:select.option value="video">{{ __('Video') }}</flux:select.option>
                            <flux:select.option value="other">{{ __('Other') }}</flux:select.option>
                        </flux:select>

                        <flux:input
                            name="week"
                            type="number"
                            :label="__('Week')"
                            placeholder="1"
                            min="1"
                            max="20"
                        />

                        <flux:textarea
                            name="description"
                            :label="__('Description')"
                            placeholder="{{ __('Optional description') }}"
                            rows="2"
                        />

                        <!-- Enhanced File Upload -->
                        <div class="space-y-3">
                            <!-- Drag & Drop Area -->
                            <div id="fileDropZone" class="relative rounded-lg border-2 border-dashed border-neutral-300 p-6 text-center transition-colors hover:border-blue-400 dark:border-neutral-600 dark:hover:border-blue-500">
                                <input
                                    type="file"
                                    name="file"
                                    id="fileInput"
                                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                    accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.txt,.zip,.rar,.jpg,.jpeg,.png,.gif,.mp4,.avi,.mov,.wmv"
                                />
                                <div class="space-y-2">
                                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-blue-100 text-blue-600 dark:bg-blue-900 dark:text-blue-400">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-neutral-900 dark:text-neutral-100">
                                            {{ __('Drop files here or click to browse') }}
                                        </p>
                                        <p class="text-xs text-neutral-500 dark:text-neutral-400">
                                            {{ __('Supported formats: PDF, DOC, PPT, XLS, TXT, ZIP, Images, Videos') }}
                                        </p>
                                    </div>
                                </div>
                                <div id="filePreview" class="mt-4 hidden">
                                    <div class="flex items-center gap-2 p-2 bg-neutral-50 dark:bg-neutral-800 rounded-md">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4 text-neutral-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                        </svg>
                                        <span id="fileName" class="text-sm text-neutral-700 dark:text-neutral-300"></span>
                                        <span id="fileSize" class="text-xs text-neutral-500 dark:text-neutral-400"></span>
                                        <button type="button" id="removeFile" class="text-red-500 hover:text-red-700">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                                <p class="mt-2 text-xs text-neutral-500 dark:text-neutral-400">
                                    {{ __('Maximum file size: 50MB') }}
                                </p>
                            </div>

                            <!-- Upload Progress (Hidden by default) -->
                            <div id="uploadProgress" class="hidden">
                                <div class="space-y-2">
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-neutral-700 dark:text-neutral-300">{{ __('Uploading...') }}</span>
                                        <span id="progressPercent" class="text-neutral-500">0%</span>
                                    </div>
                                    <div class="w-full bg-neutral-200 rounded-full h-2 dark:bg-neutral-700">
                                        <div id="progressBar" class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- YouTube URL -->
                        <div>
                            <flux:input
                                name="video_url"
                                :label="__('YouTube Video URL')"
                                placeholder="https://www.youtube.com/watch?v=..."
                            />
                            <p class="mt-1 text-xs text-neutral-500 dark:text-neutral-400">{{ __('Or paste a YouTube link') }}</p>
                        </div>

                        <flux:button type="submit" variant="primary" class="w-full justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                            </svg>
                            {{ __('Add Material') }}
                        </flux:button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Materials List -->
        <div class="lg:col-span-2">
            <div class="rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                <div class="border-b border-neutral-200 px-6 py-4 dark:border-neutral-700">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Course Materials') }}</h2>
                        <span class="rounded-full bg-indigo-100 px-3 py-1 text-xs font-medium text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200">
                            {{ $section->materials->count() }} {{ __('items') }}
                        </span>
                    </div>
                </div>
                <div class="p-6">
                    @if($section->materials->isEmpty())
                        <div class="flex flex-col items-center justify-center py-12">
                            <div class="mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-neutral-100 dark:bg-neutral-700">
                                <svg xmlns="http://www.w3.org/2000/svg" class="size-10 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-medium text-neutral-900 dark:text-neutral-100">{{ __('No materials yet') }}</h3>
                            <p class="text-neutral-500 dark:text-neutral-400">{{ __('Upload files or add YouTube videos') }}</p>
                        </div>
                    @else
                        <!-- Group by Week -->
                        @php
                            $materialsByWeek = $section->materials->groupBy('week');
                        @endphp

                        @foreach($materialsByWeek as $week => $weekMaterials)
                            <div class="mb-6 last:mb-0">
                                <div class="mb-3 flex items-center gap-2">
                                    <h3 class="text-sm font-semibold text-neutral-500 dark:text-neutral-400">
                                        {{ __('Week') }} {{ $week ?? 'N/A' }}
                                    </h3>
                                    <div class="h-px flex-1 bg-neutral-200 dark:bg-neutral-700"></div>
                                    <span class="text-xs text-neutral-400 dark:text-neutral-500">
                                        {{ $weekMaterials->count() }} {{ __('items') }}
                                    </span>
                                </div>

                                <div class="space-y-3">
                                    @foreach($weekMaterials as $material)
                                        <div class="group relative rounded-lg border border-neutral-200 bg-white p-4 transition-all hover:border-blue-300 hover:shadow-md dark:border-neutral-700 dark:bg-neutral-800 dark:hover:border-blue-600">
                                            <div class="flex items-start gap-4">
                                                <!-- Icon -->
                                                <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-lg
                                                    @if($material->hasYouTubeVideo())
                                                        bg-red-100 text-red-600 dark:bg-red-900 dark:text-red-400
                                                    @else
                                                        bg-indigo-100 text-indigo-600 dark:bg-indigo-900 dark:text-indigo-400
                                                    @endif
                                                ">
                                                    @if($material->hasYouTubeVideo())
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="size-6" viewBox="0 0 24 24" fill="currentColor">
                                                            <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                                                        </svg>
                                                    @else
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                        </svg>
                                                    @endif
                                                </div>

                                                <!-- Content -->
                                                <div class="flex-1 min-w-0">
                                                    <h4 class="font-medium text-neutral-900 dark:text-neutral-100 truncate">{{ $material->title }}</h4>
                                                    <div class="mt-1 flex flex-wrap items-center gap-2 text-sm text-neutral-500 dark:text-neutral-400">
                                                        <span class="inline-flex items-center rounded-full bg-blue-50 px-2 py-0.5 text-xs font-medium text-blue-700 dark:bg-blue-900 dark:text-blue-300">
                                                            {{ __($material->type) }}
                                                        </span>
                                                        @if($material->description)
                                                            <span class="truncate max-w-xs">{{ Str::limit($material->description, 50) }}</span>
                                                        @endif
                                                        <span>•</span>
                                                        <span>{{ $material->created_at->format('M d, Y') }}</span>
                                                    </div>
                                                    @if($material->file_size)
                                                        <p class="mt-1 text-xs text-neutral-400 dark:text-neutral-500">
                                                            {{ number_format($material->file_size / 1024, 1) }} KB
                                                        </p>
                                                    @endif
                                                </div>

                                                <!-- Actions -->
                                                <div class="flex items-center gap-2 opacity-0 transition-opacity group-hover:opacity-100">
                                                    @if($material->hasYouTubeVideo())
                                                        <flux:button size="sm" variant="primary" onclick="document.getElementById('video-modal-{{ $material->id }}').showModal()">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 size-4" viewBox="0 0 24 24" fill="currentColor">
                                                                <path d="M8 5v14l11-7z"/>
                                                            </svg>
                                                            {{ __('Play') }}
                                                        </flux:button>
                                                        <dialog id="video-modal-{{ $material->id }}" class="modal" onclick="if(event.target === this) { this.close(); document.getElementById('video-iframe-{{ $material->id }}').src = document.getElementById('video-iframe-{{ $material->id }}').src; }">
                                                            <div class="modal-box max-w-4xl bg-neutral-900 p-0">
                                                                <div class="aspect-video w-full">
                                                                    <iframe
                                                                        id="video-iframe-{{ $material->id }}"
                                                                        class="h-full w-full"
                                                                        src="{{ $material->getYouTubeEmbedUrl() }}?autoplay=1"
                                                                        title="{{ $material->title }}"
                                                                        frameborder="0"
                                                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                                                        allowfullscreen>
                                                                    </iframe>
                                                                </div>
                                                                <form method="dialog" class="p-4">
                                                                    <flux:button type="submit" variant="subtle">{{ __('Close') }}</flux:button>
                                                                </form>
                                                            </div>
                                                        </dialog>
                                                    @endif

                                                    @if($material->file_path)
                                                        <flux:button size="sm" variant="subtle" onclick="window.open('{{ asset('storage/' . ltrim($material->file_path, '/')) }}', '_blank')">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                            </svg>
                                                            {{ __('View') }}
                                                        </flux:button>
                                                        <flux:button size="sm" variant="subtle" href="{{ asset('storage/' . ltrim($material->file_path, '/')) }}" download>
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                            </svg>
                                                            {{ __('Download') }}
                                                        </flux:button>
                                                    @endif

                                                    <form method="POST" action="{{ route('teacher.materials.destroy', $material) }}" class="inline" onsubmit="return confirm('{{ __('Are you sure you want to delete this material?') }}')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <flux:button size="sm" variant="danger" type="submit">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                            </svg>
                                                        </flux:button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        // Drag and drop functionality for file upload
        const fileDropZone = document.getElementById('fileDropZone');
        const fileInput = document.getElementById('fileInput');

        // Prevent default drag behaviors
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            fileDropZone.addEventListener(eventName, preventDefaults, false);
            document.body.addEventListener(eventName, preventDefaults, false);
        });

        // Highlight drop zone when item is dragged over it
        ['dragenter', 'dragover'].forEach(eventName => {
            fileDropZone.addEventListener(eventName, highlight, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            fileDropZone.addEventListener(eventName, unhighlight, false);
        });

        // Handle dropped files
        fileDropZone.addEventListener('drop', handleDrop, false);

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        function highlight(e) {
            fileDropZone.classList.add('border-blue-500', 'bg-blue-50', 'dark:bg-blue-900/20');
        }

        function unhighlight(e) {
            fileDropZone.classList.remove('border-blue-500', 'bg-blue-50', 'dark:bg-blue-900/20');
        }

        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;

            if (files.length > 0) {
                fileInput.files = files;
                // Update the visual feedback
                const fileName = files[0].name;
                const uploadText = fileDropZone.querySelector('.upload-text');
                if (uploadText) {
                    uploadText.textContent = `Selected: ${fileName}`;
                }
            }
        }

        // File input change handler
        fileInput.addEventListener('change', function(e) {
            const fileName = e.target.files[0]?.name;
            if (fileName) {
                const uploadText = fileDropZone.querySelector('.upload-text');
                if (uploadText) {
                    uploadText.textContent = `Selected: ${fileName}`;
                }
            }
        });
    </script>
</x-layouts::app>
