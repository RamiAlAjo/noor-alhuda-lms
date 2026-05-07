{{--
    =============================================================================
    ADMIN USERS INDEX VIEW
    =============================================================================

    Purpose: List all users in the system with search, filter, and bulk actions.

    Route: admin.users.index
    Controller: Admin\UserController@index

    Components:
    - Header with title and action buttons (Import, Add User)
    - Stats cards showing Total Users, Admins, Teachers, Students counts
    - User list with search and role filter dropdown
    - Users table with avatar, name, user ID, email, role, status
    - Action buttons (View, Edit)
    - Pagination
    - Empty state when no users found

    Required Data:
    - $users: Paginated collection of User models

    Dependencies:
    - route('admin.users.import') - Import users page
    - route('admin.users.create') - Create user page
    - route('admin.users.show', $user) - View user details
    - route('admin.users.edit', $user) - Edit user page
    - $user->full_name - User's full name accessor
    - $user->user_id - User's unique identifier
    - $user->hasRole($role) - Check if user has specific role
    - $user->is_active - Check if user account is active

    =============================================================================
--}}
<x-layouts::app :title="__('User Management')">
    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-neutral-900 dark:text-neutral-100">{{ __('User Management') }}</h1>
            <p class="mt-1 text-neutral-500 dark:text-neutral-400">{{ __('Manage all users in your institution') }}</p>
        </div>
        <div class="flex gap-3">
            <flux:button :href="route('admin.users.import')" variant="ghost">
                <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                </svg>
                {{ __('Import') }}
            </flux:button>
            <flux:button :href="route('admin.users.create')" variant="primary">
                <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                </svg>
                {{ __('Add User') }}
            </flux:button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="mb-6 grid gap-4 md:grid-cols-4">
        <div class="rounded-xl border border-neutral-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100 text-blue-600 dark:bg-blue-900 dark:text-blue-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Total Users') }}</p>
                    <p class="text-xl font-bold text-neutral-900 dark:text-neutral-100">{{ $users->total() }}</p>
                </div>
            </div>
        </div>
        <div class="rounded-xl border border-neutral-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-green-100 text-green-600 dark:bg-green-900 dark:text-green-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Admins') }}</p>
                    <p class="text-xl font-bold text-neutral-900 dark:text-neutral-100">{{ $users->filter(fn($u) => $u->hasRole('admin'))->count() }}</p>
                </div>
            </div>
        </div>
        <div class="rounded-xl border border-neutral-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-purple-100 text-purple-600 dark:bg-purple-900 dark:text-purple-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Teachers') }}</p>
                    <p class="text-xl font-bold text-neutral-900 dark:text-neutral-100">{{ $users->filter(fn($u) => $u->hasRole('teacher'))->count() }}</p>
                </div>
            </div>
        </div>
        <div class="rounded-xl border border-neutral-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-orange-100 text-orange-600 dark:bg-orange-900 dark:text-orange-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Students') }}</p>
                    <p class="text-xl font-bold text-neutral-900 dark:text-neutral-100">{{ $users->filter(fn($u) => $u->hasRole('student'))->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- User List -->
    <div class="rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
        <div class="border-b border-neutral-200 px-6 py-4 dark:border-neutral-700">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('All Users') }}</h2>
                <!-- Search & Filters -->
                <div class="flex gap-3">
                    <select class="rounded-lg border border-neutral-300 py-2 pl-3 pr-8 text-sm focus:border-indigo-500 focus:outline-none dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-100">
                        <option value="">{{ __('All Roles') }}</option>
                        <option value="admin">{{ __('Admin') }}</option>
                        <option value="teacher">{{ __('Teacher') }}</option>
                        <option value="student">{{ __('Student') }}</option>
                    </select>
                    <div class="relative">
                        <input type="text" placeholder="{{ __('Search users...') }}" class="w-64 rounded-lg border border-neutral-300 py-2 pl-10 pr-4 text-sm focus:border-indigo-500 focus:outline-none dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-100">
                        <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-2.5 size-4 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-neutral-50 text-neutral-500 dark:bg-neutral-700 dark:text-neutral-400">
                    <tr>
                        <th class="px-6 py-3">{{ __('User') }}</th>
                        <th class="px-6 py-3">{{ __('User ID') }}</th>
                        <th class="px-6 py-3">{{ __('Email') }}</th>
                        <th class="px-6 py-3">{{ __('Role') }}</th>
                        <th class="px-6 py-3">{{ __('Status') }}</th>
                        <th class="px-6 py-3 text-right">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                    @forelse($users as $user)
                    <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-700/50">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 text-sm font-bold text-white">
                                    {{ substr($user->first_name ?? 'U', 0, 1) }}{{ substr($user->last_name ?? '', 0, 1) }}
                                </div>
                                <div>
                                    <p class="font-medium text-neutral-900 dark:text-neutral-100">{{ $user->full_name }}</p>
                                    <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ $user->phone ?? 'No phone' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="font-mono text-sm text-indigo-600 dark:text-indigo-400">{{ $user->user_id }}</span>
                        </td>
                        <td class="px-6 py-4 text-neutral-600 dark:text-neutral-400">{{ $user->email }}</td>
                        <td class="px-6 py-4">
                            @if($user->hasRole('admin'))
                            <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800 dark:bg-red-900 dark:text-red-200">
                                {{ __('Admin') }}
                            </span>
                            @elseif($user->hasRole('teacher'))
                            <span class="inline-flex items-center rounded-full bg-purple-100 px-2.5 py-0.5 text-xs font-medium text-purple-800 dark:bg-purple-900 dark:text-purple-200">
                                {{ __('Teacher') }}
                            </span>
                            @else
                            <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                {{ __('Student') }}
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($user->is_active)
                            <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800 dark:bg-green-900 dark:text-green-200">
                                {{ __('Active') }}
                            </span>
                            @else
                            <span class="inline-flex items-center rounded-full bg-neutral-100 px-2.5 py-0.5 text-xs font-medium text-neutral-800 dark:bg-neutral-700 dark:text-neutral-200">
                                {{ __('Inactive') }}
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <flux:button size="sm" variant="subtle" :href="route('admin.users.show', $user)">
                                    {{ __('View') }}
                                </flux:button>
                                <flux:button size="sm" variant="ghost" :href="route('admin.users.edit', $user)">
                                    {{ __('Edit') }}
                                </flux:button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-neutral-100 dark:bg-neutral-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="size-8 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                </div>
                                <h3 class="text-lg font-medium text-neutral-900 dark:text-neutral-100">{{ __('No users found') }}</h3>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
        <div class="border-t border-neutral-200 px-6 py-4 dark:border-neutral-700">
            {{ $users->links() }}
        </div>
        @endif
    </div>
</x-layouts::app>
