<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="{{ session('flux_appearance', 'dark') }}"
    dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}"
    {{ session('high_contrast') ? 'data-high-contrast-mode="true"' : '' }}
    {{ session('large_text') ? 'data-large-text-mode="true"' : '' }}
    {{ session('dyslexia_font') ? 'data-dyslexia-font-mode="true"' : '' }}
    {{ session('reduced_motion') ? 'data-reduced-motion="true"' : '' }}
    {{ session('grayscale') ? 'data-grayscale="true"' : '' }}
    {{ session('line_spacing') ? 'data-line-spacing-mode="true"' : '' }}
    {{ session('focus_outline') ? 'data-focus-outline-mode="true"' : '' }}
    data-font-size="{{ session('font_size', 'medium') }}"
    data-theme="{{ session('accent_color', Cookie::get('accent_color', auth()->user()?->settings?->theme ?? config('settings.accent_color', 'zinc'))) }}"
    data-accent-color="{{ session('accent_color', Cookie::get('accent_color', auth()->user()?->settings?->theme ?? config('settings.accent_color', 'zinc'))) }}"
>
    <head>
        @include('partials.head')
        <script>
            // Apply accent color from session/localStorage
            (function() {
                const savedColor = localStorage.getItem('accent_color') || '{{ session("accent_color", config("settings.accent_color", "zinc")) }}';
                const accentColors = {
                    'red': '#ef4444', 'orange': '#f97316', 'amber': '#f59e0b', 'yellow': '#eab308',
                    'lime': '#84cc16', 'green': '#22c55e', 'emerald': '#10b981', 'teal': '#14b8a6',
                    'cyan': '#06b6d4', 'sky': '#0ea5e9', 'blue': '#3b82f6', 'indigo': '#6366f1',
                    'violet': '#8b5cf6', 'purple': '#a855f7', 'fuchsia': '#d946ef', 'pink': '#ec4899',
                    'rose': '#f43f5e', 'slate': '#64748b', 'gray': '#6b7280', 'zinc': '#71717a',
                    'neutral': '#737373', 'stone': '#78716c'
                };
                if (accentColors[savedColor]) {
                    document.documentElement.style.setProperty('--color-accent', accentColors[savedColor], 'important');
                    document.documentElement.style.setProperty('--color-accent-light', accentColors[savedColor] + '80', 'important');
                    document.documentElement.setAttribute('data-theme', savedColor);
                    document.documentElement.setAttribute('data-accent-color', savedColor);
                }

                // Apply dark mode from system preference if not set
                const savedDarkMode = localStorage.getItem('dark_mode');
                if (savedDarkMode === null && window.matchMedia) {
                    if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
                        document.documentElement.classList.add('dark');
                    }
                }
            })();
        </script>
    </head>
    <body class="min-h-screen bg-neutral-100 antialiased dark:bg-linear-to-b dark:from-neutral-950 dark:to-neutral-900">
        <!-- Skip Link for Accessibility -->
        <a href="#main-content" class="skip-link">{{ __('Skip to main content') }}</a>

        <div class="bg-muted flex min-h-svh flex-col items-center justify-center gap-6 p-6 md:p-10" role="presentation">
            <div class="flex w-full max-w-md flex-col gap-6">
                <a href="{{ route('home') }}" class="flex flex-col items-center gap-2 font-medium" wire:navigate aria-label="{{ __('Go to homepage') }}">
                    <span class="flex h-9 w-9 items-center justify-center rounded-md" aria-hidden="true">
                        <x-app-logo-icon class="size-9 fill-current text-black dark:text-white" />
                    </span>

                    <span class="sr-only">{{ config('app.name', 'Laravel') }}</span>
                </a>

                <main id="main-content" class="flex flex-col gap-6" role="main" aria-label="{{ __('Authentication form') }}">
                    <div class="rounded-xl border bg-white dark:bg-stone-950 dark:border-stone-800 text-stone-800 shadow-xs">
                        <div class="px-10 py-8">{{ $slot }}</div>
                    </div>
                </main>
            </div>
        </div>
        @fluxScripts
    </body>
</html>
