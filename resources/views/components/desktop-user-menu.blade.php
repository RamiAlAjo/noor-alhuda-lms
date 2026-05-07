@props([
    'showOnlineStatus' => true,
])

<flux:dropdown position="bottom" align="start">
    <flux:sidebar.profile
        :name="auth()->user()->name"
        :initials="auth()->user()->initials()"
        icon:trailing="chevron-up-down"
        data-test="sidebar-menu-button"
        class="hover-lift transition-all duration-300"
        aria-label="{{ __('User menu') }}"
        aria-haspopup="true"
    />

    <flux:menu class="p-2 min-w-72" role="menu" aria-label="{{ __('User navigation') }}">
        <!-- User Card -->
        <div class="flex items-center gap-3 px-3 py-4 text-start rounded-xl bg-gradient-to-r from-indigo-50 to-purple-50 dark:from-indigo-900/30 dark:to-purple-900/30 mb-2 border border-indigo-100 dark:border-indigo-800" role="presentation">
            <div class="relative">
                <flux:avatar
                    :name="auth()->user()->name"
                    :initials="auth()->user()->initials()"
                    class="ring-2 ring-indigo-500 ring-offset-2 dark:ring-offset-zinc-900"
                    aria-hidden="true"
                />
                @if($showOnlineStatus)
                    <div
                        class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 bg-green-500 rounded-full border-2 border-white dark:border-zinc-800"
                        aria-label="{{ __('Online') }}"
                        title="{{ __('Online') }}"
                    ></div>
                @endif
            </div>
            <div class="grid flex-1 text-start text-sm leading-tight overflow-hidden">
                <flux:heading class="truncate font-semibold text-neutral-900 dark:text-neutral-100">
                    {{ auth()->user()->name }}
                </flux:heading>
                <flux:text class="truncate text-xs text-zinc-500 dark:text-zinc-400">
                    {{ auth()->user()->email }}
                </flux:text>
                @if(auth()->user()->profile)
                    <flux:text class="truncate text-xs text-indigo-600 dark:text-indigo-400 font-medium">
                        {{ auth()->user()->roles->first()?->name ?? 'User' }}
                    </flux:text>
                @endif
            </div>
        </div>

        <flux:menu.separator class="my-2" aria-hidden="true" />

        <!-- Quick Links -->
        <flux:menu.radio.group role="group" aria-label="{{ __('Account options') }}">
            <div class="px-2 py-1">
                <flux:menu.item
                    :href="route('profile.edit')"
                    icon="user"
                    wire:navigate
                    class="rounded-lg hover:bg-indigo-50 dark:hover:bg-indigo-900/30 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-zinc-800"
                    role="menuitem"
                >
                    {{ __('My Profile') }}
                </flux:menu.item>
            </div>
        </flux:menu.radio.group>

        <flux:menu.separator class="my-2" aria-hidden="true" />

        <!-- Logout -->
        <form method="POST" action="{{ route('logout') }}" class="w-full px-2" role="presentation">
            @csrf
            <flux:menu.item
                as="button"
                type="submit"
                class="w-full cursor-pointer rounded-lg hover:bg-red-50 dark:hover:bg-red-900/30 text-red-600 dark:text-red-400 focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-zinc-800"
                data-test="logout-button"
                icon="arrow-left-start-on-rectangle"
                role="menuitem"
            >
                {{ __('Log Out') }}
            </flux:menu.item>
        </form>
    </flux:menu>
</flux:dropdown>
