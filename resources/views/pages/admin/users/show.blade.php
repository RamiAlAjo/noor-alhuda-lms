{{--
    =============================================================================
    ADMIN USER DETAILS VIEW
    =============================================================================

    Purpose: Display detailed information about a specific user including
    their profile, credentials, and role-specific information.

    Route: admin.users.show
    Controller: Admin\UserController@show

    Components:
    - Credentials Card (blue highlighted) with login info and password
      * Print button to print credentials
      * Copy password button
      * Send email button to send credentials
    - Print-only section for credential printing
    - Header with user name and action buttons (Edit, Back to Users)
    - Sidebar with:
      * User avatar and name
      * Role badges (Admin/Teacher/Student)
      * Account info (User ID, Email, Status, Joined date)
    - Main content with:
      * Personal Information section (name parts, nationality, phone, etc.)
      * Teacher Information section (if teacher role)
      * Student Information section (if student role)
      * Enrollments table (for students)
      * Assigned Courses table (for teachers)

    Required Data:
    - $user: User model with loaded relationships
    - session('generated_password'): Temporary password if recently created

    Dependencies:
    - full_name($user) - Helper function to get user's full name
    - route('admin.users.edit', $user) - Edit user page
    - route('admin.users.index') - Back to users list
    - route('admin.users.send-credentials', $user) - Send credentials email
    - $user->profile - UserProfile relationship
    - $user->roles - Role relationship
    - $user->hasRole($role) - Check user role
    - $user->enrollments - Student enrollments
    - $user->taughtCourses - Teacher's assigned courses

    =============================================================================
--}}
<x-layouts::app :title="full_name($user)">
    <!-- Credentials Card - Always Visible -->
    <div id="credentials-card" class="mb-6 rounded-xl border-2 border-blue-500 bg-gradient-to-r from-blue-50 to-indigo-50 p-6 dark:border-blue-600 dark:from-blue-900/30 dark:to-indigo-900/30 no-print">
        <div class="flex items-start justify-between">
            <div class="flex items-start gap-4">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-800">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-blue-800 dark:text-blue-200">{{ __('Login Credentials') }}</h3>
                    <p class="mt-1 text-sm text-blue-700 dark:text-blue-300">{{ __('User login information') }}</p>
                </div>
            </div>
            <!-- Print Button -->
            <button onclick="window.print()" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 inline h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                {{ __('Print Credentials') }}
            </button>
        </div>

        <div class="mt-4 grid grid-cols-2 gap-4 md:grid-cols-4">
            <div class="rounded-lg bg-white p-3 shadow-sm dark:bg-blue-900/50">
                <p class="text-xs text-blue-600 dark:text-blue-400">{{ __('Name') }}</p>
                <p class="font-semibold text-blue-900 dark:text-blue-100">{{ full_name($user) }}</p>
            </div>
            <div class="rounded-lg bg-white p-3 shadow-sm dark:bg-blue-900/50">
                <p class="text-xs text-blue-600 dark:text-blue-400">{{ __('Email') }}</p>
                <p class="font-semibold text-blue-900 dark:text-blue-100">{{ $user->email }}</p>
            </div>
            <div class="rounded-lg bg-white p-3 shadow-sm dark:bg-blue-900/50">
                <p class="text-xs text-blue-600 dark:text-blue-400">{{ __('User ID') }}</p>
                <p class="font-mono font-semibold text-blue-900 dark:text-blue-100">{{ $user->profile?->display_id ?? $user->user_id }}</p>
            </div>
            <div class="rounded-lg bg-white p-3 shadow-sm dark:bg-blue-900/50">
                <p class="text-xs text-blue-600 dark:text-blue-400">{{ __('Password') }}</p>
                @if(session('generated_password'))
                    <p class="font-mono font-semibold text-blue-900 dark:text-blue-100">{{ session('generated_password') }}</p>
                @elseif($user->profile?->initial_password)
                    <p class="font-mono font-semibold text-blue-900 dark:text-blue-100">{{ $user->profile?->initial_password }}</p>
                @else
                    <p class="text-xs text-blue-500 dark:text-blue-400">********</p>
                @endif
            </div>
        </div>

        @if(session('generated_password') || $user->profile?->initial_password)
        <div class="mt-4 flex gap-3">
            <button onclick="navigator.clipboard.writeText('{{ session('generated_password') ?? $user->profile?->initial_password }}')" class="rounded-lg bg-blue-100 px-3 py-2 text-sm font-medium text-blue-700 hover:bg-blue-200 dark:bg-blue-800 dark:text-blue-300 dark:hover:bg-blue-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 inline h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                </svg>
                {{ __('Copy Password') }}
            </button>
            <form method="POST" action="{{ route('admin.users.send-credentials', $user) }}" class="inline">
                @csrf
                <input type="hidden" name="password" value="{{ session('generated_password') ?? $user->profile?->initial_password }}">
                <x-button.submit loading-text="{{ __('Sending...') }}" class="rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 inline h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    {{ __('Send Email') }}
                </x-button.submit>
            </form>
        </div>
        @endif
    </div>

    <!-- Print section - visible when printing -->
    <div class="print-only">
        <div style="padding: 20px; font-family: Arial, sans-serif;">
            <h1 style="color: #4f46e5; text-align: center;">{{ __('lms.app_name') }} - {{ __('lms.login_credentials') }}</h1>
            <hr style="border-color: #4f46e5; margin: 20px 0;">
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="padding: 10px; border: 1px solid #ccc; font-weight: bold;">{{ __('lms.name') }}</td>
                    <td style="padding: 10px; border: 1px solid #ccc;">{{ full_name($user) }}</td>
                </tr>
                <tr>
                    <td style="padding: 10px; border: 1px solid #ccc; font-weight: bold;">{{ __('lms.email') }}</td>
                    <td style="padding: 10px; border: 1px solid #ccc;">{{ $user->email }}</td>
                </tr>
                <tr>
                    <td style="padding: 10px; border: 1px solid #ccc; font-weight: bold;">{{ __('lms.user_id') }}</td>
                    <td style="padding: 10px; border: 1px solid #ccc; font-family: monospace;">{{ $user->profile?->display_id ?? $user->user_id }}</td>
                </tr>
                <tr>
                    <td style="padding: 10px; border: 1px solid #ccc; font-weight: bold;">{{ __('lms.password') }}</td>
                    <td style="padding: 10px; border: 1px solid #ccc; font-family: monospace;">
                        @if(session('generated_password'))
                            {{ session('generated_password') }}
                        @elseif($user->profile?->initial_password)
                            {{ $user->profile?->initial_password }}
                        @else
                            ({{ __('lms.contact_admin_password') }})
                        @endif
                    </td>
                </tr>
            </table>
            <p style="text-align: center; color: #666; margin-top: 20px;">{{ __('lms.change_password_notice') }}</p>
        </div>
    </div>

    <style>
    @media print {
        .no-print { display: none !important; }
        .print-only { display: block !important; }
    }
    </style>

    <!-- Header -->
    <div class="mb-8 flex items-center justify-between no-print">
        <div>
            <h1 class="text-3xl font-bold text-neutral-900 dark:text-neutral-100">{{ full_name($user) }}</h1>
            <p class="mt-1 text-neutral-500 dark:text-neutral-400">{{ $user->profile?->display_id }}</p>
        </div>
        <div class="flex gap-3">
            <flux:button :href="route('admin.users.edit', $user)" variant="primary">
                <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                {{ __('Edit') }}
            </flux:button>
            <flux:button :href="route('admin.users.index')" variant="ghost">
                <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12" />
                </svg>
                {{ __('Back to Users') }}
            </flux:button>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-4 no-print">
        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <div class="rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                <div class="p-6 text-center">
                    <div class="mb-4 inline-flex h-24 w-24 items-center justify-center rounded-full bg-gradient-to-br from-blue-500 to-purple-600 text-4xl font-bold text-white shadow-lg">
                        {{ strtoupper(substr($user->profile?->first_name ?? $user->name, 0, 1)) }}
                    </div>
                    <h3 class="text-xl font-semibold text-neutral-900 dark:text-neutral-100">{{ full_name($user) }}</h3>
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
                                    {{ __($user->profile?->gender) }}
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
                                    {{ $user->profile?->full_address }}
                                @else
                                    -
                                @endif
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Teacher-specific Info -->
            @if($user->hasRole('teacher'))
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

            <!-- Student-specific Info -->
            @if($user->hasRole('student'))
            <div class="rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                <div class="border-b border-neutral-200 px-6 py-4 dark:border-neutral-700">
                    <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Student Information') }}</h2>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div class="flex justify-between py-2 border-b border-neutral-100 dark:border-neutral-700">
                            <dt class="text-neutral-500 dark:text-neutral-400">{{ __('Major') }}</dt>
                            <dd class="font-medium text-neutral-900 dark:text-neutral-100">{{ $user->profile?->major?->name ?? $user->major?->name ?? '-' }}</dd>
                        </div>
                        <div class="flex justify-between py-2 border-b border-neutral-100 dark:border-neutral-700">
                            <dt class="text-neutral-500 dark:text-neutral-400">{{ __('Emergency Phone') }}</dt>
                            <dd class="font-medium text-neutral-900 dark:text-neutral-100">{{ $user->profile?->emergency_phone ?? '-' }}</dd>
                        </div>
                        <div class="flex justify-between py-2 border-b border-neutral-100 dark:border-neutral-700">
                            <dt class="text-neutral-500 dark:text-neutral-400">{{ __('Emergency Contact Name') }}</dt>
                            <dd class="font-medium text-neutral-900 dark:text-neutral-100">{{ $user->profile?->emergency_contact_name ?? '-' }}</dd>
                        </div>
                        <div class="flex justify-between py-2 border-b border-neutral-100 dark:border-neutral-700">
                            <dt class="text-neutral-500 dark:text-neutral-400">{{ __('Relationship') }}</dt>
                            <dd class="font-medium text-neutral-900 dark:text-neutral-100">{{ $user->profile?->emergency_contact_relationship ?? '-' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
            @endif

            <!-- Enrollments (for students) -->
            @if($user->hasRole('student') && $user->enrollments->count() > 0)
            <div class="rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                <div class="border-b border-neutral-200 px-6 py-4 dark:border-neutral-700">
                    <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Enrollments') }}</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-neutral-50 text-neutral-500 dark:bg-neutral-700 dark:text-neutral-400">
                            <tr>
                                <th class="px-6 py-3">{{ __('Course') }}</th>
                                <th class="px-6 py-3">{{ __('Section') }}</th>
                                <th class="px-6 py-3">{{ __('Status') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                            @foreach($user->enrollments as $enrollment)
                            <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-700/50">
                                <td class="px-6 py-4 font-medium text-neutral-900 dark:text-neutral-100">{{ $enrollment->courseSection?->course?->name }}</td>
                                <td class="px-6 py-4 text-neutral-500 dark:text-neutral-400">{{ $enrollment->courseSection?->section_name }}</td>
                                <td class="px-6 py-4">
                                    @if($enrollment->status == 'active' || $enrollment->status == 'approved')
                                        <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800 dark:bg-green-900 dark:text-green-200">
                                            {{ ucfirst($enrollment->status) }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-neutral-100 px-2.5 py-0.5 text-xs font-medium text-neutral-800 dark:bg-neutral-700 dark:text-neutral-300">
                                            {{ ucfirst($enrollment->status) }}
                                        </span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            <!-- Courses (for teachers) -->
            @if($user->hasRole('teacher') && $user->taughtCourses->count() > 0)
            <div class="rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                <div class="border-b border-neutral-200 px-6 py-4 dark:border-neutral-700">
                    <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Assigned Courses') }}</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-neutral-50 text-neutral-500 dark:bg-neutral-700 dark:text-neutral-400">
                            <tr>
                                <th class="px-6 py-3">{{ __('Course') }}</th>
                                <th class="px-6 py-3">{{ __('Section') }}</th>
                                <th class="px-6 py-3">{{ __('Students') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                            @foreach($user->taughtCourses as $section)
                            <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-700/50">
                                <td class="px-6 py-4 font-medium text-neutral-900 dark:text-neutral-100">{{ $section->course?->name }}</td>
                                <td class="px-6 py-4 text-neutral-500 dark:text-neutral-400">{{ $section->section_name }}</td>
                                <td class="px-6 py-4 text-neutral-500 dark:text-neutral-400">{{ $section->enrollments->where('status', 'approved')->count() }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>
    </div>
</x-layouts::app>
