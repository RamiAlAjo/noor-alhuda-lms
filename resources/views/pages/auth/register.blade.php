<x-layouts::auth>
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Create an account')" :description="__('Enter your details below to create your account')" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('register.store') }}" class="flex flex-col gap-6">
            @csrf

            <!-- Four-Part Name Fields -->
            <div class="grid grid-cols-2 gap-4">
                <!-- First Name -->
                <flux:input
                    name="first_name"
                    :label="__('First Name')"
                    :value="old('first_name')"
                    type="text"
                    required
                    autofocus
                    autocomplete="given-name"
                    :placeholder="__('First Name')"
                />

                <!-- Second Name -->
                <flux:input
                    name="second_name"
                    :label="__('Second Name')"
                    :value="old('second_name')"
                    type="text"
                    autocomplete="additional-name"
                    :placeholder="__('Second Name')"
                />
            </div>

            <div class="grid grid-cols-2 gap-4">
                <!-- Third Name -->
                <flux:input
                    name="third_name"
                    :label="__('Third Name')"
                    :value="old('third_name')"
                    type="text"
                    autocomplete="family-name"
                    :placeholder="__('Third Name')"
                />

                <!-- Last Name -->
                <flux:input
                    name="last_name"
                    :label="__('Last Name')"
                    :value="old('last_name')"
                    type="text"
                    required
                    autocomplete="family-name"
                    :placeholder="__('Last Name')"
                />
            </div>

            <!-- Email Address -->
            <flux:input
                name="email"
                :label="__('Email address')"
                :value="old('email')"
                type="email"
                required
                autocomplete="email"
                placeholder="email@example.com"
            />

            <!-- Password -->
            <flux:input
                name="password"
                :label="__('Password')"
                type="password"
                required
                autocomplete="new-password"
                :placeholder="__('Password')"
                viewable
            />

            <!-- Confirm Password -->
            <flux:input
                name="password_confirmation"
                :label="__('Confirm password')"
                type="password"
                required
                autocomplete="new-password"
                :placeholder="__('Confirm password')"
                viewable
            />

            <div class="flex items-center justify-end">
                <x-button.submit loading-text="{{ __('Creating...') }}" class="w-full" data-test="register-user-button">
                    {{ __('Create account') }}
                </x-button.submit>
            </div>
        </form>

        <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400">
            <span>{{ __('Already have an account?') }}</span>
            <flux:link :href="route('login')" wire:navigate>{{ __('Log in') }}</flux:link>
        </div>
    </div>
</x-layouts::auth>
