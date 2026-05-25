<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="{{ session('flux_appearance', 'dark') }}"
    dir="{{ in_array(app()->getLocale(), ['ar', 'fa', 'ku']) ? 'rtl' : 'ltr' }}"
    {{ session('high_contrast') ? 'data-high-contrast-mode="true"' : '' }}
    {{ session('large_text') ? 'data-large-text-mode="true"' : '' }}
    {{ session('dyslexia_font') ? 'data-dyslexia-font-mode="true"' : '' }}
    {{ session('reduced_motion') ? 'data-reduced-motion="true"' : '' }}
    {{ session('grayscale') ? 'data-grayscale="true"' : '' }}
    {{ session('line_spacing') ? 'data-line-spacing-mode="true"' : '' }}
    {{ session('focus_outline') ? 'data-focus-outline-mode="true"' : '' }}
    data-font-size="{{ session('font_size', 'medium') }}"
    data-theme="{{ session('accent_color', Cookie::get('accent_color', auth()->user()?->settings?->theme ?? config('settings.accent_color', 'zinc'))) }}"
>
    <head>
        @include('partials.head')

        <!-- =====================================================
             SECTION 1: THEME & ACCESSIBILITY INITIALIZATION
             Used by: All pages using app.blade.php layout
             Description: Critical JavaScript that runs immediately in head
             to prevent flash of unstyled content
        ===================================================== -->
        <script>
        // Critical Theme Initialization - Runs BEFORE body renders
        (function() {
            // Define accent colors mapping
            const accentColors = {
                'red': '#ef4444', 'orange': '#f97316', 'amber': '#f59e0b', 'yellow': '#eab308',
                'lime': '#84cc16', 'green': '#22c55e', 'emerald': '#10b981', 'teal': '#14b8a6',
                'cyan': '#06b6d4', 'sky': '#0ea5e9', 'blue': '#3b82f6', 'indigo': '#6366f1',
                'violet': '#8b5cf6', 'purple': '#a855f7', 'fuchsia': '#d946ef', 'pink': '#ec4899',
                'rose': '#f43f5e', 'slate': '#64748b', 'gray': '#6b7280', 'zinc': '#71717a',
                'neutral': '#737373', 'stone': '#78716c', 'base': '#6366f1'
            };

            const htmlElement = document.documentElement;

            // Get saved preferences in priority order
            let savedColor = localStorage.getItem('accent_color');
            if (!savedColor) {
                const cookieMatch = document.cookie.match(/accent_color=([^;]+)/);
                if (cookieMatch) savedColor = cookieMatch[1];
            }
            if (!savedColor) savedColor = htmlElement.getAttribute('data-theme');
            if (!savedColor || !accentColors[savedColor]) savedColor = 'zinc';

            // Apply immediately to prevent flash
            const hexColor = accentColors[savedColor];
            htmlElement.style.setProperty('--color-accent', hexColor, 'important');
            htmlElement.style.setProperty('--color-accent-light', hexColor + '80', 'important');

            // Dark mode
            const savedDarkMode = localStorage.getItem('dark_mode');
            const systemThemeDetection = localStorage.getItem('system_theme_detection') !== 'false';

            if (savedDarkMode === 'true') {
                htmlElement.classList.add('dark');
            } else if (savedDarkMode === 'false') {
                htmlElement.classList.remove('dark');
            } else if (systemThemeDetection && window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
                htmlElement.classList.add('dark');
            }

            // Load accessibility settings
            const accessibilitySettings = ['high_contrast', 'large_text', 'dyslexia_font', 'reduced_motion', 'grayscale', 'line_spacing', 'focus_outline', 'font_size'];
            accessibilitySettings.forEach(function(setting) {
                const savedValue = localStorage.getItem('accessibility_' + setting);
                if (savedValue !== null) {
                    if (setting === 'font_size') {
                        htmlElement.setAttribute('data-font-size', savedValue);
                    } else if (savedValue === 'true') {
                        htmlElement.setAttribute('data-' + setting.replace('_', '-') + '-mode', 'true');
                    }
                }
            });

            // Listen for system theme changes
            if (systemThemeDetection && window.matchMedia) {
                window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function(e) {
                    if (localStorage.getItem('dark_mode') === null) {
                        htmlElement.classList.toggle('dark', e.matches);
                    }
                });
            }
        })();
        </script>

        <!-- Global Accessibility Live Update Handler -->
        <script>
            // Listen for accessibility changes from the settings page (or any Livewire component)
            // This makes changes take effect instantly across the current page without reload
            document.addEventListener('livewire:init', () => {
                Livewire.on('appearance-updated', (payload) => {
                    if (!payload || typeof payload !== 'object') return;

                    const html = document.documentElement;

                    // Map of setting => how to apply it
                    const settingsToApply = {
                        'high_contrast': (val) => {
                            localStorage.setItem('accessibility_high_contrast', val ? 'true' : 'false');
                            if (val) {
                                html.setAttribute('data-high-contrast-mode', 'true');
                                html.classList.add('high-contrast');
                            } else {
                                html.removeAttribute('data-high-contrast-mode');
                                html.classList.remove('high-contrast');
                            }
                        },
                        'large_text': (val) => {
                            localStorage.setItem('accessibility_large_text', val ? 'true' : 'false');
                            if (val) {
                                html.setAttribute('data-large-text-mode', 'true');
                                html.classList.add('large-text');
                            } else {
                                html.removeAttribute('data-large-text-mode');
                                html.classList.remove('large-text');
                            }
                        },
                        'dyslexia_font': (val) => {
                            localStorage.setItem('accessibility_dyslexia_font', val ? 'true' : 'false');
                            if (val) {
                                html.setAttribute('data-dyslexia-font-mode', 'true');
                                html.classList.add('dyslexia-font');
                            } else {
                                html.removeAttribute('data-dyslexia-font-mode');
                                html.classList.remove('dyslexia-font');
                            }
                        },
                        'reduced_motion': (val) => {
                            localStorage.setItem('accessibility_reduced_motion', val ? 'true' : 'false');
                            if (val) {
                                html.setAttribute('data-reduced-motion-mode', 'true');
                                html.classList.add('reduced-motion');
                            } else {
                                html.removeAttribute('data-reduced-motion-mode');
                                html.classList.remove('reduced-motion');
                            }
                        },
                        'grayscale': (val) => {
                            localStorage.setItem('accessibility_grayscale', val ? 'true' : 'false');
                            if (val) {
                                html.setAttribute('data-grayscale-mode', 'true');
                                html.classList.add('grayscale');
                            } else {
                                html.removeAttribute('data-grayscale-mode');
                                html.classList.remove('grayscale');
                            }
                        },
                        'strong_focus_outline': (val) => {
                            localStorage.setItem('accessibility_focus_outline', val ? 'true' : 'false');
                            if (val) {
                                html.setAttribute('data-focus-outline-mode', 'true');
                            } else {
                                html.removeAttribute('data-focus-outline-mode');
                            }
                        },
                        'line_spacing': (val) => {
                            localStorage.setItem('accessibility_line_spacing', val);
                            // The CSS uses data-line-spacing-mode for the boolean "increased" state
                            // For numeric, we can set a custom property or attribute
                            html.style.setProperty('--user-line-spacing', val);
                            if (parseFloat(val) > 1.5) {
                                html.setAttribute('data-line-spacing-mode', 'true');
                            } else {
                                html.removeAttribute('data-line-spacing-mode');
                            }
                        },
                        'font_size': (val) => {
                            localStorage.setItem('accessibility_font_size', val);
                            // Map numeric to the data-font-size values used in CSS
                            let sizeKey = 'medium';
                            const num = parseInt(val);
                            if (num <= 14) sizeKey = 'small';
                            else if (num >= 18 && num < 20) sizeKey = 'large';
                            else if (num >= 20) sizeKey = 'xlarge';
                            html.setAttribute('data-font-size', sizeKey);
                        }
                    };

                    // Apply each setting that was sent in the payload
                    Object.keys(settingsToApply).forEach(key => {
                        if (key in payload) {
                            settingsToApply[key](payload[key]);
                        }
                    });

                    // Optional: flash a small toast or just let the styles apply
                    console.log('Accessibility settings applied live from settings page');
                });
            });
        </script>

        <!-- Theme and accessibility styles are now loaded from external CSS files -->
    </head>
    <body class="min-h-screen bg-zinc-50 dark:bg-zinc-950">
        <!-- Skeleton Loading Overlay - Shows while theme initializes -->
        <div id="skeleton-loader" class="fixed inset-0 z-[9999] bg-white dark:bg-zinc-900">
            <div class="flex h-screen">
                <!-- Sidebar skeleton -->
                <div class="w-64 bg-zinc-50 dark:bg-zinc-800/50 border-e border-zinc-200 dark:border-zinc-700 p-4 hidden lg:block">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500/20 to-purple-500/20 dark:from-indigo-500/10 dark:to-purple-500/10 animate-pulse"></div>
                        <div class="h-4 w-32 bg-zinc-200 dark:bg-zinc-700 rounded-full animate-pulse"></div>
                    </div>
                    <div class="space-y-3">
                        <div class="h-3 w-16 bg-zinc-200 dark:bg-zinc-700 rounded-full animate-pulse"></div>
                        <div class="space-y-2">
                            <div class="h-9 w-full bg-zinc-100 dark:bg-zinc-700/50 rounded-lg animate-pulse"></div>
                            <div class="h-9 w-full bg-zinc-100 dark:bg-zinc-700/50 rounded-lg animate-pulse"></div>
                            <div class="h-9 w-full bg-zinc-100 dark:bg-zinc-700/50 rounded-lg animate-pulse"></div>
                            <div class="h-9 w-full bg-zinc-100 dark:bg-zinc-700/50 rounded-lg animate-pulse"></div>
                        </div>
                    </div>
                </div>
                <!-- Main content skeleton -->
                <div class="flex-1 flex flex-col">
                    <!-- Header skeleton -->
                    <div class="h-16 border-b border-zinc-200 dark:border-zinc-700 bg-white/80 dark:bg-zinc-900/80 backdrop-blur-sm px-6 flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-8 h-8 bg-zinc-100 dark:bg-zinc-700 rounded-lg animate-pulse"></div>
                            <div class="h-5 w-28 bg-zinc-200 dark:bg-zinc-700 rounded-full animate-pulse"></div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 bg-zinc-100 dark:bg-zinc-700 rounded-lg animate-pulse"></div>
                            <div class="w-9 h-9 bg-zinc-100 dark:bg-zinc-700 rounded-lg animate-pulse"></div>
                            <div class="w-9 h-9 bg-zinc-100 dark:bg-zinc-700 rounded-lg animate-pulse"></div>
                            <div class="w-9 h-9 bg-zinc-100 dark:bg-zinc-700 rounded-full animate-pulse"></div>
                        </div>
                    </div>
                    <!-- Content skeleton -->
                    <div class="flex-1 p-6 overflow-hidden">
                        <div class="max-w-7xl mx-auto space-y-5">
                            <div class="h-7 w-36 bg-zinc-200 dark:bg-zinc-700 rounded-lg animate-pulse"></div>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                <div class="h-28 bg-zinc-100 dark:bg-zinc-700/50 rounded-xl animate-pulse"></div>
                                <div class="h-28 bg-zinc-100 dark:bg-zinc-700/50 rounded-xl animate-pulse"></div>
                                <div class="h-28 bg-zinc-100 dark:bg-zinc-700/50 rounded-xl animate-pulse hidden lg:block"></div>
                            </div>
                            <div class="h-56 bg-zinc-100 dark:bg-zinc-700/50 rounded-xl animate-pulse"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script>
            // Hide skeleton loader once everything is initialized
            (function() {
                function hideSkeleton() {
                    var skeleton = document.getElementById('skeleton-loader');
                    if (skeleton) {
                        skeleton.style.opacity = '0';
                        setTimeout(function() {
                            skeleton.remove();
                        }, 300);
                    }
                }

                // Wait for DOM ready and a brief delay for fonts/scripts
                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', function() {
                        // Wait for fonts to load
                        if (document.fonts && document.fonts.ready) {
                            document.fonts.ready.then(function() {
                                setTimeout(hideSkeleton, 50);
                            });
                        } else {
                            setTimeout(hideSkeleton, 150);
                        }
                    });
                } else {
                    if (document.fonts && document.fonts.ready) {
                        document.fonts.ready.then(function() {
                            setTimeout(hideSkeleton, 50);
                        });
                    } else {
                        setTimeout(hideSkeleton, 150);
                    }
                }
            })();
        </script>

        <!-- Skip Navigation Link for Accessibility -->
        <x-skip-link />

        <!-- Live Region for Announcements -->
        <div id="live-region" class="sr-only" aria-live="polite" aria-atomic="true"></div>

        <div class="flex min-h-screen">
            <!-- Include Enhanced Sidebar Component -->
            @include('layouts.app.sidebar')

            <!-- Main Content Area -->
            <div class="flex flex-1 flex-col min-w-0">
                <!-- Header -->
                <flux:header container class="
                    border-b border-zinc-200/50
                    dark:border-zinc-700/50
                    bg-white/80
                    dark:bg-zinc-900/80
                    backdrop-blur-md
                    sticky
                    top-0
                    z-40
                " role="banner">
                    <!-- Mobile Sidebar Toggle -->
                    <flux:sidebar.toggle
                        class="lg:hidden"
                        icon="bars-3"
                        inset="left"
                        aria-label="{{ __('Open sidebar menu') }}"
                    />

                    <!-- Logo -->
                    <x-app-logo href="{{ route('dashboard') }}" wire:navigate aria-label="{{ __('Go to dashboard') }}" />

                    <!-- Desktop Navigation -->
                    <flux:navbar class="-mb-px max-lg:hidden">
                        <flux:navbar.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                            {{ __('Dashboard') }}
                        </flux:navbar.item>
                    </flux:navbar>

                    <flux:spacer />

                    <!-- Navbar Actions -->
                    <flux:navbar class="me-1.5 space-x-0.5 rtl:space-x-reverse py-0!">
                        <!-- Accessibility Settings Dropdown -->
                        <flux:dropdown position="bottom" align="end">
                            <flux:tooltip :content="__('Accessibility')" position="bottom">
                                <flux:navbar.item class="!h-10 navbar-icon-btn [&>div>svg]:size-5 relative" icon="eye" icon-trailing="chevron-down" :label="__('Accessibility')">
                                    @if(session('high_contrast') || session('large_text') || session('dyslexia_font') || session('reduced_motion') || session('grayscale') || session('line_spacing') || session('focus_outline'))
                                        <span class="absolute top-1 rtl:left-1 ltr:right-1 w-2 h-2 bg-green-500 rounded-full"></span>
                                    @endif
                                </flux:navbar.item>
                            </flux:tooltip>

                            <flux:menu class="w-72">
                                <div class="px-4 py-3 border-b border-zinc-200 dark:border-zinc-700 bg-gradient-to-r from-[var(--color-accent)]/5 to-transparent">
                                    <p class="font-semibold text-sm">{{ __('Accessibility') }}</p>
                                    <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('Customize your experience') }}</p>
                                </div>

                                <!-- High Contrast -->
                                <form method="POST" action="{{ route('settings.accessibility.toggle') }}">
                                    @csrf
                                    <input type="hidden" name="high_contrast" value="{{ session('high_contrast') ? 'false' : 'true' }}">
                                    <button type="submit" class="w-full px-4 py-2.5 text-start flex items-center justify-between hover:bg-zinc-50 dark:hover:bg-zinc-800">
                                        <span class="flex items-center gap-3">
                                            <span class="w-8 h-8 rounded-lg bg-zinc-100 dark:bg-zinc-700 flex items-center justify-center">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            </span>
                                            <span class="font-medium">{{ __('High Contrast') }}</span>
                                        </span>
                                        @if(session('high_contrast'))
                                            <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                        @endif
                                    </button>
                                </form>

                                <!-- Large Text -->
                                <form method="POST" action="{{ route('settings.accessibility.toggle') }}">
                                    @csrf
                                    <input type="hidden" name="large_text" value="{{ session('large_text') ? 'false' : 'true' }}">
                                    <button type="submit" class="w-full px-4 py-2.5 text-start flex items-center justify-between hover:bg-zinc-50 dark:hover:bg-zinc-800">
                                        <span class="flex items-center gap-3">
                                            <span class="w-8 h-8 rounded-lg bg-zinc-100 dark:bg-zinc-700 flex items-center justify-center text-lg font-bold">A</span>
                                            <span class="font-medium">{{ __('Large Text') }}</span>
                                        </span>
                                        @if(session('large_text'))
                                            <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                        @endif
                                    </button>
                                </form>

                                <!-- Dyslexia Font -->
                                <form method="POST" action="{{ route('settings.accessibility.toggle') }}">
                                    @csrf
                                    <input type="hidden" name="dyslexia_font" value="{{ session('dyslexia_font') ? 'false' : 'true' }}">
                                    <button type="submit" class="w-full px-4 py-2.5 text-start flex items-center justify-between hover:bg-zinc-50 dark:hover:bg-zinc-800">
                                        <span class="flex items-center gap-3">
                                            <span class="w-8 h-8 rounded-lg bg-zinc-100 dark:bg-zinc-700 flex items-center justify-center font-serif">Aa</span>
                                            <span class="font-medium">{{ __('Dyslexia Font') }}</span>
                                        </span>
                                        @if(session('dyslexia_font'))
                                            <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                        @endif
                                    </button>
                                </form>

                                <!-- Reduced Motion -->
                                <form method="POST" action="{{ route('settings.accessibility.toggle') }}">
                                    @csrf
                                    <input type="hidden" name="reduced_motion" value="{{ session('reduced_motion') ? 'false' : 'true' }}">
                                    <button type="submit" class="w-full px-4 py-2.5 text-start flex items-center justify-between hover:bg-zinc-50 dark:hover:bg-zinc-800">
                                        <span class="flex items-center gap-3">
                                            <span class="w-8 h-8 rounded-lg bg-zinc-100 dark:bg-zinc-700 flex items-center justify-center">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/></svg>
                                            </span>
                                            <span class="font-medium">{{ __('Reduced Motion') }}</span>
                                        </span>
                                        @if(session('reduced_motion'))
                                            <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                        @endif
                                    </button>
                                </form>

                                <div class="border-t border-zinc-200 dark:border-zinc-700 my-1"></div>

                                <!-- Grayscale -->
                                <form method="POST" action="{{ route('settings.accessibility.toggle') }}">
                                    @csrf
                                    <input type="hidden" name="grayscale" value="{{ session('grayscale') ? 'false' : 'true' }}">
                                    <button type="submit" class="w-full px-4 py-2.5 text-start flex items-center justify-between hover:bg-zinc-50 dark:hover:bg-zinc-800">
                                        <span class="flex items-center gap-3">
                                            <span class="w-8 h-8 rounded-lg bg-zinc-100 dark:bg-zinc-700 flex items-center justify-center">
                                                <svg class="w-4 h-4 grayscale" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            </span>
                                            <span class="font-medium">{{ __('Grayscale') }}</span>
                                        </span>
                                        @if(session('grayscale'))
                                            <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                        @endif
                                    </button>
                                </form>

                                <!-- Line Spacing -->
                                <form method="POST" action="{{ route('settings.accessibility.toggle') }}">
                                    @csrf
                                    <input type="hidden" name="line_spacing" value="{{ session('line_spacing') ? 'false' : 'true' }}">
                                    <button type="submit" class="w-full px-4 py-2.5 text-start flex items-center justify-between hover:bg-zinc-50 dark:hover:bg-zinc-800">
                                        <span class="flex items-center gap-3">
                                            <span class="w-8 h-8 rounded-lg bg-zinc-100 dark:bg-zinc-700 flex items-center justify-center">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h8m-8 6h16"/></svg>
                                            </span>
                                            <span class="font-medium">{{ __('Line Spacing') }}</span>
                                        </span>
                                        @if(session('line_spacing'))
                                            <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                        @endif
                                    </button>
                                </form>

                                <!-- Focus Outline -->
                                <form method="POST" action="{{ route('settings.accessibility.toggle') }}">
                                    @csrf
                                    <input type="hidden" name="focus_outline" value="{{ session('focus_outline') ? 'false' : 'true' }}">
                                    <button type="submit" class="w-full px-4 py-2.5 text-start flex items-center justify-between hover:bg-zinc-50 dark:hover:bg-zinc-800">
                                        <span class="flex items-center gap-3">
                                            <span class="w-8 h-8 rounded-lg bg-zinc-100 dark:bg-zinc-700 flex items-center justify-center">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5"/></svg>
                                            </span>
                                            <span class="font-medium">{{ __('Focus Outline') }}</span>
                                        </span>
                                        @if(session('focus_outline'))
                                            <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                        @endif
                                    </button>
                                </form>

                                <div class="border-t border-zinc-200 dark:border-zinc-700 my-1"></div>

                                <!-- Reset All -->
                                <form method="POST" action="{{ route('settings.accessibility.reset') }}">
                                    @csrf
                                    <button type="submit" class="w-full px-4 py-2.5 text-start flex items-center gap-3 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20">
                                        <span class="w-8 h-8 rounded-lg bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                        </span>
                                        <span class="font-medium">{{ __('Reset All') }}</span>
                                    </button>
                                </form>
                            </flux:menu>
                        </flux:dropdown>

                        <!-- Language Switcher -->
                        <flux:dropdown position="bottom" align="end">
                            <flux:tooltip :content="__('Language')" position="bottom">
                                <flux:navbar.item class="!h-10 navbar-icon-btn [&>div>svg]:size-5" icon="globe-alt" icon-trailing="chevron-down" :label="__('Language')" />
                            </flux:tooltip>

                            <flux:menu class="w-64">
                                <div class="px-4 py-3 border-b border-zinc-200 dark:border-zinc-700 bg-gradient-to-r from-[var(--color-accent)]/5 to-transparent">
                                    <p class="font-semibold text-sm">{{ __('Language') }}</p>
                                    <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('Switch language') }}</p>
                                </div>
                                @php
                                $languages = [
                                    'en' => ['name' => 'English', 'native' => 'English'],
                                    'ar' => ['name' => 'Arabic', 'native' => 'العربية'],
                                    'fr' => ['name' => 'French', 'native' => 'Français'],
                                    'tr' => ['name' => 'Turkish', 'native' => 'Türkçe'],
                                    'zh' => ['name' => 'Chinese', 'native' => '中文'],
                                    'fa' => ['name' => 'Farsi', 'native' => 'فارسی'],
                                    'id' => ['name' => 'Indonesian', 'native' => 'Bahasa Indonesia'],
                                    'ku' => ['name' => 'Kurdish', 'native' => 'کوردی'],
                                    'hy' => ['name' => 'Armenian', 'native' => 'Հայերեն'],
                                ];
                                @endphp
                                @foreach($languages as $code => $lang)
                                    <a href="{{ route('language.switch', $code) }}" class="flex items-center gap-3 px-4 py-2.5 hover:bg-zinc-50 dark:hover:bg-zinc-800 {{ app()->getLocale() == $code ? 'bg-[var(--color-accent)]/10' : '' }}">
                                        <div class="w-8 h-8 rounded-lg bg-zinc-100 dark:bg-zinc-700 flex items-center justify-center text-sm font-bold uppercase">
                                            {{ $code }}
                                        </div>
                                        <div class="flex-1">
                                            <p class="font-medium text-sm">{{ $lang['native'] }}</p>
                                            <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ $lang['name'] }}</p>
                                        </div>
                                        @if(app()->getLocale() == $code)
                                            <svg class="w-5 h-5 text-[var(--color-accent)]" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                        @endif
                                    </a>
                                @endforeach
                            </flux:menu>
                        </flux:dropdown>

                        <!-- Theme Switcher -->
                        <!-- =====================================================
                             THEME SWITCHER DROPDOWN
                             Used by: All pages (navbar)
                             Description: Dropdown with base theme selection,
                             accent color picker, and light/dark/system mode
                        ===================================================== -->
                        <flux:dropdown position="bottom" align="end">
                            <flux:tooltip :content="__('Theme')" position="bottom">
                                <flux:navbar.item class="!h-10 navbar-icon-btn [&>div>svg]:size-5 relative" icon="swatch" icon-trailing="chevron-down" :label="__('Theme')">
                                    <span class="absolute top-1.5 rtl:left-1 ltr:right-1 w-2 h-2 rounded-full border border-white dark:border-zinc-800" id="themeIndicator" style="background-color: var(--color-accent);"></span>
                                </flux:navbar.item>
                            </flux:tooltip>

                            <flux:menu class="w-80">
                                <div class="px-4 py-3 border-b border-zinc-200 dark:border-zinc-700 bg-gradient-to-r from-[var(--color-accent)]/5 to-transparent">
                                    <p class="font-bold text-sm text-zinc-800 dark:text-zinc-100">{{ __('Theme Settings') }}</p>
                                    <p class="text-xs text-zinc-600 dark:text-zinc-400 mt-0.5">{{ __('Customize your appearance') }}</p>
                                </div>

                                <!-- Base Theme Selection -->
                                <div class="px-4 py-3 border-b border-zinc-200 dark:border-zinc-700">
                                    <p class="text-xs font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-2">{{ __('Base Theme') }}</p>
                                    <div class="grid grid-cols-4 gap-2">
                                        @php
                                        $baseThemes = [
                                            'default-light' => ['name' => 'Light', 'icon' => '☀️'],
                                            'default-dark' => ['name' => 'Dark', 'icon' => '🌙'],
                                            'cyberpunk-neon' => ['name' => 'Cyber', 'icon' => '⚡'],
                                            'cyberpunk-dark' => ['name' => 'CyberD', 'icon' => '🔵'],
                                            'synthwave' => ['name' => 'Wave', 'icon' => '🌊'],
                                            'midnight' => ['name' => 'Mid', 'icon' => '✨'],
                                            'dracula' => ['name' => 'Drac', 'icon' => '🧛'],
                                            'nord' => ['name' => 'Nord', 'icon' => '❄️'],
                                        ];
                                        @endphp
                                        @foreach($baseThemes as $key => $theme)
                                            <button type="button"
                                                onclick="setBaseTheme('{{ $key }}')"
                                                class="base-theme-btn p-2 rounded-lg border-2 border-zinc-300 dark:border-zinc-600 hover:border-zinc-500 dark:hover:border-zinc-400 transition-all text-center bg-white dark:bg-zinc-800"
                                                title="{{ $theme['name'] }}">
                                                <div class="text-lg">{{ $theme['icon'] }}</div>
                                                <div class="text-xs font-medium mt-1 text-zinc-700 dark:text-zinc-300">{{ $theme['name'] }}</div>
                                            </button>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Accent Colors -->
                                <div class="px-4 py-3 border-b border-zinc-200 dark:border-zinc-700">
                                    <p class="text-xs font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-2">{{ __('Accent Color') }}</p>
                                    <div class="grid grid-cols-4 sm:grid-cols-6 gap-2 justify-items-center">
                                        @php
                                        $themes = [
                                            'base' => '#6366f1',
                                            'red' => '#ef4444',
                                            'orange' => '#f97316',
                                            'amber' => '#f59e0b',
                                            'yellow' => '#eab308',
                                            'lime' => '#84cc16',
                                            'green' => '#22c55e',
                                            'emerald' => '#10b981',
                                            'teal' => '#14b8a6',
                                            'cyan' => '#06b6d4',
                                            'sky' => '#0ea5e9',
                                            'blue' => '#3b82f6',
                                            'indigo' => '#6366f1',
                                            'violet' => '#8b5cf6',
                                            'purple' => '#a855f7',
                                            'fuchsia' => '#d946ef',
                                            'pink' => '#ec4899',
                                            'rose' => '#f43f5e',
                                        ];
                                        @endphp
                                        @foreach($themes as $name => $hex)
                                            @php
                                            $isSelected = session('accent_color', 'zinc') == $name;
                                            @endphp
                                             <button type="button"
                                                 onclick="setThemeColor('{{ $name }}', '{{ $hex }}')"
                                                 class="theme-btn w-8 h-8 rounded-full border-2 border-white dark:border-zinc-800 {{ $isSelected ? 'ring-2 ring-offset-2 accent-selected' : 'hover:ring-1 hover:ring-offset-1 hover:ring-zinc-300 dark:hover:ring-zinc-600' }} hover:scale-110 transition-all duration-200"
                                                 style="background-color: {{ $hex }}; {{ $isSelected ? 'box-shadow: 0 0 0 2px ' . $hex . ', 0 0 0 6px rgba(0, 0, 0, 0.1);' : '' }}"
                                                 data-accent-color="{{ $hex }}"
                                                 title="{{ ucfirst($name) }}">
                                             </button>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Appearance Mode -->
                                <div class="px-4 py-3">
                                    <p class="text-xs font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-2">{{ __('Appearance') }}</p>
                                    <div class="flex gap-2">
                                        <button type="button" onclick="setAppearance('light')" class="flex-1 py-2.5 px-3 rounded-lg text-sm font-semibold transition-colors {{ session('flux_appearance', 'dark') == 'light' ? 'bg-[var(--color-accent)] text-white' : 'bg-gray-200 dark:bg-zinc-700 hover:bg-gray-300 dark:hover:bg-zinc-600 text-gray-700 dark:text-gray-200' }}">
                                            <svg class="w-4 h-4 inline-block ltr:mr-1.5 rtl:ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                            Light
                                        </button>
                                        <button type="button" onclick="setAppearance('dark')" class="flex-1 py-2.5 px-3 rounded-lg text-sm font-semibold transition-colors {{ session('flux_appearance', 'dark') == 'dark' ? 'bg-[var(--color-accent)] text-white' : 'bg-gray-200 dark:bg-zinc-700 hover:bg-gray-300 dark:hover:bg-zinc-600 text-gray-700 dark:text-gray-200' }}">
                                            <svg class="w-4 h-4 inline-block ltr:mr-1.5 rtl:ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                                            Dark
                                        </button>
                                        <button type="button" onclick="setAppearance('system')" class="flex-1 py-2.5 px-3 rounded-lg text-sm font-semibold transition-colors {{ session('flux_appearance', 'dark') == 'system' ? 'bg-[var(--color-accent)] text-white' : 'bg-gray-200 dark:bg-zinc-700 hover:bg-gray-300 dark:hover:bg-zinc-600 text-gray-700 dark:text-gray-200' }}">
                                            <svg class="w-4 h-4 inline-block ltr:mr-1.5 rtl:ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                            Auto
                                        </button>
                                    </div>
                                </div>

                                <!-- Quick Link to Full Settings (Admin only) -->
                                @can('admin.access')
                                <div class="border-t border-zinc-200 dark:border-zinc-700 p-2">
                                    <a href="{{ route('admin.settings.theme') }}" class="flex items-center justify-center gap-2 w-full py-2 text-sm text-[var(--color-accent)] hover:underline">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        {{ __('More Theme Options') }}
                                    </a>
                                </div>
                                @endcan
                            </flux:menu>
                        </flux:dropdown>

                        <!-- Real-time Notifications with Livewire -->
                        <livewire:notification-dropdown />

                        <!-- Search -->
                        <flux:dropdown position="bottom" align="end" class="relative">
                            <flux:tooltip :content="__('Search')" position="bottom">
                                <flux:button variant="ghost" size="sm" class="!h-10 navbar-icon-btn [&>svg]:size-5" icon="magnifying-glass" aria-label="{{ __('Search') }}" />
                            </flux:tooltip>

                            <flux:menu class="w-96">
                                <div class="p-3">
                                    <div class="relative">
                                        <input type="text"
                                            id="globalSearch"
                                            placeholder="{{ __('Search users, courses, announcements...') }}"
                                            class="w-full pl-10 pr-4 py-2 bg-zinc-100 dark:bg-zinc-800 border-0 rounded-lg focus:ring-2 focus:ring-[var(--color-accent)] text-sm"
                                            autocomplete="off">
                                        <svg class="absolute ltr:left-3 rtl:right-3 top-2.5 w-4 h-4 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                        </svg>
                                        <kbd class="absolute rtl:left-3 ltr:right-3 top-2 px-1.5 py-0.5 text-xs bg-zinc-200 dark:bg-zinc-700 rounded">⌘K</kbd>
                                    </div>
                                </div>
                                  <div id="searchResults" class="max-h-64 overflow-y-auto border-t border-zinc-200 dark:border-zinc-700">
                                      <div class="p-4 text-center text-sm text-zinc-500">
                                          <p class="mb-2">{{ __('Type to search...') }}</p>
                                          <p class="text-xs text-zinc-400">{{ __('Search users, courses, quizzes, assignments, and more') }}</p>
                                      </div>
                                  </div>
                            </flux:menu>
                        </flux:dropdown>
                    </flux:navbar>

                    <x-desktop-user-menu />
                </flux:header>

                <!-- Content Area -->
                <main id="main-content"
                    class="
                        flex-1
                        p-4
                        sm:p-6
                        lg:p-8
                        overflow-y-auto
                        bg-zinc-50/50
                        dark:bg-zinc-950/50
                    "
                    role="main"
                    aria-label="{{ __('Main content') }}"
                >
                    <div class="mx-auto max-w-7xl">
                        @hasSection('content')
                            @yield('content')
                        @else
                            {{ $slot ?? '' }}
                        @endif
                    </div>
                </main>
            </div>
        </div>

        @fluxScripts

    <!-- =====================================================
         SECTION 4: THEME SWITCHER FUNCTIONS
         Used by: Theme dropdown in navbar (all pages)
         Description: JavaScript functions for switching accent colors,
         base themes (cyberpunk, synthwave, etc.), and appearance (light/dark)
    ===================================================== -->
    <script>
        // Theme Color Switcher
        function setThemeColor(name, hex) {
            // Save to localStorage for immediate effect
            localStorage.setItem('accent_color', name);
            document.documentElement.style.setProperty('--color-accent', hex, 'important');
            document.documentElement.style.setProperty('--color-accent-light', hex + '80', 'important');
            document.documentElement.setAttribute('data-theme', name);
            document.body.setAttribute('data-theme', name);

            // Update indicator
            var indicator = document.getElementById('themeIndicator');
            if (indicator) {
                indicator.style.backgroundColor = hex;
            }

            // Update button styles - simple approach
            document.querySelectorAll('.theme-btn').forEach(function(btn) {
                // Remove all selection indicators
                btn.classList.remove('ring-2', 'ring-offset-2', 'accent-selected');
                btn.style.removeProperty('box-shadow');

                if (btn.title.toLowerCase() === name.toLowerCase()) {
                    // Apply selected state
                    btn.classList.add('ring-2', 'ring-offset-2', 'accent-selected');
                    btn.style.boxShadow = `0 0 0 2px ${hex}, 0 0 0 6px rgba(0, 0, 0, 0.1)`;
                }
            });

            // Send to server via AJAX to persist (only accent color, not appearance)
            // Don't reload page - theme changes apply immediately
            fetch('{{ route('settings.theme.switch') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: 'theme=' + name
            }).then(function(response) {
                return response.json();
            }).then(function(data) {
                // Theme is already applied, no reload needed
            }).catch(function(error) {
                console.error('Error saving theme:', error);
            });
        }

        // Base Theme Switcher (Classic Light, Cyberpunk, Synthwave, etc.)
        function setBaseTheme(themeName) {
            // Base theme configurations
            const baseThemes = {
                'default-light': {
                    name: 'Classic Light',
                    background: '#ffffff',
                    surface: '#f5f5f4',
                    text: '#1c1917',
                    border: '#e7e5e4',
                },
                'default-dark': {
                    name: 'Classic Dark',
                    background: '#0c0a09',
                    surface: '#1c1917',
                    text: '#fafaf9',
                    border: '#292524',
                },
                'cyberpunk-neon': {
                    name: 'Cyber Neon',
                    background: '#1a0a2e',
                    surface: '#2d1b4e',
                    text: '#e0e0e0',
                    border: '#ff00ff',
                    accent: '#ff00ff',
                    glow: true,
                },
                'cyberpunk-dark': {
                    name: 'Cyber Dark',
                    background: '#000000',
                    surface: '#0a0a0a',
                    text: '#00ffff',
                    border: '#00ffff',
                    accent: '#00ffff',
                    glow: true,
                },
                'synthwave': {
                    name: 'Synthwave',
                    background: '#1a0a1a',
                    surface: '#2d1b2e',
                    text: '#ff71ce',
                    border: '#ff71ce',
                    accent: '#ff71ce',
                },
                'midnight': {
                    name: 'Midnight',
                    background: '#0f172a',
                    surface: '#1e293b',
                    text: '#e2e8f0',
                    border: '#334155',
                    accent: '#6366f1',
                },
                'dracula': {
                    name: 'Dracula',
                    background: '#21222c',
                    surface: '#282a36',
                    text: '#f8f8f2',
                    border: '#44475a',
                    accent: '#bd93f9',
                },
                'nord': {
                    name: 'Nord',
                    background: '#2e3440',
                    surface: '#3b4252',
                    text: '#eceff4',
                    border: '#4c566a',
                    accent: '#88c0d0',
                },
            };

            const theme = baseThemes[themeName];
            if (!theme) return;

            // Set data attribute for CSS theme application
            document.documentElement.setAttribute('data-base-theme', themeName);

            // Set CSS variables as backup/fallback
            document.documentElement.style.setProperty('--theme-background', theme.background || '#ffffff');
            document.documentElement.style.setProperty('--theme-surface', theme.surface || '#f5f5f4');
            document.documentElement.style.setProperty('--theme-text', theme.text || '#1c1917');
            document.documentElement.style.setProperty('--theme-border', theme.border || '#e7e5e4');

            // Set additional theme properties if available
            if (theme.accent) {
                document.documentElement.style.setProperty('--theme-accent', theme.accent);
            }

            // Apply cyberpunk glow effects
            if (theme.glow) {
                document.documentElement.setAttribute('data-theme-glow', 'true');
            } else {
                document.documentElement.removeAttribute('data-theme-glow');
            }

            // Save to localStorage
            localStorage.setItem('base_theme', themeName);

            // Update button styles
            var buttons = document.querySelectorAll('.base-theme-btn');
            buttons.forEach(function(btn) {
                if (btn.getAttribute('onclick') && btn.getAttribute('onclick').includes(themeName)) {
                    btn.classList.add('border-indigo-500', 'bg-indigo-50', 'dark:bg-indigo-900/30');
                    btn.classList.remove('border-zinc-200', 'dark:border-zinc-600');
                } else {
                    btn.classList.remove('border-indigo-500', 'bg-indigo-50', 'dark:bg-indigo-900/30');
                    btn.classList.add('border-zinc-200', 'dark:border-zinc-600');
                }
            });

            console.log('Base theme applied: ' + theme.name);
        }

        // Initialize appearance mode on page load
        (function() {
            const savedAppearance = localStorage.getItem('flux_appearance') || 'dark';
            if (savedAppearance === 'dark') {
                document.documentElement.classList.add('dark');
                document.documentElement.classList.remove('light');
            } else if (savedAppearance === 'light') {
                document.documentElement.classList.add('light');
                document.documentElement.classList.remove('dark');
            }
            // For 'system', let the default session value handle it
        })();

        // Appearance Switcher (Light/Dark)
        function setAppearance(mode) {
            // Save to localStorage for immediate effect (both keys)
            localStorage.setItem('flux_appearance', mode);
            localStorage.setItem('color-theme', mode);

            // Update the HTML class for Flux UI
            if (mode === 'dark') {
                document.documentElement.classList.add('dark');
                document.documentElement.classList.remove('light');
            } else {
                document.documentElement.classList.add('light');
                document.documentElement.classList.remove('dark');
            }

            // Update button styles
            var buttons = document.querySelectorAll('[onclick^="setAppearance"]');
            buttons.forEach(function(btn) {
                if (btn.getAttribute('onclick').includes(mode)) {
                    btn.classList.add('bg-[var(--color-accent)]', 'text-white');
                    btn.classList.remove('bg-gray-100', 'dark:bg-zinc-700', 'hover:bg-gray-200', 'dark:hover:bg-zinc-600');
                } else {
                    btn.classList.remove('bg-[var(--color-accent)]', 'text-white');
                    btn.classList.add('bg-gray-100', 'dark:bg-zinc-700', 'hover:bg-gray-200', 'dark:hover:bg-zinc-600');
                }
            });

            // Send to server via AJAX to persist
            // Don't reload page - appearance changes apply immediately
            fetch('{{ route('settings.theme.switch') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: 'theme=' + mode
            }).then(function(response) {
                return response.json();
            }).then(function(data) {
                // Appearance is already applied, no reload needed
            }).catch(function(error) {
                console.error('Error saving appearance:', error);
            });
        }

        // =====================================================
        // SIDEBAR TOGGLE FUNCTIONALITY
        // Used by: Desktop sidebar toggle button
        // Description: Hide/show sidebar and persist state
        // =====================================================

        // Initialize sidebar state on page load
        (function() {
            var sidebar = document.querySelector('flux-sidebar');
            var toggleBtn = document.getElementById('desktop-sidebar-toggle');
            var tooltip = document.getElementById('sidebar-tooltip');

            if (!sidebar) return;

            // Check localStorage for saved state
            var sidebarHidden = localStorage.getItem('sidebar_hidden');

            if (sidebarHidden === 'true') {
                sidebar.classList.add('sidebar-hidden');
                if (toggleBtn) {
                    toggleBtn.classList.add('sidebar-toggle-active');
                    toggleBtn.setAttribute('aria-expanded', 'false');
                }
                if (tooltip) {
                    tooltip.textContent = 'Show sidebar';
                }
            } else {
                sidebar.classList.remove('sidebar-hidden');
                if (toggleBtn) {
                    toggleBtn.classList.remove('sidebar-toggle-active');
                    toggleBtn.setAttribute('aria-expanded', 'true');
                }
                if (tooltip) {
                    tooltip.textContent = 'Hide sidebar';
                }
            }
        })();

        // Toggle sidebar visibility
        function toggleSidebar() {
            var sidebar = document.querySelector('flux-sidebar');
            var toggleBtn = document.getElementById('desktop-sidebar-toggle');
            var tooltip = document.getElementById('sidebar-tooltip');

            if (!sidebar) return;

            var isHidden = sidebar.classList.contains('sidebar-hidden');

            if (isHidden) {
                // Show sidebar
                sidebar.classList.remove('sidebar-hidden');
                localStorage.setItem('sidebar_hidden', 'false');
                if (toggleBtn) {
                    toggleBtn.classList.remove('sidebar-toggle-active');
                    toggleBtn.setAttribute('aria-expanded', 'true');
                }
                if (tooltip) {
                    tooltip.textContent = 'Hide sidebar';
                }
            } else {
                // Hide sidebar
                sidebar.classList.add('sidebar-hidden');
                localStorage.setItem('sidebar_hidden', 'true');
                if (toggleBtn) {
                    toggleBtn.classList.add('sidebar-toggle-active');
                    toggleBtn.setAttribute('aria-expanded', 'false');
                }
                if (tooltip) {
                    tooltip.textContent = 'Show sidebar';
                }
            }
        }

        // Keyboard shortcut: Ctrl+B or Cmd+B to toggle sidebar
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'b') {
                e.preventDefault();
                toggleSidebar();
            }
        });

        // =====================================================
        // ACCESSIBILITY TOGGLE FUNCTIONS
        // Used by: Accessibility dropdown in navbar
        // Description: Toggle accessibility settings with instant feedback
        // =====================================================

        // Toggle a single accessibility setting
        function toggleAccessibility(setting, value) {
            // Save to localStorage for immediate effect
            localStorage.setItem('accessibility_' + setting, value);

            // Apply the setting immediately
            var htmlElement = document.documentElement;

            if (setting === 'font_size') {
                htmlElement.setAttribute('data-font-size', value);
            } else {
                var attrName = 'data-' + setting.replace('_', '-') + '-mode';
                if (value === 'true') {
                    htmlElement.setAttribute(attrName, 'true');
                } else {
                    htmlElement.removeAttribute(attrName);
                }
            }

            // Send to server to persist
            fetch('{{ route('settings.accessibility.toggle') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: setting + '=' + value
            }).then(function(response) {
                return response.json();
            }).then(function(data) {
                // Reload page to apply server-side session
                // Add a small delay to ensure session is saved
                setTimeout(function() {
                    window.location.reload();
                }, 100);
            }).catch(function(error) {
                console.error('Error saving accessibility setting:', error);
            });
        }

        // Reset all accessibility settings
        function resetAllAccessibility() {
            // Clear localStorage
            var settings = ['high_contrast', 'large_text', 'dyslexia_font', 'reduced_motion', 'grayscale', 'line_spacing', 'focus_outline', 'font_size'];
            settings.forEach(function(setting) {
                localStorage.removeItem('accessibility_' + setting);
            });

            // Remove data attributes
            var htmlElement = document.documentElement;
            htmlElement.removeAttribute('data-high-contrast-mode');
            htmlElement.removeAttribute('data-large-text-mode');
            htmlElement.removeAttribute('data-dyslexia-font-mode');
            htmlElement.removeAttribute('data-reduced-motion-mode');
            htmlElement.removeAttribute('data-grayscale-mode');
            htmlElement.removeAttribute('data-line-spacing-mode');
            htmlElement.removeAttribute('data-focus-outline-mode');
            htmlElement.setAttribute('data-font-size', 'medium');

            // Send to server
            fetch('{{ route('settings.accessibility.reset') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }).then(function(response) {
                return response.json();
            }).then(function(data) {
                // Add a small delay to ensure session is saved
                setTimeout(function() {
                    window.location.reload();
                }, 100);
            }).catch(function(error) {
                console.error('Error resetting accessibility settings:', error);
            });
        }

        // Global Search Functionality
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Initializing search functionality...');
            const searchInput = document.getElementById('globalSearch');
            const searchResults = document.getElementById('searchResults');
            let searchTimeout;

            if (!searchInput || !searchResults) {
                console.error('Search elements not found:', { searchInput: !!searchInput, searchResults: !!searchResults });
                return;
            }

            console.log('Search elements found, initializing...');

            // Handle search input
            searchInput.addEventListener('input', function(e) {
                const query = e.target.value.trim();

                clearTimeout(searchTimeout);

                if (query.length < 2) {
                    showEmptyState();
                    return;
                }

                // Debounce search requests
                searchTimeout = setTimeout(() => {
                    performSearch(query);
                }, 300);
            });

            let selectedIndex = -1;
            let searchResultItems = [];

            // Handle keyboard shortcuts
            document.addEventListener('keydown', function(e) {
                // Cmd+K or Ctrl+K to focus search
                if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
                    e.preventDefault();
                    // Find the search button and click it to open dropdown
                    const searchButton = document.querySelector('button[aria-label*="Search"]');
                    if (searchButton) {
                        searchButton.click();
                        // Focus the input after a short delay to ensure dropdown is open
                        setTimeout(() => searchInput.focus(), 100);
                    } else {
                        searchInput.focus();
                    }
                }

                // Escape to close search
                if (e.key === 'Escape' && document.activeElement === searchInput) {
                    searchInput.blur();
                    const dropdown = searchInput.closest('[data-flux-dropdown]');
                    if (dropdown) {
                        dropdown.setAttribute('data-open', 'false');
                    }
                    selectedIndex = -1;
                    updateSelection();
                }

                // Navigation within search results
                if (document.activeElement === searchInput) {
                    const resultsContainer = document.getElementById('searchResults');
                    const resultItems = resultsContainer ? resultsContainer.querySelectorAll('a') : [];

                    if (resultItems.length > 0) {
                        if (e.key === 'ArrowDown') {
                            e.preventDefault();
                            selectedIndex = Math.min(selectedIndex + 1, resultItems.length - 1);
                            updateSelection();
                        } else if (e.key === 'ArrowUp') {
                            e.preventDefault();
                            selectedIndex = Math.max(selectedIndex - 1, -1);
                            updateSelection();
                        } else if (e.key === 'Enter' && selectedIndex >= 0) {
                            e.preventDefault();
                            resultItems[selectedIndex].click();
                        }
                    }
                }
            });

            function updateSelection() {
                const resultsContainer = document.getElementById('searchResults');
                const resultItems = resultsContainer ? resultsContainer.querySelectorAll('a') : [];

                // Remove previous selection
                resultItems.forEach(item => item.classList.remove('bg-blue-50', 'dark:bg-blue-900'));

                // Add new selection
                if (selectedIndex >= 0 && resultItems[selectedIndex]) {
                    resultItems[selectedIndex].classList.add('bg-blue-50', 'dark:bg-blue-900');
                    resultItems[selectedIndex].scrollIntoView({ block: 'nearest' });
                }
            }

            function performSearch(query) {
                searchResults.innerHTML = `
                    <div class="p-4 text-center">
                        <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-[var(--color-accent)] mx-auto"></div>
                        <p class="mt-2 text-sm text-zinc-500">{{ __('Searching...') }}</p>
                    </div>
                `;

                console.log('Performing search for:', query);

                fetch(`{{ url('/api/search') }}?q=${encodeURIComponent(query)}&limit=8`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    }
                })
                .then(response => {
                    console.log('Search response status:', response.status);
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Search results:', data);
                    displayResults(data.results || [], query);
                })
                .catch(error => {
                    console.error('Search error:', error);
                    searchResults.innerHTML = `
                        <div class="p-4 text-center text-sm text-red-600">
                            <p>{{ __('Search failed. Please try again.') }}</p>
                        </div>
                    `;
                });
            }

            function displayResults(results, query) {
                selectedIndex = -1; // Reset selection

                if (results.length === 0) {
                    searchResults.innerHTML = `
                        <div class="p-4 text-center text-sm text-zinc-500">
                            <p>No results found for "${query}"</p>
                        </div>
                    `;
                    return;
                }

                const resultsHtml = results.map((result, index) => {
                    const iconHtml = getIconHtml(result.icon);
                    const subtitle = result.subtitle ? `<p class="text-xs text-zinc-400 truncate">${result.subtitle}</p>` : '';

                    return `
                        <a href="${result.url !== '#' ? result.url : '#'}" class="search-result-enter block px-4 py-3 hover:bg-zinc-50 dark:hover:bg-zinc-700 border-b border-zinc-100 dark:border-zinc-600 last:border-b-0 transition-colors" style="animation-delay: ${index * 50}ms">
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0 w-8 h-8 bg-zinc-100 dark:bg-zinc-700 rounded-lg flex items-center justify-center">
                                    ${iconHtml}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-zinc-900 dark:text-white truncate">${highlightText(result.title, query)}</p>
                                    ${subtitle}
                                </div>
                            </div>
                        </a>
                    `;
                }).join('');

                // Add "View All Results" link if there are results
                if (results.length >= 8) {
                    resultsHtml += `
                        <div class="px-4 py-3 border-t border-zinc-100 dark:border-zinc-600">
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 text-center">
                                {{ __('Showing top 8 results. Continue typing for more specific results.') }}
                            </p>
                        </div>
                    `;
                }

                searchResults.innerHTML = resultsHtml;
            }

            function showEmptyState() {
                searchResults.innerHTML = `
                    <div class="p-4 text-center text-sm text-zinc-500">
                        <p class="mb-2">{{ __('Type to search...') }}</p>
                        <p class="text-xs text-zinc-400">{{ __('Search users, courses, quizzes, assignments, and more') }}</p>
                    </div>
                `;
            }

            function getIconHtml(icon) {
                const iconMap = {
                    'user': '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>',
                    'academic-cap': '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"/></svg>',
                    'book-open': '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>',
                    'megaphone': '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>',
                    'shield-check': '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>',
                    'clipboard-list': '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2m0 0V3a2 2 0 012 2m-2 0v2" /></svg>',
                    'document-text': '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>',
                    'default': '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>'
                };

                return iconMap[icon] || iconMap['default'];
            }

            function highlightText(text, query) {
                if (!query) return text;
                const regex = new RegExp(`(${query})`, 'gi');
                return text.replace(regex, '<mark class="bg-yellow-200 dark:bg-yellow-800">$1</mark>');
            }
        });
    </script>

    @include('partials.theme-settings')
</body>
</html>
