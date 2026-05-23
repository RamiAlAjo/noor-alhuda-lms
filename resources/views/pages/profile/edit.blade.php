<x-layouts::app :title="__('Edit Profile')">
    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-neutral-900 dark:text-neutral-100">{{ __('Edit Profile') }}</h1>
            <p class="mt-1 text-neutral-500 dark:text-neutral-400">{{ __('Update your profile information') }}</p>
        </div>
        <flux:button :href="route('profile.show')" variant="ghost">
            <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12" />
            </svg>
            {{ __('Back to Profile') }}
        </flux:button>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="mb-6 rounded-lg bg-green-50 p-4 text-green-800 dark:bg-green-900/20 dark:text-green-200">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid gap-6 lg:grid-cols-3">
        <!-- Form -->
        <div class="lg:col-span-2">
            <div class="rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                <div class="border-b border-neutral-200 px-6 py-4 dark:border-neutral-700">
                    <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Personal Information') }}</h2>
                </div>
                <div class="p-6">
                    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

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

                        <!-- Contact Info -->
                        <div class="mb-6 grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div>
                                <flux:label>{{ __('Phone') }}</flux:label>
                                <flux:input type="text" name="phone" value="{{ old('phone', $user->profile?->phone) }}" />
                            </div>
                            <div>
                                <flux:label>{{ __('Personal Email') }}</flux:label>
                                <flux:input type="email" name="personal_email" value="{{ old('personal_email', $user->profile?->personal_email) }}" />
                            </div>
                        </div>

                        <div class="mb-6">
                            <flux:label>{{ __('Nationality') }}</flux:label>
                            <flux:input type="text" name="nationality" value="{{ old('nationality', $user->profile?->nationality) }}" />
                        </div>

                        <!-- Gender and DOB -->
                        <div class="mb-6 grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div>
                                <flux:label>{{ __('Gender') }}</flux:label>
                                <select name="gender" class="w-full rounded-lg border-neutral-300 dark:border-neutral-600 dark:bg-neutral-700">
                                    <option value="">{{ __('Select Gender') }}</option>
                                    <option value="male" @selected(old('gender', $user->profile?->gender) == 'male')>{{ __('Male') }}</option>
                                    <option value="female" @selected(old('gender', $user->profile?->gender) == 'female')>{{ __('Female') }}</option>
                                </select>
                            </div>
                            <div>
                                <flux:label>{{ __('Date of Birth') }}</flux:label>
                                <flux:input type="date" name="date_of_birth" value="{{ old('date_of_birth', $user->profile?->date_of_birth?->format('Y-m-d')) }}" />
                            </div>
                        </div>

                        <!-- Address -->
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

                        <div class="flex justify-end">
                            <x-button.submit loading-text="{{ __('Saving...') }}">
                                {{ __('Save Changes') }}
                            </x-button.submit>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Emergency Contact Section -->
            <div class="mt-6 rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                <div class="border-b border-neutral-200 px-6 py-4 dark:border-neutral-700">
                    <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Emergency Contact Information') }}</h2>
                </div>
                <div class="p-6">
                    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-6 grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div>
                                <flux:label>{{ __('Emergency Contact Name') }}</flux:label>
                                <flux:input type="text" name="emergency_contact_name" value="{{ old('emergency_contact_name', $user->profile?->emergency_contact_name) }}" />
                            </div>
                            <div>
                                <flux:label>{{ __('Relationship') }}</flux:label>
                                <flux:input type="text" name="emergency_contact_relationship" value="{{ old('emergency_contact_relationship', $user->profile?->emergency_contact_relationship) }}" placeholder="e.g., Parent, Sibling" />
                            </div>
                        </div>

                        <div class="mb-6">
                            <flux:label>{{ __('Emergency Phone') }}</flux:label>
                            <flux:input type="text" name="emergency_phone" value="{{ old('emergency_phone', $user->profile?->emergency_phone) }}" />
                        </div>

                        <div class="flex justify-end">
                            <x-button.submit loading-text="{{ __('Saving...') }}">
                                {{ __('Save Changes') }}
                            </x-button.submit>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Social Links Section -->
            <div class="mt-6 rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                <div class="border-b border-neutral-200 px-6 py-4 dark:border-neutral-700">
                    <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Social Links') }}</h2>
                </div>
                <div class="p-6">
                    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-6">
                            <flux:label>{{ __('lms.facebook_url') }}</flux:label>
                            <flux:input type="url" name="facebook" value="{{ old('facebook', $user->profile?->social_links['facebook'] ?? '') }}" placeholder="https://facebook.com/username" />
                        </div>

                        <div class="mb-6">
                            <flux:label>{{ __('lms.twitter_url') }}</flux:label>
                            <flux:input type="url" name="twitter" value="{{ old('twitter', $user->profile?->social_links['twitter'] ?? '') }}" placeholder="https://twitter.com/username" />
                        </div>

                        <div class="mb-6">
                            <flux:label>{{ __('lms.linkedin_url') }}</flux:label>
                            <flux:input type="url" name="linkedin" value="{{ old('linkedin', $user->profile?->social_links['linkedin'] ?? '') }}" placeholder="https://linkedin.com/in/username" />
                        </div>

                        <div class="mb-6">
                            <flux:label>{{ __('lms.instagram_url') }}</flux:label>
                            <flux:input type="url" name="instagram" value="{{ old('instagram', $user->profile?->social_links['instagram'] ?? '') }}" placeholder="https://instagram.com/username" />
                        </div>

                        <div class="flex justify-end">
                            <x-button.submit loading-text="{{ __('Saving...') }}">
                                {{ __('Save Changes') }}
                            </x-button.submit>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <div class="rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                <div class="p-6 text-center">
                    <div class="mb-4 inline-flex h-24 w-24 items-center justify-center rounded-full bg-gradient-to-br from-blue-500 to-purple-600 text-4xl font-bold text-white shadow-lg">
                        {{ strtoupper(substr($user->profile?->first_name ?? $user->name, 0, 1)) }}
                    </div>
                    <h3 class="text-xl font-semibold text-neutral-900 dark:text-neutral-100">{{ $user->profile?->full_name ?? $user->name }}</h3>
                    <p class="mb-4 text-neutral-500 dark:text-neutral-400">{{ $user->email }}</p>
                </div>
            </div>

            <!-- Change Password -->
            <div class="mt-6 rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                <div class="border-b border-neutral-200 px-6 py-4 dark:border-neutral-700">
                    <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Change Password') }}</h2>
                </div>
                <div class="p-6">
                    <form method="POST" action="{{ route('profile.password.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <flux:label>{{ __('Current Password') }}</flux:label>
                            <flux:input type="password" name="current_password" required />
                            @error('current_password')
                                <flux:error>{{ $message }}</flux:error>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <flux:label>{{ __('New Password') }}</flux:label>
                            <flux:input type="password" name="password" required />
                            @error('password')
                                <flux:error>{{ $message }}</flux:error>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <flux:label>{{ __('Confirm Password') }}</flux:label>
                            <flux:input type="password" name="password_confirmation" required />
                        </div>

                        <x-button.submit loading-text="{{ __('Updating...') }}" class="w-full">
                            {{ __('Update Password') }}
                        </x-button.submit>
                    </form>
                </div>
            </div>

            @if($user->isTeacher())
            <!-- Teacher Bio & CV -->
            <div class="mt-6 rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                <div class="border-b border-neutral-200 px-6 py-4 dark:border-neutral-700">
                    <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Bio & Resume') }}</h2>
                </div>
                <div class="p-6">
                    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <flux:label>{{ __('About Me') }}</flux:label>
                            <textarea name="bio" rows="4" class="w-full rounded-lg border-neutral-300 dark:border-neutral-600 dark:bg-neutral-700">{{ old('bio', $user->profile?->bio) }}</textarea>
                        </div>

                        <div class="mb-4">
                            <flux:label>{{ __('Upload CV/Resume') }}</flux:label>
                            <input
                                type="file"
                                name="cv"
                                accept=".pdf,.doc,.docx"
                                class="block w-full text-sm text-neutral-500
                                    file:mr-4 file:py-2 file:px-4
                                    file:rounded-full file:border-0
                                    file:text-sm file:font-semibold
                                    file:bg-blue-50 file:text-blue-700
                                    hover:file:bg-blue-100
                                    dark:file:bg-blue-900 dark:file:text-blue-300
                                "
                            />
                            <p class="mt-1 text-xs text-neutral-500 dark:text-neutral-400">{{ __('PDF, DOC, or DOCX (Max 5MB)') }}</p>
                            @if($user->profile?->cv)
                                <div class="mt-2 flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="size-4 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span class="text-sm text-green-600 dark:text-green-400">{{ __('CV uploaded') }}</span>
                                    <a href="{{ asset('storage/' . $user->profile?->cv) }}" target="_blank" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">{{ __('View') }}</a>
                                </div>
                            @endif
                        </div>

                        <x-button.submit loading-text="{{ __('Saving...') }}" class="w-full">
                            {{ __('Save Bio & CV') }}
                        </x-button.submit>
                    </form>
                </div>
            </div>
            @endif
        </div>
    </div>
</x-layouts::app>
