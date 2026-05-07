<x-layouts::app :title="__('Theme Settings')">
    <div class="mb-8">
        <nav class="flex text-sm text-gray-400">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-white">{{ __('Dashboard') }}</a>
            <span class="mx-2">/</span>
            <a href="{{ route('admin.settings.index') }}" class="hover:text-white">{{ __('Settings') }}</a>
            <span class="mx-2">/</span>
            <span class="text-white">{{ __('Appearance') }}</span>
        </nav>

        <h1 class="mt-4 text-3xl font-bold text-stone-900 dark:text-stone-100">{{ __('Theme Settings') }}</h1>
        <p class="mt-1 text-stone-500 dark:text-stone-400">{{ __('Customize the visual appearance of Noor Alhuda LMS') }}</p>
    </div>

    <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl p-4 mb-6">
        <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div>
                <p class="font-medium text-amber-800 dark:text-amber-200">{{ __('Theme Settings Moved to Navbar') }}</p>
                <p class="text-sm text-amber-700 dark:text-amber-300 mt-1">
                    {{ __('All theme customizations (base themes, accent colors, light/dark mode) are now available in the navbar. Click your profile avatar and select the theme icon (🎨) to access theme settings.') }}
                </p>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.settings.theme.update') }}" class="space-y-6" id="themeForm">
        @csrf
        @method('PUT')

        <!-- Branding -->
        <div class="rounded-xl border border-stone-200 bg-white p-6 dark:border-stone-700 dark:bg-stone-800">
            <flux:heading level="3" size="lg" class="mb-6">{{ __('Branding') }}</flux:heading>

            <div class="grid gap-4 md:grid-cols-2">
                <flux:input name="app_name" :label="__('Application Name')" value="Noor Alhuda LMS" />
                <flux:input name="app_name_ar" :label="__('Application Name (Arabic)')" value="نور نظام إدارة التعلم" />
            </div>

            <div class="mt-4">
                <flux:textarea name="app_description" :label="__('Application Description')" rows="3">
{{ __('Noor LMS is a modern, bilingual Learning Management System designed for educational institutions.') }}
                </flux:textarea>
            </div>
        </div>

        <!-- Logo Upload -->
        <div class="rounded-xl border border-stone-200 bg-white p-6 dark:border-stone-700 dark:bg-stone-800">
            <flux:heading level="3" size="lg" class="mb-6">{{ __('Logo') }}</flux:heading>

            <div class="flex items-center gap-6">
                <div class="flex h-20 w-20 items-center justify-center rounded-xl bg-gradient-to-br from-amber-400 to-orange-500">
                    <span class="text-3xl font-bold text-white">N</span>
                </div>
                <div>
                    <flux:input type="file" name="app_logo" :label="__('Upload Logo')" />
                    <flux:text variant="subtle" class="mt-2">{{ __('Recommended size: 200x200px. PNG, JPG, or SVG supported.') }}</flux:text>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <flux:button variant="primary" type="submit">
                {{ __('Save Settings') }}
            </flux:button>

            @if(session('success'))
                <flux:text class="text-green-600 dark:text-green-400">{{ session('success') }}</flux:text>
            @endif
        </div>
    </form>
</x-layouts::app>
