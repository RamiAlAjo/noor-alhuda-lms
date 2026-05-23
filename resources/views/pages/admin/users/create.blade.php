{{--
    =============================================================================
    ADMIN USER CREATE VIEW
    =============================================================================

    Purpose: Form to create a new user in the system with role-based fields.

    Route: admin.users.create
    Controller: Admin\UserController@create

    Components:
    - Header with back button
    - Main form with:
      * Role selection dropdown (Admin, Teacher, Student)
      * Four-part name fields (First, Second, Third, Last Name)
      * Auto-generate options for email, user ID, and password
      * Email field (shown/hidden based on auto-generate toggle)
      * Password fields with confirmation
      * Common fields (Phone, Gender, Nationality, Address, etc.)
      * Teacher-specific fields (Department, Years of Experience, Bio)
      * Student-specific fields (Major, Emergency Contact info)
      * Submit button (Create User)
    - Sidebar showing User ID format examples per role

    JavaScript Functions:
    - toggleRoleFields() - Show/hide role-specific fields
    - toggleEmailField() - Show/hide email field based on auto-generate
    - togglePasswordField() - Show/hide password fields based on auto-generate
    - generatePassword() - Generate random secure password
    - generatePasswordPreview() - Update password preview
    - regeneratePassword() - Regenerate password
    - updatePreview() - Update email preview based on name

    Required Data:
    - $roles: Available roles collection
    - $departments: Available departments (for teachers)
    - $majors: Available majors (for students)

    Dependencies:
    - route('admin.users.store') - POST endpoint to create user
    - route('admin.users.index') - Back to users list
    - old() - Laravel old input helper for form repopulation
    - @selected() - Laravel Blade directive for selected option

    =============================================================================
--}}
<x-layouts::app :title="__('Add User')">
    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-neutral-900 dark:text-neutral-100">{{ __('Add New User') }}</h1>
            <p class="mt-1 text-neutral-500 dark:text-neutral-400">{{ __('Create a new system user') }}</p>
        </div>
        <flux:button :href="route('admin.users.index')" variant="ghost">
            <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12" />
            </svg>
            {{ __('Back to Users') }}
        </flux:button>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <!-- Form -->
        <div class="lg:col-span-2">
            <div class="rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                <div class="border-b border-neutral-200 px-6 py-4 dark:border-neutral-700">
                    <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('User Information') }}</h2>
                </div>
                <div class="p-6">
                    <form method="POST" action="{{ route('admin.users.store') }}">
                        @csrf

                        <!-- Role Selection -->
                        <div class="mb-6">
                            <flux:label>{{ __('Select Role') }} *</flux:label>
                            <select name="role" id="roleSelect" class="w-full rounded-lg border-neutral-300 dark:border-neutral-600 dark:bg-neutral-700" required onchange="toggleRoleFields()">
                                <option value="">{{ __('Choose Role') }}</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->name }}" @selected(old('role') == $role->name)>
                                        {{ ucfirst($role->name) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('role')
                                <flux:error>{{ $message }}</flux:error>
                            @enderror
                        </div>

                        <!-- Four-Part Name -->
                        <div class="mb-6 grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div>
                                <flux:label>{{ __('First Name') }} *</flux:label>
                                <flux:input type="text" name="first_name" id="firstName" value="{{ old('first_name') }}" required oninput="updatePreview()" />
                                @error('first_name')
                                    <flux:error>{{ $message }}</flux:error>
                                @enderror
                            </div>
                            <div>
                                <flux:label>{{ __('Last Name') }} *</flux:label>
                                <flux:input type="text" name="last_name" id="lastName" value="{{ old('last_name') }}" required oninput="updatePreview()" />
                                @error('last_name')
                                    <flux:error>{{ $message }}</flux:error>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-6 grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div>
                                <flux:label>{{ __('Second Name') }}</flux:label>
                                <flux:input type="text" name="second_name" value="{{ old('second_name') }}" />
                            </div>
                            <div>
                                <flux:label>{{ __('Third Name') }}</flux:label>
                                <flux:input type="text" name="third_name" value="{{ old('third_name') }}" />
                            </div>
                        </div>

                        <!-- Auto-generate options -->
                        <div class="mb-6 grid grid-cols-1 gap-6 md:grid-cols-3">
                            <div class="flex items-center gap-3">
                                <input type="checkbox" name="auto_generate_email" id="autoGenerateEmail" value="1" @checked(old('auto_generate_email')) onchange="toggleEmailField()" class="rounded border-neutral-300">
                                <flux:label for="autoGenerateEmail" class="mb-0">{{ __('Auto-generate Email') }}</flux:label>
                            </div>
                            <div class="flex items-center gap-3">
                                <input type="checkbox" name="auto_generate_id" id="autoGenerateId" value="1" @checked(old('auto_generate_id', true)) checked class="rounded border-neutral-300">
                                <flux:label for="autoGenerateId" class="mb-0">{{ __('Auto-generate ID') }}</flux:label>
                            </div>
                            <div class="flex items-center gap-3">
                                <input type="checkbox" name="auto_generate_password" id="autoGeneratePassword" value="1" @checked(old('auto_generate_password')) onchange="togglePasswordField()" class="rounded border-neutral-300">
                                <flux:label for="autoGeneratePassword" class="mb-0">{{ __('Auto-generate Password') }}</flux:label>
                            </div>
                        </div>

                        <!-- Password Preview -->
                        <div class="mb-6 hidden" id="passwordPreview">
                            <flux:label>{{ __('Generated Password Preview') }}</flux:label>
                            <div class="rounded-lg bg-green-50 p-4 dark:bg-green-900/20">
                                <div class="flex items-center justify-between">
                                    <span id="passwordPreviewText" class="font-mono text-lg font-bold text-green-700 dark:text-green-400">-</span>
                                    <button type="button" onclick="regeneratePassword()" class="rounded-lg bg-green-100 px-3 py-1 text-sm font-medium text-green-700 hover:bg-green-200 dark:bg-green-800 dark:text-green-300 dark:hover:bg-green-700">
                                        {{ __('Regenerate') }}
                                    </button>
                                </div>
                                <p class="mt-2 text-xs text-green-600 dark:text-green-500">{{ __('Copy this password - it will be shown only once after creation') }}</p>
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="mb-6" id="emailField">
                            <flux:label>{{ __('Email') }} *</flux:label>
                            <flux:input type="email" name="email" id="email" value="{{ old('email') }}" />
                            @error('email')
                                <flux:error>{{ $message }}</flux:error>
                            @enderror
                        </div>

                        <!-- Email Preview -->
                        <div class="mb-6 hidden" id="emailPreview">
                            <flux:label>{{ __('Email Preview') }}</flux:label>
                            <div class="rounded-lg bg-blue-50 p-3 dark:bg-blue-900/20">
                                <span id="emailPreviewText" class="font-mono text-sm text-blue-600 dark:text-blue-400">-</span>
                            </div>
                        </div>

                        <!-- Password -->
                        <div class="mb-6" id="passwordFields">
                            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                <div>
                                    <flux:label>{{ __('Password') }} *</flux:label>
                                    <flux:input type="password" name="password" id="password" />
                                    @error('password')
                                        <flux:error>{{ $message }}</flux:error>
                                    @enderror
                                </div>
                                <div>
                                    <flux:label>{{ __('Confirm Password') }} *</flux:label>
                                    <flux:input type="password" name="password_confirmation" id="passwordConfirmation" />
                                </div>
                            </div>
                        </div>

                        <!-- Common Fields -->
                        <div class="mb-6 grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div>
                                <flux:label>{{ __('Phone') }}</flux:label>
                                <flux:input type="text" name="phone" value="{{ old('phone') }}" />
                            </div>
                            <div>
                                <flux:label>{{ __('Gender') }}</flux:label>
                                <select name="gender" class="w-full rounded-lg border-neutral-300 dark:border-neutral-600 dark:bg-neutral-700">
                                    <option value="">{{ __('Select Gender') }}</option>
                                    <option value="male" @selected(old('gender') == 'male')>{{ __('Male') }}</option>
                                    <option value="female" @selected(old('gender') == 'female')>{{ __('Female') }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-6">
                            <flux:label>{{ __('Nationality') }}</flux:label>
                            <flux:input type="text" name="nationality" value="{{ old('nationality') }}" />
                        </div>

                        <div class="mb-6">
                            <flux:label>{{ __('Address') }}</flux:label>
                            <flux:input type="text" name="address" value="{{ old('address') }}" />
                        </div>

                        <div class="mb-6 grid grid-cols-1 gap-6 md:grid-cols-3">
                            <div>
                                <flux:label>{{ __('City') }}</flux:label>
                                <flux:input type="text" name="city" value="{{ old('city') }}" />
                            </div>
                            <div>
                                <flux:label>{{ __('Country') }}</flux:label>
                                <flux:input type="text" name="country" value="{{ old('country') }}" />
                            </div>
                            <div>
                                <flux:label>{{ __('Postal Code') }}</flux:label>
                                <flux:input type="text" name="postal_code" value="{{ old('postal_code') }}" />
                            </div>
                        </div>

                        <div class="mb-6 grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div>
                                <flux:label>{{ __('Date of Birth') }}</flux:label>
                                <flux:input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}" />
                            </div>
                            <div>
                                <flux:label>{{ __('Blood Type') }}</flux:label>
                                <select name="blood_type" class="w-full rounded-lg border-neutral-300 dark:border-neutral-600 dark:bg-neutral-700">
                                    <option value="">{{ __('Select Blood Type') }}</option>
                                    @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $type)
                                        <option value="{{ $type }}" @selected(old('blood_type') == $type)>{{ $type }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Teacher-specific fields -->
                        <div id="teacherFields" class="hidden">
                            <div class="mb-6 border-t border-neutral-200 pt-6 dark:border-neutral-700">
                                <h3 class="mb-4 text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Teacher Information') }}</h3>
                            </div>

                            <div class="mb-6 grid grid-cols-1 gap-6 md:grid-cols-2">
                                <div>
                                    <flux:label>{{ __('Department') }}</flux:label>
                                    <select name="department_id" class="w-full rounded-lg border-neutral-300 dark:border-neutral-600 dark:bg-neutral-700">
                                        <option value="">{{ __('Select Department') }}</option>
                                        @foreach($departments as $department)
                                            <option value="{{ $department->id }}" @selected(old('department_id') == $department->id)>{{ $department->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <flux:label>{{ __('Years of Experience') }}</flux:label>
                                    <flux:input type="number" name="years_of_experience" value="{{ old('years_of_experience') }}" min="0" max="50" />
                                </div>
                            </div>

                            <div class="mb-6">
                                <flux:label>{{ __('Bio') }}</flux:label>
                                <textarea name="bio" rows="4" class="w-full rounded-lg border-neutral-300 dark:border-neutral-600 dark:bg-neutral-700">{{ old('bio') }}</textarea>
                            </div>
                        </div>

                        <!-- Student-specific fields -->
                        <div id="studentFields" class="hidden">
                            <div class="mb-6 border-t border-neutral-200 pt-6 dark:border-neutral-700">
                                <h3 class="mb-4 text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Student Information') }}</h3>
                            </div>

                            <div class="mb-6">
                                <flux:label>{{ __('Major') }}</flux:label>
                                <select name="major_id" class="w-full rounded-lg border-neutral-300 dark:border-neutral-600 dark:bg-neutral-700">
                                    <option value="">{{ __('Select Major') }}</option>
                                    @foreach($majors as $major)
                                        <option value="{{ $major->id }}" @selected(old('major_id') == $major->id)>{{ $major->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-6 grid grid-cols-1 gap-6 md:grid-cols-2">
                                <div>
                                    <flux:label>{{ __('Emergency Phone') }}</flux:label>
                                    <flux:input type="text" name="emergency_phone" value="{{ old('emergency_phone') }}" />
                                </div>
                                <div>
                                    <flux:label>{{ __('Emergency Contact Name') }}</flux:label>
                                    <flux:input type="text" name="emergency_contact_name" value="{{ old('emergency_contact_name') }}" />
                                </div>
                            </div>

                            <div class="mb-6">
                                <flux:label>{{ __('Relationship') }}</flux:label>
                                <flux:input type="text" name="emergency_contact_relationship" value="{{ old('emergency_contact_relationship') }}" placeholder="e.g., Parent, Sibling" />
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <x-button.submit loading-text="Creating User...">
                                <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Create User
                            </x-button.submit>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <div class="rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                <div class="border-b border-neutral-200 px-6 py-4 dark:border-neutral-700">
                    <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('User ID Info') }}</h2>
                </div>
                <div class="p-6">
                    <p class="mb-4 text-neutral-500 dark:text-neutral-400">
                        {{ __('User IDs are auto-generated based on role') }}
                    </p>
                    <div class="space-y-3">
                        <div class="flex items-center gap-3 rounded-lg bg-red-50 p-3 dark:bg-red-900/20">
                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-red-100 text-red-600 dark:bg-red-900 dark:text-red-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <div>
                                <p class="font-mono text-sm font-bold text-neutral-900 dark:text-neutral-100">ADM-2024-0001</p>
                                <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('Admin') }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 rounded-lg bg-blue-50 p-3 dark:bg-blue-900/20">
                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 text-blue-600 dark:bg-blue-900 dark:text-blue-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                            </div>
                            <div>
                                <p class="font-mono text-sm font-bold text-neutral-900 dark:text-neutral-100">T-2024-00001</p>
                                <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('Teacher') }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 rounded-lg bg-green-50 p-3 dark:bg-green-900/20">
                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-green-100 text-green-600 dark:bg-green-900 dark:text-green-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                            </div>
                            <div>
                                <p class="font-mono text-sm font-bold text-neutral-900 dark:text-neutral-100">2024-00001</p>
                                <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('Student') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleRoleFields() {
            const role = document.getElementById('roleSelect').value;
            const teacherFields = document.getElementById('teacherFields');
            const studentFields = document.getElementById('studentFields');

            teacherFields.classList.add('hidden');
            studentFields.classList.add('hidden');

            if (role === 'teacher') {
                teacherFields.classList.remove('hidden');
            } else if (role === 'student') {
                studentFields.classList.remove('hidden');
            }
        }

        function toggleEmailField() {
            const autoGenerate = document.getElementById('autoGenerateEmail').checked;
            const emailField = document.getElementById('emailField');
            const emailPreview = document.getElementById('emailPreview');

            if (autoGenerate) {
                emailField.classList.add('hidden');
                emailPreview.classList.remove('hidden');
                updatePreview();
            } else {
                emailField.classList.remove('hidden');
                emailPreview.classList.add('hidden');
            }
        }

        function togglePasswordField() {
            const autoGenerate = document.getElementById('autoGeneratePassword').checked;
            const passwordFields = document.getElementById('passwordFields');
            const passwordPreview = document.getElementById('passwordPreview');
            const passwordInput = document.getElementById('password');
            const passwordConfirm = document.getElementById('passwordConfirmation');

            if (autoGenerate) {
                passwordFields.classList.add('hidden');
                passwordPreview.classList.remove('hidden');
                passwordInput.removeAttribute('required');
                passwordConfirm.removeAttribute('required');
                generatePasswordPreview();
            } else {
                passwordFields.classList.remove('hidden');
                passwordPreview.classList.add('hidden');
                passwordInput.setAttribute('required', 'required');
                passwordConfirm.setAttribute('required', 'required');
            }
        }

        function generatePassword(length = 12) {
            const charset = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';
            let password = '';
            // Ensure at least one of each type
            password += 'abcdefghijklmnopqrstuvwxyz'[Math.floor(Math.random() * 26)];
            password += 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'[Math.floor(Math.random() * 26)];
            password += '0123456789'[Math.floor(Math.random() * 10)];
            password += '!@#$%^&*'[Math.floor(Math.random() * 8)];

            for (let i = 4; i < length; i++) {
                password += charset[Math.floor(Math.random() * charset.length)];
            }

            // Shuffle the password
            return password.split('').sort(() => Math.random() - 0.5).join('');
        }

        function generatePasswordPreview() {
            const password = generatePassword();
            document.getElementById('passwordPreviewText').textContent = password;
        }

        function regeneratePassword() {
            generatePasswordPreview();
        }

        function updatePreview() {
            const firstName = document.getElementById('firstName').value.trim().toLowerCase().replace(/[^a-z0-9]/g, '');
            const lastName = document.getElementById('lastName').value.trim().toLowerCase().replace(/[^a-z0-9]/g, '');

            if (firstName && lastName) {
                const email = firstName + '.' + lastName + '@institution.edu';
                document.getElementById('emailPreviewText').textContent = email;
            } else {
                document.getElementById('emailPreviewText').textContent = '-';
            }
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            toggleRoleFields();
            toggleEmailField();
            togglePasswordField();
        });
    </script>
</x-layouts::app>
