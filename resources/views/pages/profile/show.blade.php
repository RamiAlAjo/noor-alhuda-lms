<x-layouts::app :title="__('My Profile')">
    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-neutral-900 dark:text-neutral-100">{{ __('My Profile') }}</h1>
            <p class="mt-1 text-neutral-500 dark:text-neutral-400">{{ __('View and manage your profile information') }}</p>
        </div>
        <flux:button :href="route('profile.edit')" variant="primary">
            <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
            {{ __('Edit Profile') }}
        </flux:button>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="mb-6 rounded-lg bg-green-50 p-4 text-green-800 dark:bg-green-900/20 dark:text-green-200">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid gap-6 lg:grid-cols-4">
        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <div class="rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                <div class="p-6 text-center">
                    <div class="mb-4 inline-flex h-24 w-24 items-center justify-center rounded-full bg-gradient-to-br from-blue-500 to-purple-600 text-4xl font-bold text-white shadow-lg">
                        {{ strtoupper(substr($user->profile?->first_name ?? $user->name, 0, 1)) }}
                    </div>
                    <h3 class="text-xl font-semibold text-neutral-900 dark:text-neutral-100">{{ $user->profile?->full_name ?? $user->name }}</h3>
                    <p class="mb-4 text-neutral-500 dark:text-neutral-400">{{ $user->email }}</p>
                    <div class="flex flex-wrap justify-center gap-2">
                        @foreach($user->roles as $role)
                            @if($role->name == 'admin')
                                <span class="inline-flex items-center rounded-full bg-red-100 px-3 py-1 text-xs font-medium text-red-800 dark:bg-red-900 dark:text-red-200">
                                    {{ ucfirst($role->name) }}
                                </span>
                            @elseif($role->name == 'teacher')
                                <span class="inline-flex items-center rounded-full bg-blue-100 px-3 py-1 text-xs font-medium text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                    {{ ucfirst($role->name) }}
                                </span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-800 dark:bg-green-900 dark:text-green-200">
                                    {{ ucfirst($role->name) }}
                                </span>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="mt-6 rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                <div class="border-b border-neutral-200 px-6 py-4 dark:border-neutral-700">
                    <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Account Info') }}</h2>
                </div>
                <div class="p-6">
                    <dl class="space-y-4">
                        <div class="flex justify-between">
                            <dt class="text-neutral-500 dark:text-neutral-400">{{ __('User ID') }}</dt>
                            <dd class="font-mono font-medium text-neutral-900 dark:text-neutral-100">{{ $user->profile?->display_id ?? $user->user_id }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-neutral-500 dark:text-neutral-400">{{ __('Email') }}</dt>
                            <dd class="text-neutral-900 dark:text-neutral-100">{{ $user->email }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-neutral-500 dark:text-neutral-400">{{ __('Status') }}</dt>
                            <dd>
                                @if($user->email_verified_at)
                                    <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800 dark:bg-green-900 dark:text-green-200">
                                        {{ __('Verified') }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-yellow-100 px-2.5 py-0.5 text-xs font-medium text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                        {{ __('Pending') }}
                                    </span>
                                @endif
                            </dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-neutral-500 dark:text-neutral-400">{{ __('Joined') }}</dt>
                            <dd class="text-neutral-900 dark:text-neutral-100">{{ $user->created_at->format('M d, Y') }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="lg:col-span-3 space-y-6">
            <!-- Personal Info -->
            <div class="rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                <div class="border-b border-neutral-200 px-6 py-4 dark:border-neutral-700">
                    <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Personal Information') }}</h2>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div class="flex justify-between py-2 border-b border-neutral-100 dark:border-neutral-700">
                            <dt class="text-neutral-500 dark:text-neutral-400">{{ __('First Name') }}</dt>
                            <dd class="font-medium text-neutral-900 dark:text-neutral-100">{{ $user->profile?->first_name ?? '-' }}</dd>
                        </div>
                        <div class="flex justify-between py-2 border-b border-neutral-100 dark:border-neutral-700">
                            <dt class="text-neutral-500 dark:text-neutral-400">{{ __('Second Name') }}</dt>
                            <dd class="font-medium text-neutral-900 dark:text-neutral-100">{{ $user->profile?->second_name ?? '-' }}</dd>
                        </div>
                        <div class="flex justify-between py-2 border-b border-neutral-100 dark:border-neutral-700">
                            <dt class="text-neutral-500 dark:text-neutral-400">{{ __('Third Name') }}</dt>
                            <dd class="font-medium text-neutral-900 dark:text-neutral-100">{{ $user->profile?->third_name ?? '-' }}</dd>
                        </div>
                        <div class="flex justify-between py-2 border-b border-neutral-100 dark:border-neutral-700">
                            <dt class="text-neutral-500 dark:text-neutral-400">{{ __('Last Name') }}</dt>
                            <dd class="font-medium text-neutral-900 dark:text-neutral-100">{{ $user->profile?->last_name ?? '-' }}</dd>
                        </div>
                        <div class="flex justify-between py-2 border-b border-neutral-100 dark:border-neutral-700">
                            <dt class="text-neutral-500 dark:text-neutral-400">{{ __('Nationality') }}</dt>
                            <dd class="font-medium text-neutral-900 dark:text-neutral-100">{{ $user->profile?->nationality ?? '-' }}</dd>
                        </div>
                        <div class="flex justify-between py-2 border-b border-neutral-100 dark:border-neutral-700">
                            <dt class="text-neutral-500 dark:text-neutral-400">{{ __('Phone') }}</dt>
                            <dd class="font-medium text-neutral-900 dark:text-neutral-100">{{ $user->profile?->phone ?? '-' }}</dd>
                        </div>
                        <div class="flex justify-between py-2 border-b border-neutral-100 dark:border-neutral-700">
                            <dt class="text-neutral-500 dark:text-neutral-400">{{ __('Personal Email') }}</dt>
                            <dd class="font-medium text-neutral-900 dark:text-neutral-100">{{ $user->profile?->personal_email ?? '-' }}</dd>
                        </div>
                        <div class="flex justify-between py-2 border-b border-neutral-100 dark:border-neutral-700">
                            <dt class="text-neutral-500 dark:text-neutral-400">{{ __('Gender') }}</dt>
                            <dd class="font-medium text-neutral-900 dark:text-neutral-100">
                                @if($user->profile?->gender)
                                    {{ __($user->profile->gender) }}
                                @else
                                    -
                                @endif
                            </dd>
                        </div>
                        <div class="flex justify-between py-2 border-b border-neutral-100 dark:border-neutral-700">
                            <dt class="text-neutral-500 dark:text-neutral-400">{{ __('Date of Birth') }}</dt>
                            <dd class="font-medium text-neutral-900 dark:text-neutral-100">{{ $user->profile?->date_of_birth?->format('M d, Y') ?? '-' }}</dd>
                        </div>
                        <div class="flex justify-between py-2 border-b border-neutral-100 dark:border-neutral-700">
                            <dt class="text-neutral-500 dark:text-neutral-400">{{ __('Blood Type') }}</dt>
                            <dd class="font-medium text-neutral-900 dark:text-neutral-100">{{ $user->profile?->blood_type ?? '-' }}</dd>
                        </div>
                        <div class="col-span-2 py-2 border-b border-neutral-100 dark:border-neutral-700">
                            <dt class="text-neutral-500 dark:text-neutral-400">{{ __('Address') }}</dt>
                            <dd class="font-medium text-neutral-900 dark:text-neutral-100">
                                @if($user->profile?->full_address)
                                    {{ $user->profile->full_address }}
                                @else
                                    -
                                @endif
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Emergency Contact -->
            <div class="rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                <div class="border-b border-neutral-200 px-6 py-4 dark:border-neutral-700">
                    <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Emergency Contact Information') }}</h2>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div class="flex justify-between py-2 border-b border-neutral-100 dark:border-neutral-700">
                            <dt class="text-neutral-500 dark:text-neutral-400">{{ __('Emergency Contact Name') }}</dt>
                            <dd class="font-medium text-neutral-900 dark:text-neutral-100">{{ $user->profile?->emergency_contact_name ?? '-' }}</dd>
                        </div>
                        <div class="flex justify-between py-2 border-b border-neutral-100 dark:border-neutral-700">
                            <dt class="text-neutral-500 dark:text-neutral-400">{{ __('Relationship') }}</dt>
                            <dd class="font-medium text-neutral-900 dark:text-neutral-100">{{ $user->profile?->emergency_contact_relationship ?? '-' }}</dd>
                        </div>
                        <div class="flex justify-between py-2 border-b border-neutral-100 dark:border-neutral-700">
                            <dt class="text-neutral-500 dark:text-neutral-400">{{ __('Emergency Phone') }}</dt>
                            <dd class="font-medium text-neutral-900 dark:text-neutral-100">{{ $user->profile?->emergency_phone ?? '-' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Social Links -->
            @if($user->profile?->social_links && count($user->profile->social_links) > 0)
            <div class="rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                <div class="border-b border-neutral-200 px-6 py-4 dark:border-neutral-700">
                    <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Social Links') }}</h2>
                </div>
                <div class="p-6">
                    <div class="flex flex-wrap gap-4">
                        @if($user->profile->hasSocialLink('facebook'))
                            <a href="{{ $user->profile->getSocialLink('facebook') }}" target="_blank" class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
                                <svg class="mr-2 h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                                Facebook
                            </a>
                        @endif
                        @if($user->profile->hasSocialLink('twitter'))
                            <a href="{{ $user->profile->getSocialLink('twitter') }}" target="_blank" class="inline-flex items-center rounded-lg bg-sky-500 px-4 py-2 text-white hover:bg-sky-600">
                                <svg class="mr-2 h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                                Twitter
                            </a>
                        @endif
                        @if($user->profile->hasSocialLink('linkedin'))
                            <a href="{{ $user->profile->getSocialLink('linkedin') }}" target="_blank" class="inline-flex items-center rounded-lg bg-blue-700 px-4 py-2 text-white hover:bg-blue-800">
                                <svg class="mr-2 h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                                LinkedIn
                            </a>
                        @endif
                        @if($user->profile->hasSocialLink('instagram'))
                            <a href="{{ $user->profile->getSocialLink('instagram') }}" target="_blank" class="inline-flex items-center rounded-lg bg-pink-600 px-4 py-2 text-white hover:bg-pink-700">
                                <svg class="mr-2 h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                                Instagram
                            </a>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <!-- Teacher-specific Info -->
            @if($user->isTeacher())
            <div class="rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                <div class="border-b border-neutral-200 px-6 py-4 dark:border-neutral-700">
                    <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Teacher Information') }}</h2>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div class="flex justify-between py-2 border-b border-neutral-100 dark:border-neutral-700">
                            <dt class="text-neutral-500 dark:text-neutral-400">{{ __('Department') }}</dt>
                            <dd class="font-medium text-neutral-900 dark:text-neutral-100">{{ $user->profile?->department?->name ?? '-' }}</dd>
                        </div>
                        <div class="flex justify-between py-2 border-b border-neutral-100 dark:border-neutral-700">
                            <dt class="text-neutral-500 dark:text-neutral-400">{{ __('Years of Experience') }}</dt>
                            <dd class="font-medium text-neutral-900 dark:text-neutral-100">{{ $user->profile?->years_of_experience ?? '-' }}</dd>
                        </div>
                        <div class="col-span-2 py-2 border-b border-neutral-100 dark:border-neutral-700">
                            <dt class="mb-2 text-neutral-500 dark:text-neutral-400">{{ __('Bio') }}</dt>
                            <dd class="font-medium text-neutral-900 dark:text-neutral-100">{{ $user->profile?->bio ?? '-' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
            @endif
        </div>
    </div>
</x-layouts::app>
