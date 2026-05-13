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
                    <!-- Message Type Toggle -->
                    <div class="mb-6">
                        <div class="flex gap-4">
                            <label class="flex items-center">
                                <input type="radio" name="message_type" value="direct" {{ request('type') !== 'group' ? 'checked' : '' }} class="mr-2" onchange="toggleMessageType()">
                                <span class="text-sm font-medium">{{ __('Direct Message') }}</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="message_type" value="group" {{ request('type') === 'group' ? 'checked' : '' }} class="mr-2" onchange="toggleMessageType()">
                                <span class="text-sm font-medium">{{ __('Group Conversation') }}</span>
                            </label>
                        </div>
                    </div>

                    <!-- Direct Message Form -->
                    <form action="{{ route('messages.store') }}" method="POST" id="directMessageForm" class="block">
                        @csrf
                        <input type="hidden" name="message_type" value="direct">
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
                    </form>

                    <!-- Group Conversation Form -->
                    <form action="{{ route('messages.conversation.create') }}" method="POST" id="groupMessageForm" class="hidden">
                        @csrf
                        <div class="mb-6">
                            <flux:label>{{ __('Group Title') }} *</flux:label>
                            <flux:input name="title" placeholder="{{ __('Enter group name') }}" required />
                        </div>
                        <div class="mb-6">
                            <flux:label>{{ __('Add Members') }} *</flux:label>
                            <div class="space-y-3">
                                <div class="flex flex-wrap gap-2" id="selectedRecipients">
                                    <!-- Current user is always included -->
                                    <span class="inline-flex items-center gap-1 rounded-full bg-blue-100 px-3 py-1 text-sm font-medium text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                        {{ auth()->user()->name }} ({{ __('You') }})
                                        <input type="hidden" name="participant_ids[]" value="{{ auth()->id() }}">
                                    </span>
                                </div>
                                <select id="recipientSelect" class="w-full rounded-lg border-neutral-300 dark:border-neutral-600 dark:bg-neutral-700">
                                    <option value="">{{ __('Select members to add') }}</option>
                                    @foreach(\App\Models\User::where('id', '!=', auth()->id())->get() as $user)
                                        <option value="{{ $user->id }}">{{ $user->full_name }}</option>
                                    @endforeach
                                </select>
                                <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('Select additional members to add to the group') }}</p>
                            </div>
                        </div>
                    </form>

                    <!-- File Attachments -->
                    <div class="mb-6">
                        <flux:label>{{ __('Attachments') }}</flux:label>
                        <div class="space-y-3">
                            <div class="border-2 border-dashed border-neutral-300 dark:border-neutral-600 rounded-lg p-4 text-center hover:border-blue-400 dark:hover:border-blue-500 transition-colors">
                                <input type="file" id="attachments" name="attachments[]" multiple class="hidden" accept="image/*,application/pdf,.doc,.docx,.txt,.zip">
                                <div class="space-y-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-8 w-8 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                    </svg>
                                    <div class="text-sm">
                                        <label for="attachments" class="cursor-pointer text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 font-medium">
                                            {{ __('Click to upload files') }}
                                        </label>
                                        <span class="text-neutral-500 dark:text-neutral-400"> {{ __('or drag and drop') }}</span>
                                    </div>
                                    <p class="text-xs text-neutral-500 dark:text-neutral-400">
                                        {{ __('Supported: Images, PDF, DOC, TXT, ZIP (max 10MB each)') }}
                                    </p>
                                </div>
                            </div>
                            <div id="attachmentList" class="space-y-2"></div>
                        </div>
                    </div>

                    <!-- Message Field -->
                    <div class="mb-6">
                        <flux:textarea name="message" :label="__('Message')" rows="8" placeholder="{{ __('Write your message') }}" required></flux:textarea>
                    </div>
                        <div class="flex gap-3">
                            <button type="button" id="submitBtn" onclick="submitForm()" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                </svg>
                                {{ __('Send') }}
                            </button>
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

    <script>
        function toggleMessageType() {
            const messageType = document.querySelector('input[name="message_type"]:checked').value;
            const directForm = document.getElementById('directMessageForm');
            const groupForm = document.getElementById('groupMessageForm');
            const submitBtn = document.getElementById('submitBtn');

            if (messageType === 'group') {
                directForm.classList.add('hidden');
                directForm.classList.remove('block');
                groupForm.classList.add('block');
                groupForm.classList.remove('hidden');
                submitBtn.textContent = '{{ __("Create Group") }}';
            } else {
                groupForm.classList.add('hidden');
                groupForm.classList.remove('block');
                directForm.classList.add('block');
                directForm.classList.remove('hidden');
                submitBtn.textContent = '{{ __("Send Message") }}';
            }
        }

        function submitForm() {
            const messageType = document.querySelector('input[name="message_type"]:checked').value;
            const messageTextarea = document.querySelector('textarea[name="message"]');
            const message = messageTextarea.value.trim();

            if (!message && !hasAttachments()) {
                alert('{{ __("Please enter a message or attach files") }}');
                messageTextarea.focus();
                return;
            }

            if (messageType === 'direct') {
                const receiverId = document.querySelector('select[name="receiver_id"]').value;
                if (!receiverId) {
                    alert('{{ __("Please select a recipient") }}');
                    return;
                }

                // Copy message and attachments to direct form
                const directForm = document.getElementById('directMessageForm');
                copyMessageAndAttachmentsToForm(directForm, message);
                directForm.submit();
            } else {
                const title = document.querySelector('input[name="title"]').value.trim();
                const participantInputs = document.querySelectorAll('#groupMessageForm input[name="participant_ids[]"]');

                if (!title) {
                    alert('{{ __("Please enter a group title") }}');
                    document.querySelector('input[name="title"]').focus();
                    return;
                }

                if (participantInputs.length < 3) {
                    alert('{{ __("Please add at least one other member to the group") }}');
                    return;
                }

                // Copy message and attachments to group form
                const groupForm = document.getElementById('groupMessageForm');
                copyMessageAndAttachmentsToForm(groupForm, message);
                groupForm.submit();
            }
        }

        function hasAttachments() {
            const attachmentsInput = document.getElementById('attachments');
            return attachmentsInput && attachmentsInput.files.length > 0;
        }

        function copyMessageAndAttachmentsToForm(targetForm, message) {
            // Copy message
            const messageInput = document.createElement('input');
            messageInput.type = 'hidden';
            messageInput.name = 'message';
            messageInput.value = message;
            targetForm.appendChild(messageInput);

            // Copy attachments
            const attachmentsInput = document.getElementById('attachments');
            if (attachmentsInput && attachmentsInput.files.length > 0) {
                const newFileInput = document.createElement('input');
                newFileInput.type = 'file';
                newFileInput.name = 'attachments[]';
                newFileInput.multiple = true;
                newFileInput.style.display = 'none';

                // Copy files using DataTransfer
                const dt = new DataTransfer();
                for (let i = 0; i < attachmentsInput.files.length; i++) {
                    dt.items.add(attachmentsInput.files[i]);
                }
                newFileInput.files = dt.files;

                targetForm.appendChild(newFileInput);
            }
        }

            if (messageType === 'direct') {
                const receiverId = document.querySelector('select[name="receiver_id"]').value;
                if (!receiverId) {
                    alert('{{ __("Please select a recipient") }}');
                    return;
                }
                document.getElementById('directMessageForm').submit();
            } else {
                const title = document.querySelector('input[name="title"]').value.trim();
                const participantInputs = document.querySelectorAll('#groupMessageForm input[name="participant_ids[]"]');

                if (!title) {
                    alert('{{ __("Please enter a group title") }}');
                    document.querySelector('input[name="title"]').focus();
                    return;
                }

                if (participantInputs.length < 3) {
                    alert('{{ __("Please add at least one other member to the group") }}');
                    return;
                }

                // Copy message to group form
                const groupMessageInput = document.createElement('input');
                groupMessageInput.type = 'hidden';
                groupMessageInput.name = 'message';
                groupMessageInput.value = message;
                document.getElementById('groupMessageForm').appendChild(groupMessageInput);

                document.getElementById('groupMessageForm').submit();
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Initialize form based on URL parameter
            const urlParams = new URLSearchParams(window.location.search);
            const messageType = urlParams.get('type');

            if (messageType === 'group') {
                document.querySelector('input[name="message_type"][value="group"]').checked = true;
                toggleMessageType();
            }

            // File upload handling
            const attachmentsInput = document.getElementById('attachments');
            const attachmentList = document.getElementById('attachmentList');
            const uploadArea = document.querySelector('.border-dashed');

            if (attachmentsInput) {
                attachmentsInput.addEventListener('change', handleFileSelection);
            }

            // Drag and drop functionality
            if (uploadArea) {
                uploadArea.addEventListener('dragover', function(e) {
                    e.preventDefault();
                    this.classList.add('border-blue-500', 'bg-blue-50', 'dark:bg-blue-900/20');
                });

                uploadArea.addEventListener('dragleave', function(e) {
                    e.preventDefault();
                    this.classList.remove('border-blue-500', 'bg-blue-50', 'dark:bg-blue-900/20');
                });

                uploadArea.addEventListener('drop', function(e) {
                    e.preventDefault();
                    this.classList.remove('border-blue-500', 'bg-blue-50', 'dark:bg-blue-900/20');

                    const files = e.dataTransfer.files;
                    if (files.length > 0) {
                        handleFiles(files);
                    }
                });
            }

            const recipientSelect = document.getElementById('recipientSelect');
            const selectedRecipients = document.getElementById('selectedRecipients');

            if (recipientSelect) {
                recipientSelect.addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    const userId = selectedOption.value;
                    const userName = selectedOption.text;

                    if (userId) {
                        // Check if already selected
                        const existingBadge = document.getElementById(`badge_${userId}`);
                        if (existingBadge) {
                            alert('{{ __("This user is already added to the group") }}');
                            this.value = '';
                            return;
                        }

                        // Add visual badge
                        const badge = document.createElement('span');
                        badge.className = 'inline-flex items-center gap-1 rounded-full bg-blue-100 px-3 py-1 text-sm font-medium text-blue-800 dark:bg-blue-900 dark:text-blue-200';
                        badge.id = `badge_${userId}`;
                        badge.innerHTML = `
                            ${userName}
                            <button type="button" onclick="removeRecipient(${userId})" class="ml-1 text-blue-600 hover:text-blue-800 dark:text-blue-400">
                                <svg class="size-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        `;
                        selectedRecipients.appendChild(badge);

                        // Add hidden input
                        const hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name = 'participant_ids[]';
                        hiddenInput.value = userId;
                        hiddenInput.id = `participant_${userId}`;
                        document.getElementById('groupMessageForm').appendChild(hiddenInput);
                    }

                    this.value = '';
                });
            }
        });

        function handleFileSelection(e) {
            const files = e.target.files;
            handleFiles(files);
        }

        function handleFiles(files) {
            const attachmentList = document.getElementById('attachmentList');
            const attachmentsInput = document.getElementById('attachments');

            // Clear existing list
            attachmentList.innerHTML = '';

            // Create a new DataTransfer to update the file input
            const dt = new DataTransfer();

            for (let i = 0; i < files.length; i++) {
                const file = files[i];

                // Check file size (10MB limit)
                if (file.size > 10 * 1024 * 1024) {
                    alert(`{{ __("File") }} "${file.name}" {{ __("is too large. Maximum size is 10MB.") }}`);
                    continue;
                }

                // Add to DataTransfer
                dt.items.add(file);

                // Create file preview
                const fileItem = document.createElement('div');
                fileItem.className = 'flex items-center justify-between p-2 bg-neutral-50 dark:bg-neutral-700 rounded-lg';
                fileItem.innerHTML = `
                    <div class="flex items-center gap-2">
                        <svg class="size-4 text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <span class="text-sm font-medium text-neutral-900 dark:text-neutral-100">${file.name}</span>
                        <span class="text-xs text-neutral-500 dark:text-neutral-400">(${formatFileSize(file.size)})</span>
                    </div>
                    <button type="button" onclick="removeFile(${i})" class="text-neutral-400 hover:text-red-500">
                        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                `;
                attachmentList.appendChild(fileItem);
            }

            // Update the file input
            attachmentsInput.files = dt.files;
        }

        function removeFile(index) {
            const attachmentsInput = document.getElementById('attachments');
            const attachmentList = document.getElementById('attachmentList');

            // Create new DataTransfer without the removed file
            const dt = new DataTransfer();
            for (let i = 0; i < attachmentsInput.files.length; i++) {
                if (i !== index) {
                    dt.items.add(attachmentsInput.files[i]);
                }
            }

            attachmentsInput.files = dt.files;

            // Re-render the file list
            attachmentList.innerHTML = '';
            if (attachmentsInput.files.length > 0) {
                handleFiles(attachmentsInput.files);
            }
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        function removeRecipient(userId) {
            const badge = document.getElementById(`badge_${userId}`);
            const hiddenInput = document.getElementById(`participant_${userId}`);

            if (badge) badge.remove();
            if (hiddenInput) hiddenInput.remove();
        }
    </script>
</x-layouts::app>
