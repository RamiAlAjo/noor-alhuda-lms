{{--
    Bulk Notification Management Page
--}}
<x-layouts::app :title="__('Bulk Notifications')">
    <div class="mb-8">
        <nav class="flex text-sm text-gray-400">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-white">{{ __('Dashboard') }}</a>
            <span class="mx-2">/</span>
            <span class="text-white">{{ __('Bulk Notifications') }}</span>
        </nav>

        <h1 class="mt-4 text-3xl font-bold text-stone-900 dark:text-stone-100">{{ __('Bulk Notifications') }}</h1>
        <p class="mt-1 text-stone-500 dark:text-stone-400">{{ __('Send notifications to multiple users at once') }}</p>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <!-- Notification Form -->
        <div class="lg:col-span-2">
            <div class="rounded-xl border border-stone-200 bg-white p-6 dark:border-stone-700 dark:bg-stone-800">
                <form id="bulkNotificationForm" class="space-y-6">
                    @csrf

                    <!-- Recipient Selection -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-stone-900 dark:text-stone-100">{{ __('Recipients') }}</h3>

                        <div class="space-y-3">
                            <flux:label for="recipient_type">{{ __('Recipient Type') }}</flux:label>
                            <flux:select name="recipient_type" id="recipient_type" required>
                                <option value="">{{ __('Select recipient type') }}</option>
                                <option value="all">{{ __('All Users') }}</option>
                                <option value="students">{{ __('All Students') }}</option>
                                <option value="teachers">{{ __('All Teachers') }}</option>
                                <option value="admins">{{ __('All Administrators') }}</option>
                                <option value="course">{{ __('Students in Specific Course') }}</option>
                                <option value="semester">{{ __('Students in Specific Semester') }}</option>
                            </flux:select>
                        </div>

                        <!-- Course Selection -->
                        <div id="courseSelection" class="hidden space-y-3">
                            <flux:label for="course_id">{{ __('Select Course') }}</flux:label>
                            <flux:select name="course_id" id="course_id">
                                <option value="">{{ __('Choose a course') }}</option>
                                @foreach($courses ?? [] as $course)
                                    <option value="{{ $course->id }}">{{ $course->code }} - {{ $course->name }}</option>
                                @endforeach
                            </flux:select>
                        </div>

                        <!-- Semester Selection -->
                        <div id="semesterSelection" class="hidden space-y-3">
                            <flux:label for="semester_id">{{ __('Select Semester') }}</flux:label>
                            <flux:select name="semester_id" id="semester_id">
                                <option value="">{{ __('Choose a semester') }}</option>
                                @foreach($semesters ?? [] as $semester)
                                    <option value="{{ $semester->id }}">{{ $semester->name }}</option>
                                @endforeach
                            </flux:select>
                        </div>

                        <!-- Recipient Preview -->
                        <div id="recipientPreview" class="hidden">
                            <button type="button" id="previewBtn" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-sm">
                                {{ __('Preview Recipients') }}
                            </button>
                            <div id="previewResult" class="mt-2 p-3 bg-stone-50 dark:bg-stone-800 rounded-lg hidden">
                                <p class="text-sm text-stone-600 dark:text-stone-400">
                                    <span id="recipientCount">0</span> {{ __('recipients will receive this notification') }}
                                </p>
                                <div id="recipientList" class="mt-2 space-y-1"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Notification Content -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-stone-900 dark:text-stone-100">{{ __('Notification Content') }}</h3>

                        <!-- Template Selection -->
                        <div class="space-y-3">
                            <flux:label for="template_id">{{ __('Use Template (Optional)') }}</flux:label>
                            <flux:select name="template_id" id="template_id">
                                <option value="">{{ __('Select a template or create custom') }}</option>
                                @foreach($templates ?? [] as $template)
                                    <option value="{{ $template->id }}" data-content="{{ $template->content }}" data-subject="{{ $template->subject }}">
                                        {{ $template->name }} ({{ ucfirst($template->category) }})
                                    </option>
                                @endforeach
                            </flux:select>
                        </div>

                        <!-- Notification Type -->
                        <div class="space-y-3">
                            <flux:label for="notification_type">{{ __('Notification Type') }}</flux:label>
                            <flux:select name="notification_type" id="notification_type" required>
                                <option value="announcement">{{ __('Announcement') }}</option>
                                <option value="system">{{ __('System Message') }}</option>
                                <option value="reminder">{{ __('Reminder') }}</option>
                                <option value="message">{{ __('Message') }}</option>
                            </flux:select>
                        </div>

                        <!-- Title -->
                        <div class="space-y-3">
                            <flux:label for="title">{{ __('Title') }}</flux:label>
                            <flux:input name="title" id="title" required placeholder="{{ __('Enter notification title') }}" />
                        </div>

                        <!-- Content -->
                        <div class="space-y-3">
                            <flux:label for="content">{{ __('Message') }}</flux:label>
                            <flux:textarea name="content" id="content" rows="4" required placeholder="{{ __('Enter notification message') }}"></flux:textarea>
                        </div>

                        <!-- Send Email -->
                        <div class="flex items-center space-x-3">
                            <flux:checkbox name="send_email" id="send_email" />
                            <div>
                                <flux:label for="send_email">{{ __('Send as Email') }}</flux:label>
                                <p class="text-sm text-stone-500 dark:text-stone-400">{{ __('Also send this notification via email') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Submit -->
                    <div class="flex items-center space-x-4">
                        <flux:button type="submit" variant="primary" id="sendBtn">
                            {{ __('Send Notification') }}
                        </flux:button>

                        <div id="sendingIndicator" class="hidden">
                            <div class="flex items-center space-x-2">
                                <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-blue-600"></div>
                                <span class="text-sm text-stone-600 dark:text-stone-400">{{ __('Sending...') }}</span>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Sidebar Info -->
        <div class="space-y-6">
            <!-- Quick Stats -->
            <div class="rounded-xl border border-stone-200 bg-white p-6 dark:border-stone-700 dark:bg-stone-800">
                <h3 class="text-lg font-semibold text-stone-900 dark:text-stone-100 mb-4">{{ __('Notification Stats') }}</h3>

                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-stone-600 dark:text-stone-400">{{ __('Total Users') }}</span>
                        <span class="font-semibold">{{ \App\Models\User::count() }}</span>
                    </div>

                    <div class="flex justify-between items-center">
                        <span class="text-sm text-stone-600 dark:text-stone-400">{{ __('Students') }}</span>
                        <span class="font-semibold">{{ \App\Models\User::role('student')->count() }}</span>
                    </div>

                    <div class="flex justify-between items-center">
                        <span class="text-sm text-stone-600 dark:text-stone-400">{{ __('Teachers') }}</span>
                        <span class="font-semibold">{{ \App\Models\User::role('teacher')->count() }}</span>
                    </div>
                </div>
            </div>

            <!-- Recent Templates -->
            <div class="rounded-xl border border-stone-200 bg-white p-6 dark:border-stone-700 dark:bg-stone-800">
                <h3 class="text-lg font-semibold text-stone-900 dark:text-stone-100 mb-4">{{ __('Available Templates') }}</h3>

                <div class="space-y-3">
                    @forelse($templates ?? [] as $template)
                        <div class="p-3 border border-stone-200 dark:border-stone-700 rounded-lg">
                            <h4 class="font-medium text-stone-900 dark:text-stone-100">{{ $template->name }}</h4>
                            <p class="text-sm text-stone-600 dark:text-stone-400">{{ ucfirst($template->category) }}</p>
                        </div>
                    @empty
                        <p class="text-sm text-stone-500 dark:text-stone-400">{{ __('No templates available') }}</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const recipientTypeSelect = document.getElementById('recipient_type');
            const courseSelection = document.getElementById('courseSelection');
            const semesterSelection = document.getElementById('semesterSelection');
            const recipientPreview = document.getElementById('recipientPreview');
            const templateSelect = document.getElementById('template_id');
            const titleInput = document.getElementById('title');
            const contentTextarea = document.getElementById('content');

            // Handle recipient type changes
            recipientTypeSelect.addEventListener('change', function() {
                const value = this.value;

                courseSelection.classList.add('hidden');
                semesterSelection.classList.add('hidden');
                recipientPreview.classList.add('hidden');

                if (value === 'course') {
                    courseSelection.classList.remove('hidden');
                } else if (value === 'semester') {
                    semesterSelection.classList.remove('hidden');
                }

                if (value && value !== '') {
                    recipientPreview.classList.remove('hidden');
                }
            });

            // Handle template selection
            templateSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const content = selectedOption.getAttribute('data-content');

                if (content) {
                    contentTextarea.value = content;
                    titleInput.value = selectedOption.text.split(' (')[0]; // Extract template name
                }
            });

            // Handle preview button
            document.getElementById('previewBtn')?.addEventListener('click', function() {
                const formData = new FormData(document.getElementById('bulkNotificationForm'));
                formData.append('_token', document.querySelector('[name="_token"]').value);

                fetch('{{ route("admin.notifications.preview") }}', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('recipientCount').textContent = data.count;
                        document.getElementById('recipientList').innerHTML = data.preview.map(user =>
                            `<div class="text-xs">${user.name} (${user.role})</div>`
                        ).join('');
                        document.getElementById('previewResult').classList.remove('hidden');
                    }
                });
            });

            // Handle form submission
            document.getElementById('bulkNotificationForm').addEventListener('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(this);
                const sendBtn = document.getElementById('sendBtn');
                const sendingIndicator = document.getElementById('sendingIndicator');

                sendBtn.classList.add('hidden');
                sendingIndicator.classList.remove('hidden');

                fetch('{{ route("admin.notifications.send") }}', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    sendBtn.classList.remove('hidden');
                    sendingIndicator.classList.add('hidden');

                    if (data.success) {
                        alert(`Notification sent to ${data.count} recipients!`);
                        this.reset();
                        document.getElementById('previewResult').classList.add('hidden');
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    sendBtn.classList.remove('hidden');
                    sendingIndicator.classList.add('hidden');
                    alert('Network error occurred');
                });
            });
        });
    </script>
</x-layouts::app>