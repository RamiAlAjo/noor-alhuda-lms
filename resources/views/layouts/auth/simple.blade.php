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
        <title>@yield('title', __('Login'))</title>
        <meta name="description" content="{{ __('Login to your LMS account to access courses, grades, and more.') }}">
        <meta name="robots" content="noindex, nofollow">

        <!-- =====================================================
             AUTH LAYOUT STYLES - SIMPLE.BLADE.PHP
             Used by: Login, Register, Forgot Password, Reset Password pages
             Description: Gradient backgrounds, glassmorphism cards,
             floating orbs animation, language switcher, gradient selector
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

            [data-theme] .border-zinc-500,
            [data-theme] .border-blue-500 {
                border-color: var(--color-accent) !important;
            }

            /* Flux UI components */
            [data-theme] .flux-button-primary {
                background-color: var(--color-accent) !important;
                border-color: var(--color-accent) !important;
            }

            [data-theme] .flux-button-primary:hover {
                background-color: var(--color-accent-light) !important;
            }

            /* Focus states */
            [data-theme] input:focus,
            [data-theme] select:focus,
            [data-theme] textarea:focus {
                outline: none;
                border-color: var(--color-accent) !important;
                box-shadow: 0 0 0 3px var(--color-accent-light) !important;
            }

            /* Links */
            [data-theme] a:not(.btn):not([class*="button"]) {
                color: var(--color-accent) !important;
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

            /* Language Switcher */
            .language-switcher {
                display: flex;
                flex-wrap: wrap;
                gap: 0.25rem;
                align-items: center;
            }
            .language-switcher a {
                padding: 0.25rem 0.5rem;
                border-radius: 0.25rem;
                transition: background-color 0.2s;
            }
            .language-switcher a:hover,
            .language-switcher a:focus {
                background-color: var(--color-accent-light, rgba(99, 102, 241, 0.2));
            }
            .language-switcher a[aria-current="true"] {
                font-weight: bold;
                background-color: var(--color-accent, #6366f1);
                color: white;
            }

            /* Background Pattern */
            .auth-background {
                position: fixed;
                inset: 0;
                z-index: -1;
                overflow: hidden;
            }

            .auth-background::before {
                content: '';
                position: absolute;
                width: 200%;
                height: 200%;
                top: -50%;
                left: -50%;
                background:
                    radial-gradient(circle at 20% 80%, var(--color-accent-light, rgba(99, 102, 241, 0.15)) 0%, transparent 50%),
                    radial-gradient(circle at 80% 20%, rgba(139, 92, 246, 0.15) 0%, transparent 50%),
                    radial-gradient(circle at 40% 40%, rgba(6, 182, 212, 0.1) 0%, transparent 40%);
                animation: backgroundPulse 15s ease-in-out infinite;
            }

            @keyframes backgroundPulse {
                0%, 100% { transform: translate(0, 0) rotate(0deg); }
                25% { transform: translate(2%, 2%) rotate(1deg); }
                50% { transform: translate(0, 4%) rotate(0deg); }
                75% { transform: translate(-2%, 2%) rotate(-1deg); }
            }

            /* Floating Orbs */
            .floating-orb {
                position: absolute;
                border-radius: 50%;
                filter: blur(60px);
                opacity: 0.5;
                animation: float 20s ease-in-out infinite;
                pointer-events: none;
            }

            .orb-1 {
                width: 300px;
                height: 300px;
                top: 10%;
                left: 10%;
                background: var(--color-accent, #6366f1);
                animation-delay: 0s;
            }

            .orb-2 {
                width: 250px;
                height: 250px;
                bottom: 20%;
                right: 15%;
                background: #8b5cf6;
                animation-delay: -5s;
            }

            .orb-3 {
                width: 200px;
                height: 200px;
                top: 50%;
                left: 60%;
                background: #06b6d4;
                animation-delay: -10s;
            }

            @keyframes float {
                0%, 100% { transform: translateY(0) scale(1); }
                50% { transform: translateY(-30px) scale(1.05); }
            }

            /* Grid Pattern */
            .grid-pattern {
                position: absolute;
                inset: 0;
                background-image:
                    linear-gradient(rgba(255, 255, 255, 0.03) 1px, transparent 1px),
                    linear-gradient(90deg, rgba(255, 255, 255, 0.03) 1px, transparent 1px);
                background-size: 50px 50px;
                mask-image: radial-gradient(ellipse at center, black 30%, transparent 70%);
                pointer-events: none;
            }

            /* Card Glassmorphism */
            .auth-card {
                background: rgba(255, 255, 255, 0.95) !important;
                backdrop-filter: blur(20px);
                -webkit-backdrop-filter: blur(20px);
                border: 2px solid var(--color-accent, #6366f1) !important;
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            }

            .dark .auth-card {
                background: rgba(30, 30, 35, 0.95) !important;
                border: 2px solid var(--color-accent, #6366f1) !important;
            }

            /* Card Shine Effect */
            .auth-card::before {
                content: '';
                position: absolute;
                top: 0;
                left: -100%;
                width: 50%;
                height: 100%;
                background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
                transform: skewX(-25deg);
                transition: left 0.5s;
            }

            .auth-card:hover::before {
                left: 100%;
                transition: left 0.7s;
            }

            /* Footer */
            .auth-footer {
                font-size: 0.875rem;
                color: rgba(0, 0, 0, 0.6);
            }

            .dark .auth-footer {
                color: rgba(255, 255, 255, 0.7);
            }

            .auth-footer a {
                transition: all 0.2s ease;
            }

            .auth-footer a:hover {
                color: var(--color-accent, #6366f1) !important;
                text-decoration: underline;
            }

            /* Animation Classes */
            .fade-in-up {
                animation: fadeInUp 0.5s ease-out forwards;
                opacity: 0;
                transform: translateY(20px);
            }

            @keyframes fadeInUp {
                to { opacity: 1; transform: translateY(0); }
            }

            .stagger-1 { animation-delay: 0.1s; }
            .stagger-2 { animation-delay: 0.2s; }
            .stagger-3 { animation-delay: 0.3s; }
            .stagger-4 { animation-delay: 0.4s; }

            /* Responsive */
            @media (max-width: 640px) {
                .auth-card {
                    margin: 0.5rem;
                    border-radius: 0.75rem;
                }
                .language-switcher {
                    justify-content: center;
                }
                .floating-orb {
                    opacity: 0.3;
                }
                /* Gradient selector responsive */
                .gradient-selector-container {
                    bottom: 1rem !important;
                    left: 0.5rem !important;
                    top: auto !important;
                    right: auto !important;
                    transform: none !important;
                }
                .gradient-selector-container button {
                    width: 28px !important;
                    height: 28px !important;
                }
            }

            /* Tablet responsive */
            @media (min-width: 641px) and (max-width: 1024px) {
                .gradient-selector-container {
                    bottom: 1.5rem !important;
                    left: 1rem !important;
                    top: auto !important;
                    right: auto !important;
                    transform: none !important;
                }
            }

            /* Large screens */
            @media (min-width: 1025px) {
                .gradient-selector-container {
                    bottom: 1.5rem !important;
                    left: 1.5rem !important;
                    top: auto !important;
                    right: auto !important;
                    transform: none !important;
                }
            }
        </style>
        @include('partials.theme-settings')
    </head>
    <body class="min-h-screen antialiased {{ session('flux_appearance') == 'light' ? '' : 'dark' }}">
        <!-- Skip Link -->
        <a href="#main-content" class="skip-link">{{ __('Skip to main content') }}</a>

        <!-- Animated Background -->
        <div class="auth-background bg-gradient-to-br {{ App\Http\Controllers\ThemeController::getGradientClasses()['light'] }} dark:bg-gradient-to-b {{ App\Http\Controllers\ThemeController::getGradientClasses()['dark'] }}" aria-hidden="true">
            <div class="floating-orb orb-1"></div>
            <div class="floating-orb orb-2"></div>
            <div class="floating-orb orb-3"></div>
            <div class="grid-pattern"></div>
        </div>

        <div class="bg-muted flex min-h-svh flex-col items-center justify-center gap-2 p-4" role="presentation">
            <!-- Language Switcher -->
            <nav class="absolute top-4 end-4 language-switcher" role="navigation" aria-label="{{ __('Language selection') }}">
                <a href="{{ route('language.switch', 'en') }}" {{ app()->getLocale() == 'en' ? 'aria-current="true"' : '' }} wire:navigate>EN</a>
                <span aria-hidden="true">|</span>
                <a href="{{ route('language.switch', 'ar') }}" {{ app()->getLocale() == 'ar' ? 'aria-current="true"' : '' }} wire:navigate>AR</a>
                <span aria-hidden="true">|</span>
                <a href="{{ route('language.switch', 'fr') }}" {{ app()->getLocale() == 'fr' ? 'aria-current="true"' : '' }} wire:navigate>FR</a>
                <span aria-hidden="true">|</span>
                <a href="{{ route('language.switch', 'tr') }}" {{ app()->getLocale() == 'tr' ? 'aria-current="true"' : '' }} wire:navigate>TR</a>
                <span aria-hidden="true">|</span>
                <a href="{{ route('language.switch', 'zh') }}" {{ app()->getLocale() == 'zh' ? 'aria-current="true"' : '' }} wire:navigate>ZH</a>
                <span aria-hidden="true">|</span>
                <a href="{{ route('language.switch', 'fa') }}" {{ app()->getLocale() == 'fa' ? 'aria-current="true"' : '' }} wire:navigate>FA</a>
                <span aria-hidden="true">|</span>
                <a href="{{ route('language.switch', 'id') }}" {{ app()->getLocale() == 'id' ? 'aria-current="true"' : '' }} wire:navigate>ID</a>
                <span aria-hidden="true">|</span>
                <a href="{{ route('language.switch', 'ku') }}" {{ app()->getLocale() == 'ku' ? 'aria-current="true"' : '' }} wire:navigate>KU</a>
                <span aria-hidden="true">|</span>
                <a href="{{ route('language.switch', 'hy') }}" {{ app()->getLocale() == 'hy' ? 'aria-current="true"' : '' }} wire:navigate>HY</a>
            </nav>

            <!-- Gradient Selector -->
            <div class="gradient-selector-container fixed bottom-6 left-6 z-50 flex gap-2 p-1.5 rounded-full bg-white/50 dark:bg-black/50 backdrop-blur-sm" role="group" aria-label="{{ __('Background gradient selection') }}">
                @php
                    $currentGradient = session('auth_gradient', 'indigo');
                    $gradients = [
                        'indigo' => ['from-indigo-50 via-white to-purple-50', '#6366f1', '#8b5cf6'],
                        'blue' => ['from-blue-50 via-white to-cyan-50', '#3b82f6', '#0ea5e9'],
                        'purple' => ['from-purple-50 via-pink-50 to-rose-50', '#a855f7', '#d946ef'],
                        'pink' => ['from-pink-50 via-rose-50 to-orange-50', '#ec4899', '#f472b6'],
                        'rose' => ['from-rose-50 via-red-50 to-orange-50', '#f43f5e', '#fb7185'],
                        'orange' => ['from-orange-50 via-amber-50 to-yellow-50', '#f97316', '#fb923c'],
                        'emerald' => ['from-emerald-50 via-teal-50 to-cyan-50', '#10b981', '#34d399'],
                        'teal' => ['from-teal-50 via-green-50 to-emerald-50', '#14b8a6', '#2dd4bf'],
                        'cyan' => ['from-cyan-50 via-sky-50 to-blue-50', '#06b6d4', '#22d3ee'],
                    ];
                    $selectedGradient = $gradients[$currentGradient] ?? $gradients['indigo'];
                @endphp
                <style>
                    :root {
                        --gradient-primary: {{ $selectedGradient[1] }};
                        --gradient-secondary: {{ $selectedGradient[2] }};
                    }
                </style>
                <style>
                    /* Form elements use gradient colors */
                    .auth-card button[type="submit"],
                    .auth-card [type="submit"] {
                        background-color: var(--gradient-primary) !important;
                        border-color: var(--gradient-primary) !important;
                    }

                    .auth-card button[type="submit"]:hover,
                    .auth-card [type="submit"]:hover {
                        background-color: var(--gradient-secondary) !important;
                    }

                    .auth-card input:focus,
                    .auth-card select:focus,
                    .auth-card textarea:focus {
                        border-color: var(--gradient-primary) !important;
                        box-shadow: 0 0 0 3px var(--gradient-secondary) !important;
                    }

                    .auth-card a {
                        color: var(--gradient-primary) !important;
                    }

                    .auth-card .flux-link {
                        color: var(--gradient-primary) !important;
                    }

                    /* Form card border uses gradient */
                    .auth-card {
                        border-color: var(--gradient-primary) !important;
                    }

                    /* Language switcher uses gradient */
                    .language-switcher a[aria-current="true"] {
                        background-color: var(--gradient-primary) !important;
                    }

                    .language-switcher a:hover,
                    .language-switcher a:focus {
                        background-color: var(--gradient-secondary) !important;
                        opacity: 0.8;
                    }

                    /* Decorative corner uses gradient */
                    .bg-gradient-to-bl {
                        background: var(--gradient-primary) !important;
                    }

                    /* Skip link uses gradient */
                    .skip-link {
                        background: var(--gradient-primary) !important;
                    }
                </style>
                @foreach($gradients as $key => $colors)
                    <button type="button"
                        class="w-8 h-8 rounded-full border-3 {{ $currentGradient === $key ? 'border-black dark:border-white ring-2 ring-offset-2 ring-[var(--gradient-primary)]' : 'border-transparent hover:border-black dark:hover:border-white' }} transition-all hover:scale-110 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[var(--color-accent)] cursor-pointer"
                        style="background: linear-gradient(135deg, {{ $colors[1] }} 0%, {{ $colors[2] }} 100%)"
                        title="{{ ucfirst($key) }} Gradient"
                        aria-label="{{ __('Select :name gradient', ['name' => ucfirst($key)]) }}"
                        {{ $currentGradient === $key ? 'aria-pressed="true"' : 'aria-pressed="false"' }}
                        onclick="switchGradient('{{ $key }}')">
                    </button>
                @endforeach
                <script>
                    function switchGradient(gradient) {
                        // Define gradient mappings
                        const gradients = {
                            'indigo': { light: 'from-indigo-50 via-white to-purple-50', dark: 'dark:from-indigo-950 dark:via-purple-950/30 dark:to-zinc-900', primary: '#6366f1', secondary: '#8b5cf6' },
                            'blue': { light: 'from-blue-50 via-white to-cyan-50', dark: 'dark:from-blue-950 dark:via-cyan-950/30 dark:to-zinc-900', primary: '#3b82f6', secondary: '#0ea5e9' },
                            'purple': { light: 'from-purple-50 via-pink-50 to-rose-50', dark: 'dark:from-purple-950 dark:via-pink-950/30 dark:to-zinc-900', primary: '#a855f7', secondary: '#d946ef' },
                            'pink': { light: 'from-pink-50 via-rose-50 to-orange-50', dark: 'dark:from-pink-950 dark:via-rose-950/30 dark:to-zinc-900', primary: '#ec4899', secondary: '#f472b6' },
                            'rose': { light: 'from-rose-50 via-red-50 to-orange-50', dark: 'dark:from-rose-950 dark:via-red-950/30 dark:to-zinc-900', primary: '#f43f5e', secondary: '#fb7185' },
                            'orange': { light: 'from-orange-50 via-amber-50 to-yellow-50', dark: 'dark:from-orange-950 dark:via-amber-950/30 dark:to-zinc-900', primary: '#f97316', secondary: '#fb923c' },
                            'emerald': { light: 'from-emerald-50 via-teal-50 to-cyan-50', dark: 'dark:from-emerald-950 dark:via-teal-950/30 dark:to-zinc-900', primary: '#10b981', secondary: '#34d399' },
                            'teal': { light: 'from-teal-50 via-green-50 to-emerald-50', dark: 'dark:from-teal-950 dark:via-green-950/30 dark:to-zinc-900', primary: '#14b8a6', secondary: '#2dd4bf' },
                            'cyan': { light: 'from-cyan-50 via-sky-50 to-blue-50', dark: 'dark:from-cyan-950 dark:via-sky-950/30 dark:to-zinc-900', primary: '#06b6d4', secondary: '#22d3ee' },
                        };

                        const selected = gradients[gradient] || gradients['indigo'];

                        // IMMEDIATELY update the background without waiting for server
                        const bg = document.querySelector('.auth-background');
                        bg.className = 'auth-background bg-gradient-to-br ' + selected.light + ' ' + selected.dark;

                        // IMMEDIATELY update CSS variables for instant button color change
                        document.documentElement.style.setProperty('--gradient-primary', selected.primary);
                        document.documentElement.style.setProperty('--gradient-secondary', selected.secondary);

                        // IMMEDIATELY update button styles
                        document.querySelectorAll('[onclick^="switchGradient"]').forEach(btn => {
                            const isSelected = btn.getAttribute('onclick').includes(gradient);
                            // Remove selection styling from all buttons
                            btn.classList.remove('ring-2', 'ring-offset-2', 'border-black', 'dark:border-white');
                            btn.setAttribute('aria-pressed', 'false');

                            if (isSelected) {
                                // Add selection styling to clicked button
                                btn.classList.add('ring-2', 'ring-offset-2', 'border-black', 'dark:border-white');
                                btn.setAttribute('aria-pressed', 'true');
                            }
                        });

                        // Store in localStorage for immediate effect
                        localStorage.setItem('auth_gradient', gradient);

                        // Also save to server (fire and forget)
                        fetch('{{ route('gradient.switch') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: 'gradient=' + gradient
                        }).catch(() => {});
                    }

                    // Initialize gradient colors on page load
                    (function() {
                        const currentGradient = '{{ session('auth_gradient', 'indigo') }}';
                        const gradients = {
                            'indigo': { primary: '#6366f1', secondary: '#8b5cf6' },
                            'blue': { primary: '#3b82f6', secondary: '#0ea5e9' },
                            'purple': { primary: '#a855f7', secondary: '#d946ef' },
                            'pink': { primary: '#ec4899', secondary: '#f472b6' },
                            'rose': { primary: '#f43f5e', secondary: '#fb7185' },
                            'orange': { primary: '#f97316', secondary: '#fb923c' },
                            'emerald': { primary: '#10b981', secondary: '#34d399' },
                            'teal': { primary: '#14b8a6', secondary: '#2dd4bf' },
                            'cyan': { primary: '#06b6d4', secondary: '#22d3ee' },
                        };
                        const selected = gradients[currentGradient] || gradients['indigo'];
                        document.documentElement.style.setProperty('--gradient-primary', selected.primary);
                        document.documentElement.style.setProperty('--gradient-secondary', selected.secondary);
                    })();
                </script>
            </div>

            <div class="flex w-full max-w-sm flex-col gap-2">
                <main id="main-content" class="flex flex-col gap-2" role="main" aria-label="{{ __('Authentication form') }}">
                    <!-- Logo outside card -->
                    <div class="flex justify-center">
                        <a href="{{ route('home') }}" wire:navigate aria-label="{{ __('Go to homepage') }}">
                            <img src="{{ asset('inu-logo.jpg') }}" alt="{{ config('app.name', 'University') }} {{ __('Logo') }}"  width="200" height="200" />
                        </a>
                    </div>
                    <div class="auth-card rounded-xl text-stone-800 dark:text-stone-200 shadow-lg relative overflow-hidden">
                        <div class="px-4 py-3 relative z-10">
                            @yield('content')
                        </div>
                    </div>
                </main>

                <footer class="auth-footer text-center fade-in-up stagger-2 text-xs">
                    <p>&copy; {{ date('Y') }} {{ config('app.name', 'LMS') }}</p>
                </footer>
            </div>
        </div>
        @fluxScripts
    </body>
</html>
