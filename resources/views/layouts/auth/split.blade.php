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

        <!-- =====================================================
             AUTH LAYOUT STYLES - SPLIT.BLADE.PHP
             Used by: Split-screen auth pages (login, register, etc.)
             Description: Accent colors, accessibility styles, skip links,
             split panel layout
        ===================================================== -->
        <style>
            /* Base accent color from theme */
            [data-theme="red"] { --color-accent: #ef4444; --color-accent-light: #fca5a5; }
            [data-theme="orange"] { --color-accent: #f97316; --color-accent-light: #fdba74; }
            [data-theme="amber"] { --color-accent: #f59e0b; --color-accent-light: #fcd34d; }
            [data-theme="yellow"] { --color-accent: #eab308; --color-accent-light: #fde047; }
            [data-theme="lime"] { --color-accent: #84cc16; --color-accent-light: #bef264; }
            [data-theme="green"] { --color-accent: #22c55e; --color-accent-light: #86efac; }
            [data-theme="emerald"] { --color-accent: #10b981; --color-accent-light: #6ee7b7; }
            [data-theme="teal"] { --color-accent: #14b8a6; --color-accent-light: #5eead4; }
            [data-theme="cyan"] { --color-accent: #06b6d4; --color-accent-light: #67e8f9; }
            [data-theme="sky"] { --color-accent: #0ea5e9; --color-accent-light: #7dd3fc; }
            [data-theme="blue"] { --color-accent: #3b82f6; --color-accent-light: #93c5fd; }
            [data-theme="indigo"] { --color-accent: #6366f1; --color-accent-light: #a5b4fc; }
            [data-theme="violet"] { --color-accent: #8b5cf6; --color-accent-light: #c4b5fd; }
            [data-theme="purple"] { --color-accent: #a855f7; --color-accent-light: #d8b4fe; }
            [data-theme="fuchsia"] { --color-accent: #d946ef; --color-accent-light: #f0abfc; }
            [data-theme="pink"] { --color-accent: #ec4899; --color-accent-light: #f9a8d4; }
            [data-theme="rose"] { --color-accent: #f43f5e; --color-accent-light: #fda4af; }
            [data-theme="slate"] { --color-accent: #64748b; --color-accent-light: #94a3b8; }
            [data-theme="gray"] { --color-accent: #6b7280; --color-accent-light: #9ca3af; }
            [data-theme="zinc"] { --color-accent: #71717a; --color-accent-light: #a1a1aa; }
            [data-theme="neutral"] { --color-accent: #737373; --color-accent-light: #a3a3a3; }
            [data-theme="stone"] { --color-accent: #78716c; --color-accent-light: #a8a29e; }
            [data-theme="base"] { --color-accent: #3b82f6; --color-accent-light: #93c5fd; }

            /* Global accent color application */
            [data-theme] .bg-zinc-600,
            [data-theme] .bg-blue-600 {
                background-color: var(--color-accent) !important;
            }

            [data-theme] .text-zinc-600,
            [data-theme] .text-blue-600 {
                color: var(--color-accent) !important;
            }

            [data-theme] input:focus,
            [data-theme] select:focus,
            [data-theme] textarea:focus {
                outline: none;
                border-color: var(--color-accent) !important;
                box-shadow: 0 0 0 3px var(--color-accent-light) !important;
            }

            /* Accessibility: Font Size */
            [data-font-size="small"] { font-size: 14px !important; }
            [data-font-size="large"] { font-size: 18px !important; }
            [data-font-size="xlarge"] { font-size: 20px !important; }

            /* Accessibility: Large Text Mode */
            [data-large-text-mode="true"] { font-size: 18px !important; }

            /* Accessibility: High Contrast Mode */
            [data-high-contrast-mode="true"] {
                --tw-border-opacity: 1 !important;
            }

            /* Accessibility: Reduced Motion */
            [data-reduced-motion="true"] *,
            [data-reduced-motion="true"] *::before,
            [data-reduced-motion="true"] *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }

            /* Accessibility: Grayscale */
            [data-grayscale="true"] {
                filter: grayscale(100%) !important;
            }

            /* Accessibility: Dyslexia Font */
            [data-dyslexia-font-mode="true"] {
                font-family: 'OpenDyslexic', 'Comic Sans MS', sans-serif !important;
                letter-spacing: 0.05em !important;
                word-spacing: 0.1em !important;
            }

            /* Accessibility: Line Spacing */
            [data-line-spacing-mode="true"] p,
            [data-line-spacing-mode="true"] li,
            [data-line-spacing-mode="true"] span {
                line-height: 2 !important;
            }

            /* Accessibility: Focus Outline */
            [data-focus-outline-mode="true"] *:focus {
                outline: 3px solid var(--color-accent, #6366f1) !important;
                outline-offset: 2px !important;
            }

            /* Skip Link Styles */
            .skip-link {
                position: absolute;
                top: -40px;
                left: 0;
                background: var(--color-accent, #6366f1);
                color: white;
                padding: 8px 16px;
                z-index: 100;
                transition: top 0.3s;
                text-decoration: none;
                border-radius: 0 0 4px 4px;
            }
            .skip-link:focus {
                top: 0;
                color: white;
            }
        </style>
        <script>
            // Apply accent color and accessibility settings
            (function() {
                const accentColors = {
                    'red': '#ef4444', 'orange': '#f97316', 'amber': '#f59e0b', 'yellow': '#eab308',
                    'lime': '#84cc16', 'green': '#22c55e', 'emerald': '#10b981', 'teal': '#14b8a6',
                    'cyan': '#06b6d4', 'sky': '#0ea5e9', 'blue': '#3b82f6', 'indigo': '#6366f1',
                    'violet': '#8b5cf6', 'purple': '#a855f7', 'fuchsia': '#d946ef', 'pink': '#ec4899',
                    'rose': '#f43f5e', 'slate': '#64748b', 'gray': '#6b7280', 'zinc': '#71717a',
                    'neutral': '#737373', 'stone': '#78716c'
                };

                // Apply accent color
                const savedColor = localStorage.getItem('accent_color') || '{{ session("accent_color", config("settings.accent_color", "zinc")) }}';
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
    <body class="min-h-screen bg-white antialiased dark:bg-linear-to-b dark:from-neutral-950 dark:to-neutral-900">
        <!-- Skip Link for Accessibility -->
        <a href="#main-content" class="skip-link">{{ __('Skip to main content') }}</a>

        <div class="relative grid h-dvh flex-col items-center justify-center px-8 sm:px-0 lg:max-w-none lg:grid-cols-2 lg:px-0">
            <!-- Left Panel - Branding -->
            <div class="bg-muted relative hidden h-full flex-col p-10 text-white lg:flex dark:border-e dark:border-neutral-800" role="complementary" aria-label="{{ __('Welcome panel') }}">
                <div class="absolute inset-0 bg-neutral-900"></div>
                <a href="{{ route('home') }}" class="relative z-20 flex items-center text-lg font-medium" wire:navigate aria-label="{{ __('Go to homepage') }}">
                    <span class="flex h-10 w-10 items-center justify-center rounded-md" aria-hidden="true">
                        <x-app-logo-icon class="me-2 h-7 fill-current text-white" />
                    </span>
                    {{ config('app.name', 'Laravel') }}
                </a>

                @php
                    [$message, $author] = str(Illuminate\Foundation\Inspiring::quotes()->random())->explode('-');
                @endphp

                <div class="relative z-20 mt-auto">
                    <blockquote class="space-y-2">
                        <flux:heading size="lg">&ldquo;{{ trim($message) }}&rdquo;</flux:heading>
                        <footer><flux:heading>{{ trim($author) }}</flux:heading></footer>
                    </blockquote>
                </div>
            </div>

            <!-- Right Panel - Form -->
            <div class="w-full lg:p-8">
                <main id="main-content" class="mx-auto flex w-full flex-col justify-center space-y-6 sm:w-[350px]" role="main" aria-label="{{ __('Authentication form') }}">
                    <a href="{{ route('home') }}" class="z-20 flex flex-col items-center gap-2 font-medium lg:hidden" wire:navigate aria-label="{{ __('Go to homepage') }}">
                        <span class="flex h-9 w-9 items-center justify-center rounded-md" aria-hidden="true">
                            <x-app-logo-icon class="size-9 fill-current text-black dark:text-white" />
                        </span>

                        <span class="sr-only">{{ config('app.name', 'Laravel') }}</span>
                    </a>
                    {{ $slot }}
                </main>
            </div>
        </div>
        @fluxScripts
    </body>
</html>
