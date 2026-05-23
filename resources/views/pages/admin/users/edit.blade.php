{{--
    =============================================================================
    ADMIN USER EDIT VIEW
    =============================================================================

    Purpose: Form to edit an existing user's information including profile data
    and role-specific details.

    Route: admin.users.edit
    Controller: Admin\UserController@edit

    Components:
    - Header with user name and back button
    - Main form with:
      * Role selection dropdown (pre-filled with current role)
      * Four-part name fields (First, Second, Third, Last Name)
      * Email field
      * Password fields (optional - leave blank to keep current)
      * Common fields (Phone, Gender, Nationality, Personal Email, etc.)
      * Teacher-specific fields (Department, Years of Experience, Bio)
      * Student-specific fields (Major, Emergency Contact info)
      * Submit button (Update User)
    - Sidebar showing user avatar, name, email, and role badges

    JavaScript Functions:
    - toggleRoleFields() - Show/hide role-specific fields

    Required Data:
    - $user: User model being edited
    - $roles: Available roles collection
    - $departments: Available departments (for teachers)
    - $majors: Available majors (for students)

    Dependencies:
    - route('admin.users.update', $user) - PUT endpoint to update user
    - route('admin.users.index') - Back to users list
    - full_name($user) - Helper function to get user's full name
    - $user->hasRole($role) - Check if user has specific role
    - $user->profile - UserProfile relationship

    =============================================================================
--}}
<x-layouts::app :title="__('Edit User')">
    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-neutral-900 dark:text-neutral-100">{{ __('Edit User') }}: {{ full_name($user) }}</h1>
            <p class="mt-1 text-neutral-500 dark:text-neutral-400">{{ $user->profile?->display_id ?? $user->user_id }}</p>
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
                    <form method="POST" action="{{ route('admin.users.update', $user) }}">
                        @csrf
                        @method('PUT')

                        <!-- Role Selection -->
                        <div class="mb-6">
                            <flux:label>{{ __('Role') }} *</flux:label>
                            <select name="role" id="roleSelect" class="w-full rounded-lg border-neutral-300 dark:border-neutral-600 dark:bg-neutral-700" required onchange="toggleRoleFields()">
                                @foreach($roles as $role)
                                    <option value="{{ $role->name }}" @selected($user->hasRole($role->name))>
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
                                <flux:input type="text" name="first_name" value="{{ old('first_name', $user->profile?->first_name) }}" required />
                                @error('first_name')
                                    <flux:error>{{ $message }}</flux:error>
                                @enderror
                            </div>
                            <div>
                                <flux:label>{{ __('Last Name') }} *</flux:label>
                                <flux:input type="text" name="last_name" value="{{ old('last_name', $user->profile?->last_name) }}" required />
                                @error('last_name')
                                    <flux:error>{{ $message }}</flux:error>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-6 grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div>
                                <flux:label>{{ __('Second Name') }}</flux:label>
                                <flux:input type="text" name="second_name" value="{{ old('second_name', $user->profile?->second_name) }}" />
                            </div>
                            <div>
                                <flux:label>{{ __('Third Name') }}</flux:label>
                                <flux:input type="text" name="third_name" value="{{ old('third_name', $user->profile?->third_name) }}" />
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="mb-6">
                            <flux:label>{{ __('Email') }} *</flux:label>
                            <flux:input type="email" name="email" value="{{ old('email', $user->email) }}" required />
                            @error('email')
                                <flux:error>{{ $message }}</flux:error>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="mb-6 grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div>
                                <flux:label>{{ __('New Password') }}</flux:label>
                                <flux:input type="password" name="password" placeholder="{{ __('Leave blank to keep current') }}" />
                                @error('password')
                                    <flux:error>{{ $message }}</flux:error>
                                @enderror
                            </div>
                            <div>
                                <flux:label>{{ __('Confirm Password') }}</flux:label>
                                <flux:input type="password" name="password_confirmation" />
                            </div>
                        </div>

                        <!-- Common Fields -->
                        <div class="mb-6 grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div>
                                <flux:label>{{ __('Phone') }}</flux:label>
                                <flux:input type="text" name="phone" value="{{ old('phone', $user->profile?->phone) }}" />
                            </div>
                            <div>
                                <flux:label>{{ __('Gender') }}</flux:label>
                                <select name="gender" class="w-full rounded-lg border-neutral-300 dark:border-neutral-600 dark:bg-neutral-700">
                                    <option value="">{{ __('Select Gender') }}</option>
                                    <option value="male" @selected(old('gender', $user->profile?->gender) == 'male')>{{ __('Male') }}</option>
                                    <option value="female" @selected(old('gender', $user->profile?->gender) == 'female')>{{ __('Female') }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-6">
                            <flux:label>{{ __('Nationality') }}</flux:label>
                            <flux:input type="text" name="nationality" value="{{ old('nationality', $user->profile?->nationality) }}" />
                        </div>

                        <div class="mb-6">
                            <flux:label>{{ __('Personal Email') }}</flux:label>
                            <flux:input type="email" name="personal_email" value="{{ old('personal_email', $user->profile?->personal_email) }}" />
                        </div>

                        <div class="mb-6">
                            <flux:label>{{ __('Address') }}</flux:label>
                            <flux:input type="text" name="address" value="{{ old('address', $user->profile?->address) }}" />
                        </div>

                        <div class="mb-6 grid grid-cols-1 gap-6 md:grid-cols-3">
                            <div>
                                <flux:label>{{ __('City') }}</flux:label>
                                <flux:input type="text" name="city" value="{{ old('city', $user->profile?->city) }}" />
                            </div>
                            <div>
                                <flux:label>{{ __('Country') }}</flux:label>
                                <flux:input type="text" name="country" value="{{ old('country', $user->profile?->country) }}" />
                            </div>
                            <div>
                                <flux:label>{{ __('Postal Code') }}</flux:label>
                                <flux:input type="text" name="postal_code" value="{{ old('postal_code', $user->profile?->postal_code) }}" />
                            </div>
                        </div>

                        <div class="mb-6 grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div>
                                <flux:label>{{ __('Date of Birth') }}</flux:label>
                                <flux:input type="date" name="date_of_birth" value="{{ old('date_of_birth', $user->profile?->date_of_birth?->format('Y-m-d')) }}" />
                            </div>
                            <div>
                                <flux:label>{{ __('Blood Type') }}</flux:label>
                                <select name="blood_type" class="w-full rounded-lg border-neutral-300 dark:border-neutral-600 dark:bg-neutral-700">
                                    <option value="">{{ __('Select Blood Type') }}</option>
                                    @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $type)
                                        <option value="{{ $type }}" @selected(old('blood_type', $user->profile?->blood_type) == $type)>{{ $type }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Teacher-specific fields -->
                        <div id="teacherFields" class="{{ $user->hasRole('teacher') ? '' : 'hidden' }}">
                            <div class="mb-6 border-t border-neutral-200 pt-6 dark:border-neutral-700">
                                <h3 class="mb-4 text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Teacher Information') }}</h3>
                            </div>

                            <div class="mb-6 grid grid-cols-1 gap-6 md:grid-cols-2">
                                <div>
                                    <flux:label>{{ __('Department') }}</flux:label>
                                    <select name="department_id" class="w-full rounded-lg border-neutral-300 dark:border-neutral-600 dark:bg-neutral-700">
                                        <option value="">{{ __('Select Department') }}</option>
                                        @foreach($departments as $department)
                                            <option value="{{ $department->id }}" @selected(old('department_id', $user->profile?->department_id) == $department->id)>{{ $department->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <flux:label>{{ __('Years of Experience') }}</flux:label>
                                    <flux:input type="number" name="years_of_experience" value="{{ old('years_of_experience', $user->profile?->years_of_experience) }}" min="0" max="50" />
                                </div>
                            </div>

                            <div class="mb-6">
                                <flux:label>{{ __('Bio') }}</flux:label>
                                <textarea name="bio" rows="4" class="w-full rounded-lg border-neutral-300 dark:border-neutral-600 dark:bg-neutral-700">{{ old('bio', $user->profile?->bio) }}</textarea>
                            </div>
                        </div>

                        <!-- Student-specific fields -->
                        <div id="studentFields" class="{{ $user->hasRole('student') ? '' : 'hidden' }}">
                            <div class="mb-6 border-t border-neutral-200 pt-6 dark:border-neutral-700">
                                <h3 class="mb-4 text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Student Information') }}</h3>
                            </div>

                            <div class="mb-6">
                                <flux:label>{{ __('Major') }}</flux:label>
                                <select name="major_id" class="w-full rounded-lg border-neutral-300 dark:border-neutral-600 dark:bg-neutral-700">
                                    <option value="">{{ __('Select Major') }}</option>
                                    @foreach($majors as $major)
                                        <option value="{{ $major->id }}" @selected(old('major_id', $user->profile?->major_id ?? $user->major_id) == $major->id)>{{ $major->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-6 grid grid-cols-1 gap-6 md:grid-cols-2">
                                <div>
                                    <flux:label>{{ __('Emergency Phone') }}</flux:label>
                                    <flux:input type="text" name="emergency_phone" value="{{ old('emergency_phone', $user->profile?->emergency_phone) }}" />
                                </div>
                                <div>
                                    <flux:label>{{ __('Emergency Contact Name') }}</flux:label>
                                    <flux:input type="text" name="emergency_contact_name" value="{{ old('emergency_contact_name', $user->profile?->emergency_contact_name) }}" />
                                </div>
                            </div>

                            <div class="mb-6">
                                <flux:label>{{ __('Relationship') }}</flux:label>
                                <flux:input type="text" name="emergency_contact_relationship" value="{{ old('emergency_contact_relationship', $user->profile?->emergency_contact_relationship) }}" placeholder="e.g., Parent, Sibling" />
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <x-button.submit loading-text="Updating User...">
                                <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Update User
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
                    <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('User Profile') }}</h2>
                </div>
                <div class="p-6 text-center">
                    <div class="mb-4 inline-flex h-20 w-20 items-center justify-center rounded-full bg-gradient-to-br from-blue-500 to-purple-600 text-3xl font-bold text-white shadow-lg">
                        {{ strtoupper(substr($user->profile?->first_name ?? $user->name, 0, 1)) }}
                    </div>
                    <h3 class="text-xl font-semibold text-neutral-900 dark:text-neutral-100">{{ full_name($user) }}</h3>
                    <p class="mb-4 text-neutral-500 dark:text-neutral-400">{{ $user->email }}</p>
                    <div class="flex flex-wrap justify-center gap-2">
                        @foreach($user->roles as $role)
                            @if($role->name == 'admin')
                                <span class="inline-flex items-center rounded-full bg-red-100 px-3 py-1 text-xs font-medium text-red-800 dark:bg-red-900 dark:text-red-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 size-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                    </svg>
                                    {{ ucfirst($role->name) }}
                                </span>
                            @elseif($role->name == 'teacher')
                                <span class="inline-flex items-center rounded-full bg-blue-100 px-3 py-1 text-xs font-medium text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 size-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                    </svg>
                                    {{ ucfirst($role->name) }}
                                </span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-800 dark:bg-green-900 dark:text-green-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 size-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                    </svg>
                                    {{ ucfirst($role->name) }}
                                </span>
                            @endif
                        @endforeach
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
    </script>
</x-layouts::app>

