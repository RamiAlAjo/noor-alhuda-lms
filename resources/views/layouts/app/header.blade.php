<flux:header container class="border-b border-[var(--color-accent)]/30 bg-white/80 dark:border-[var(--color-accent)]/30 dark:bg-zinc-900/80 backdrop-blur-md shadow-sm" role="banner">
    <flux:sidebar.toggle class="lg:hidden me-2 navbar-icon-btn" icon="bars-3" inset="left" aria-label="{{ __('Toggle sidebar menu') }}" />

    <x-app-logo href="{{ route('dashboard') }}" wire:navigate aria-label="{{ __('Go to dashboard') }}" />

    <flux:navbar class="-mb-px max-lg:hidden" role="navigation" aria-label="{{ __('Main navigation') }}">
        <flux:navbar.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
            {{ __('Dashboard') }}
        </flux:navbar.item>
    </flux:navbar>

    <flux:spacer />

    <flux:navbar class="me-1.5 space-x-1 rtl:space-x-reverse py-0!" role="toolbar" aria-label="{{ __('Header tools') }}">
        <!-- Accessibility Settings Dropdown -->
        <flux:dropdown position="bottom" align="end">
            <flux:button variant="ghost" size="sm" class="!h-10 navbar-icon-btn relative" icon="eye" icon-trailing="chevron-down" aria-haspopup="true" aria-label="{{ __('Accessibility settings') }}">
                @if(session('high_contrast') || session('large_text') || session('dyslexia_font') || session('reduced_motion') || session('grayscale') || session('line_spacing') || session('focus_outline'))
                    <span class="absolute top-1 rtl:left-1 ltr:right-1 w-2.5 h-2.5 bg-green-500 rounded-full border border-white dark:border-zinc-800" aria-label="{{ __('Accessibility features enabled') }}"></span>
                @endif
            </flux:button>

            <flux:menu class="w-72" role="menu" aria-label="{{ __('Accessibility options') }}">
                <div class="px-4 py-3 border-b border-zinc-200 dark:border-zinc-700 bg-gradient-to-r from-[var(--color-accent)]/5 to-transparent">
                    <p class="font-semibold text-sm">{{ __('Accessibility') }}</p>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('Customize your experience') }}</p>
                </div>

                <!-- High Contrast -->
                <button type="button"
                    onclick="toggleAccessibility('high_contrast', {{ session('high_contrast') ? 'true' : 'false' }})"
                    class="w-full px-3 py-2 text-start flex items-center justify-between hover:bg-zinc-100 dark:hover:bg-zinc-700 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-[var(--color-accent)]"
                    role="menuitem"
                    aria-pressed="{{ session('high_contrast') ? 'true' : 'false' }}"
                >
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        {{ __('High Contrast') }}
                    </span>
                    @if(session('high_contrast'))
                        <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                    @endif
                </button>

                <!-- Large Text -->
                <button type="button"
                    onclick="toggleAccessibility('large_text', {{ session('large_text') ? 'true' : 'false' }})"
                    class="w-full px-3 py-2 text-start flex items-center justify-between hover:bg-zinc-100 dark:hover:bg-zinc-700 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-[var(--color-accent)]"
                    role="menuitem"
                    aria-pressed="{{ session('large_text') ? 'true' : 'false' }}"
                >
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/></svg>
                        {{ __('Large Text') }}
                    </span>
                    @if(session('large_text'))
                        <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                    @endif
                </button>

                <!-- Dyslexia Font -->
                <button type="button"
                    onclick="toggleAccessibility('dyslexia_font', {{ session('dyslexia_font') ? 'true' : 'false' }})"
                    class="w-full px-3 py-2 text-start flex items-center justify-between hover:bg-zinc-100 dark:hover:bg-zinc-700 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-[var(--color-accent)]"
                    role="menuitem"
                    aria-pressed="{{ session('dyslexia_font') ? 'true' : 'false' }}"
                >
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/></svg>
                        {{ __('Dyslexia Font') }}
                    </span>
                    @if(session('dyslexia_font'))
                        <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                    @endif
                </button>

                <!-- Reduced Motion -->
                <button type="button"
                    onclick="toggleAccessibility('reduced_motion', {{ session('reduced_motion') ? 'true' : 'false' }})"
                    class="w-full px-3 py-2 text-start flex items-center justify-between hover:bg-zinc-100 dark:hover:bg-zinc-700 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-[var(--color-accent)]"
                    role="menuitem"
                    aria-pressed="{{ session('reduced_motion') ? 'true' : 'false' }}"
                >
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ __('Reduced Motion') }}
                    </span>
                    @if(session('reduced_motion'))
                        <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                    @endif
                </button>

                <div class="border-t border-zinc-200 dark:border-zinc-700 my-1" role="separator"></div>

                <!-- Grayscale -->
                <button type="button"
                    onclick="toggleAccessibility('grayscale', {{ session('grayscale') ? 'true' : 'false' }})"
                    class="w-full px-3 py-2 text-start flex items-center justify-between hover:bg-zinc-100 dark:hover:bg-zinc-700 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-[var(--color-accent)]"
                    role="menuitem"
                    aria-pressed="{{ session('grayscale') ? 'true' : 'false' }}"
                >
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        {{ __('Grayscale') }}
                    </span>
                    @if(session('grayscale'))
                        <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                    @endif
                </button>

                <div class="border-t border-zinc-200 dark:border-zinc-700 my-1" role="separator"></div>

                <!-- Line Spacing -->
                <button type="button"
                    onclick="toggleAccessibility('line_spacing', {{ session('line_spacing') ? 'true' : 'false' }})"
                    class="w-full px-3 py-2 text-start flex items-center justify-between hover:bg-zinc-100 dark:hover:bg-zinc-700 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-[var(--color-accent)]"
                    role="menuitem"
                    aria-pressed="{{ session('line_spacing') ? 'true' : 'false' }}"
                >
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                        {{ __('Line Spacing') }}
                    </span>
                    @if(session('line_spacing'))
                        <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                    @endif
                </button>

                <!-- Focus Outline -->
                <button type="button"
                    onclick="toggleAccessibility('focus_outline', {{ session('focus_outline') ? 'true' : 'false' }})"
                    class="w-full px-3 py-2 text-start flex items-center justify-between hover:bg-zinc-100 dark:hover:bg-zinc-700 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-[var(--color-accent)]"
                    role="menuitem"
                    aria-pressed="{{ session('focus_outline') ? 'true' : 'false' }}"
                >
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"/></svg>
                        {{ __('Focus Outline') }}
                    </span>
                    @if(session('focus_outline'))
                        <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                    @endif
                </button>

                <div class="border-t border-zinc-200 dark:border-zinc-700 my-1" role="separator"></div>

                <!-- Font Size -->
                <div class="px-4 py-3" role="group" aria-label="{{ __('Font size selection') }}">
                    <p class="text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-3">{{ __('Font Size') }}</p>
                    <div class="flex gap-1" role="radiogroup" aria-label="{{ __('Font size') }}">
                        @foreach(['small', 'medium', 'large', 'xlarge'] as $size)
                            <button type="button"
                                onclick="setFontSize('{{ $size }}')"
                                class="flex-1 py-1.5 text-xs rounded focus:outline-none focus:ring-2 focus:ring-[var(--color-accent)] {{ session('font_size', 'medium') == $size ? 'bg-[var(--color-accent)] text-white' : 'bg-gray-200 dark:bg-gray-700' }}"
                                role="radio"
                                aria-checked="{{ session('font_size', 'medium') == $size ? 'true' : 'false' }}"
                                aria-label="{{ __('Font size: :size', ['size' => $size]) }}"
                            >
                                @if($size == 'small') S @elseif($size == 'medium') M @elseif($size == 'large') L @else XL @endif
                            </button>
                        @endforeach
                    </div>
                </div>

                <div class="border-t border-zinc-200 dark:border-zinc-700 my-1" role="separator"></div>

                <!-- Reset All -->
                <button type="button"
                    onclick="resetAccessibility()"
                    class="w-full px-3 py-2 text-start text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 flex items-center gap-2 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-red-500"
                    role="menuitem"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    {{ __('Reset All') }}
                </button>
            </flux:menu>
        </flux:dropdown>

        <!-- Language Switcher -->
        <flux:dropdown position="bottom" align="end">
            <flux:button variant="ghost" size="sm" class="!h-10 navbar-icon-btn" icon="globe-alt" icon-trailing="chevron-down" aria-haspopup="true" aria-label="{{ __('Language selection') }}">
            </flux:button>

            <flux:menu class="w-72" role="menu" aria-label="{{ __('Available languages') }}">
                <div class="px-4 py-3 border-b border-zinc-200 dark:border-zinc-700 bg-gradient-to-r from-[var(--color-accent)]/5 to-transparent">
                    <p class="font-semibold text-sm">{{ __('Language') }}</p>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('Switch language') }}</p>
                </div>
                @php
                $languages = [
                    'en' => ['name' => 'English', 'flag' => '🇺🇸', 'native' => 'English'],
                    'ar' => ['name' => 'Arabic', 'flag' => '🇸🇦', 'native' => 'العربية'],
                    'fr' => ['name' => 'French', 'flag' => '🇫🇷', 'native' => 'Français'],
                    'tr' => ['name' => 'Turkish', 'flag' => '🇹🇷', 'native' => 'Türkçe'],
                    'zh' => ['name' => 'Chinese', 'flag' => '🇨🇳', 'native' => '中文'],
                    'fa' => ['name' => 'Farsi', 'flag' => '🇮🇷', 'native' => 'فارسی'],
                    'id' => ['name' => 'Indonesian', 'flag' => '🇮🇩', 'native' => 'Bahasa Indonesia'],
                    'ku' => ['name' => 'Kurdish', 'flag' => '🇮🇶', 'native' => 'کوردی'],
                    'hy' => ['name' => 'Armenian', 'flag' => '🇦🇲', 'native' => 'Հայերեն'],
                ];
                @endphp
                @foreach($languages as $code => $lang)
                    <a href="{{ route('language.switch', $code) }}" class="flex items-center gap-3 px-3 py-2.5 hover:bg-zinc-100 dark:hover:bg-zinc-700 {{ app()->getLocale() == $code ? 'bg-[var(--color-accent)]/10' : '' }} focus:outline-none focus:ring-2 focus:ring-inset focus:ring-[var(--color-accent)]" role="menuitem" {{ app()->getLocale() == $code ? 'aria-current="true"' : '' }}>
                        <span class="text-lg" aria-hidden="true">{{ $lang['flag'] }}</span>
                        <span class="flex-1">{{ $lang['native'] }}</span>
                        <span class="text-xs text-zinc-400">{{ $lang['name'] }}</span>
                        @if(app()->getLocale() == $code)
                            <svg class="w-4 h-4 text-[var(--color-accent)]" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        @endif
                    </a>
                @endforeach
            </flux:menu>
        </flux:dropdown>

        <!-- Theme Settings -->
        <flux:dropdown position="bottom" align="end">
            <flux:button variant="ghost" size="sm" class="!h-10 navbar-icon-btn relative" icon="swatch" icon-trailing="chevron-down" aria-haspopup="true" aria-label="{{ __('Theme settings') }}">
                <span class="absolute top-1.5 rtl:left-1 ltr:right-1 w-2 h-2 rounded-full border border-white dark:border-zinc-800" id="themeIndicator" style="background-color: var(--color-accent);"></span>
            </flux:button>

            <flux:menu class="w-80" role="menu" aria-label="{{ __('Theme options') }}">
                <div class="px-4 py-3 border-b border-zinc-200 dark:border-zinc-700 bg-gradient-to-r from-[var(--color-accent)]/5 to-transparent">
                    <p class="font-semibold text-sm">{{ __('Theme Settings') }}</p>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('Customize your appearance') }}</p>
                </div>

                <!-- Base Theme Selection -->
                <div class="px-4 py-3 border-b border-zinc-200 dark:border-zinc-700">
                    <p class="text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-2">{{ __('Base Theme') }}</p>
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
                    <p class="text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-2">{{ __('Accent Color') }}</p>
                    <div class="grid grid-cols-6 gap-2">
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
                                onclick="setAccentColor('{{ $name }}', '{{ $hex }}')"
                                class="theme-btn w-8 h-8 rounded-full border-2 {{ $isSelected ? 'ring-2 ring-offset-2 ring-[var(--color-accent)] border-transparent' : 'border-transparent hover:border-zinc-300 dark:hover:border-zinc-600' }} hover:scale-110 transition-all duration-200"
                                style="background-color: {{ $hex }};"
                                title="{{ ucfirst($name) }}">
                            </button>
                        @endforeach
                    </div>
                </div>

                <!-- Appearance Mode -->
                <div class="px-4 py-3">
                    <p class="text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-2">{{ __('Appearance') }}</p>
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
            </flux:menu>
        </flux:dropdown>

        <!-- Notifications -->
        <flux:tooltip :content="__('Notifications')" position="bottom">
            <flux:navbar.item class="!h-10 navbar-icon-btn focus:outline-none focus:ring-2 focus:ring-[var(--color-accent)] rounded-lg" icon="bell-alert" :href="route('reminders.index')" :label="__('Notifications')" aria-label="{{ __('View notifications') }}" />
        </flux:tooltip>

        <!-- Search -->
        <flux:tooltip :content="__('Search')" position="bottom">
            <flux:navbar.item class="!h-10 navbar-icon-btn focus:outline-none focus:ring-2 focus:ring-[var(--color-accent)] rounded-lg" icon="magnifying-glass" href="#" :label="__('Search')" aria-label="{{ __('Open search') }}" />
        </flux:tooltip>
    </flux:navbar>

    <x-desktop-user-menu />
</flux:header>

<script>
    // Apply accessibility settings from session to localStorage and DOM
    (function() {
        const accessibilitySettings = {
            'high_contrast': {{ session('high_contrast') ? 'true' : 'false' }},
            'large_text': {{ session('large_text') ? 'true' : 'false' }},
            'dyslexia_font': {{ session('dyslexia_font') ? 'true' : 'false' }},
            'reduced_motion': {{ session('reduced_motion') ? 'true' : 'false' }},
            'grayscale': {{ session('grayscale') ? 'true' : 'false' }},
            'line_spacing': {{ session('line_spacing') ? 'true' : 'false' }},
            'focus_outline': {{ session('focus_outline') ? 'true' : 'false' }},
            'font_size': '{{ session('font_size', 'medium') }}'
        };

        Object.keys(accessibilitySettings).forEach(function(key) {
            const value = accessibilitySettings[key];

            // Save to localStorage
            localStorage.setItem('accessibility_' + key, value.toString());

            // Apply to DOM
            if (key === 'font_size') {
                document.documentElement.setAttribute('data-font-size', value);
            } else if (value === true) {
                document.documentElement.setAttribute('data-' + key.replace('_', '-') + '-mode', 'true');
            }
        });

        // Apply high contrast mode
        if (accessibilitySettings.high_contrast) {
            document.documentElement.classList.add('high-contrast');
        }

        // Apply large text
        if (accessibilitySettings.large_text) {
            document.documentElement.classList.add('large-text');
        }

        // Apply dyslexia font
        if (accessibilitySettings.dyslexia_font) {
            document.documentElement.classList.add('dyslexia-font');
        }

        // Apply reduced motion
        if (accessibilitySettings.reduced_motion) {
            document.documentElement.classList.add('reduced-motion');
        }

        // Apply grayscale
        if (accessibilitySettings.grayscale) {
            document.documentElement.classList.add('grayscale');
        }

        // Apply line spacing
        if (accessibilitySettings.line_spacing) {
            document.documentElement.classList.add('line-spacing');
        }

        // Apply focus outline
        if (accessibilitySettings.focus_outline) {
            document.documentElement.classList.add('focus-outline');
        }
    })();

    // Set accent color function (used by color buttons in dropdown)
    function setAccentColor(name, hex) {
        localStorage.setItem('accent_color', name);
        document.documentElement.style.setProperty('--color-accent', hex, 'important');
        document.documentElement.setAttribute('data-theme', name);

        // Update button styles - remove selection from all buttons first
        document.querySelectorAll('.theme-btn').forEach(function(btn) {
            btn.classList.remove('ring-2', 'ring-offset-2', 'ring-[var(--color-accent)]', 'border-transparent');
            btn.classList.add('border-transparent', 'hover:border-zinc-300', 'dark:hover:border-zinc-600');
        });

        // Add selection to the clicked button
        document.querySelectorAll('.theme-btn').forEach(function(btn) {
            if (btn.title.toLowerCase() === name) {
                btn.classList.add('ring-2', 'ring-offset-2', 'ring-[var(--color-accent)]', 'border-transparent');
                btn.classList.remove('border-transparent', 'hover:border-zinc-300', 'dark:hover:border-zinc-600');
            }
        });

        // Update indicator
        var indicator = document.getElementById('themeIndicator');
        if (indicator) {
            indicator.style.backgroundColor = hex;
        }
    }

    // Initialize active color button on page load
    (function() {
        // First check localStorage, then cookie
        var savedColor = localStorage.getItem('accent_color');

        // If not in localStorage, check cookie
        if (!savedColor) {
            var cookieMatch = document.cookie.match(/accent_color=([^;]+)/);
            if (cookieMatch) {
                savedColor = cookieMatch[1];
            }
        }

        // Fallback to default
        if (!savedColor) {
            savedColor = 'zinc';
        }

        document.querySelectorAll('.theme-btn').forEach(function(btn) {
            if (btn.title.toLowerCase() === savedColor) {
                btn.classList.add('ring-2', 'ring-offset-2', 'ring-[var(--color-accent)]', 'border-transparent');
                btn.classList.remove('border-transparent', 'hover:border-zinc-300', 'dark:hover:border-zinc-600');
            }
        });
    })();

    // Toggle accessibility setting with immediate effect
    function toggleAccessibility(setting, currentValue) {
        var newValue = currentValue ? '0' : '1';
        var isEnabled = !currentValue;
        var key = setting.replace('_', '-');

        // Apply immediately to localStorage
        localStorage.setItem('accessibility_' + setting, isEnabled.toString());

        // Apply to DOM using data attributes
        if (isEnabled) {
            document.documentElement.setAttribute('data-' + key + '-mode', 'true');
        } else {
            document.documentElement.removeAttribute('data-' + key + '-mode');
        }

        // Apply high contrast class specifically
        if (setting === 'high_contrast') {
            if (isEnabled) {
                document.documentElement.classList.add('high-contrast');
            } else {
                document.documentElement.classList.remove('high-contrast');
            }
        }

        // Apply large text
        if (setting === 'large_text') {
            if (isEnabled) {
                document.documentElement.classList.add('large-text');
            } else {
                document.documentElement.classList.remove('large-text');
            }
        }

        // Apply dyslexia font
        if (setting === 'dyslexia_font') {
            if (isEnabled) {
                document.documentElement.classList.add('dyslexia-font');
            } else {
                document.documentElement.classList.remove('dyslexia-font');
            }
        }

        // Apply reduced motion
        if (setting === 'reduced_motion') {
            if (isEnabled) {
                document.documentElement.classList.add('reduced-motion');
            } else {
                document.documentElement.classList.remove('reduced-motion');
            }
        }

        // Apply grayscale
        if (setting === 'grayscale') {
            if (isEnabled) {
                document.documentElement.classList.add('grayscale');
            } else {
                document.documentElement.classList.remove('grayscale');
            }
        }

        // Apply line spacing
        if (setting === 'line_spacing') {
            if (isEnabled) {
                document.documentElement.classList.add('line-spacing');
            } else {
                document.documentElement.classList.remove('line-spacing');
            }
        }

        // Apply focus outline
        if (setting === 'focus_outline') {
            if (isEnabled) {
                document.documentElement.classList.add('focus-outline');
            } else {
                document.documentElement.classList.remove('focus-outline');
            }
        }

        // Submit to server via fetch
        var formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append(setting, newValue);

        fetch('{{ route("settings.accessibility.toggle") }}', {
            method: 'POST',
            body: formData
        }).then(function(response) {
            window.location.reload();
        }).catch(function(error) {
            console.error('Error saving setting:', error);
        });
    }

    // Set font size
    function setFontSize(size) {
        // Apply immediately to localStorage
        localStorage.setItem('accessibility_font_size', size);

        // Apply to DOM
        document.documentElement.setAttribute('data-font-size', size);

        // Submit to server via fetch
        var formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('font_size', size);

        fetch('{{ route("settings.accessibility.toggle") }}', {
            method: 'POST',
            body: formData
        }).then(function(response) {
            window.location.reload();
        }).catch(function(error) {
            console.error('Error saving setting:', error);
        });
    }

    // Reset all accessibility settings
    function resetAccessibility() {
        var settings = ['high_contrast', 'large_text', 'dyslexia_font', 'reduced_motion', 'grayscale', 'line_spacing', 'focus_outline', 'font_size'];

        // Clear from localStorage and DOM
        settings.forEach(function(setting) {
            localStorage.removeItem('accessibility_' + setting);
            document.documentElement.removeAttribute('data-' + setting.replace('_', '-') + '-mode');
            document.documentElement.classList.remove(setting.replace('_', '-'));
        });
        document.documentElement.removeAttribute('data-font-size');

        // Submit reset to server via fetch
        var formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');

        fetch('{{ route("settings.accessibility.reset") }}', {
            method: 'POST',
            body: formData
        }).then(function(response) {
            window.location.reload();
        }).catch(function(error) {
            console.error('Error resetting settings:', error);
        });
    }

    // Set base theme function
    function setBaseTheme(themeName) {
        // Remove active state from all theme buttons
        document.querySelectorAll('.base-theme-btn').forEach(function(btn) {
            btn.classList.remove('ring-2', 'ring-offset-2', 'ring-[var(--color-accent)]', 'border-zinc-500', 'dark:border-zinc-400');
            btn.classList.add('border-zinc-300', 'dark:border-zinc-600');
        });

        // Add active state to clicked button
        var clickedBtn = event.target.closest('.base-theme-btn');
        if (clickedBtn) {
            clickedBtn.classList.add('ring-2', 'ring-offset-2', 'ring-[var(--color-accent)]', 'border-zinc-500', 'dark:border-zinc-400');
            clickedBtn.classList.remove('border-zinc-300', 'dark:border-zinc-600');
        }

        // Set data attribute on document element
        document.documentElement.setAttribute('data-base-theme', themeName);

        // Save to localStorage
        localStorage.setItem('base_theme', themeName);

        // Submit to server via fetch
        var formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('base_theme', themeName);

        fetch('{{ route("settings.theme.switch") }}', {
            method: 'POST',
            body: formData
        }).then(function(response) {
            if (!response.ok) {
                console.error('Error saving base theme');
            }
        }).catch(function(error) {
            console.error('Error saving base theme:', error);
        });
    }

    // Set appearance mode function
    function setAppearance(mode) {
        // Remove active state from all appearance buttons
        document.querySelectorAll('.navbar .flex.gap-2 button').forEach(function(btn) {
            btn.classList.remove('bg-[var(--color-accent)]', 'text-white');
            btn.classList.add('bg-gray-200', 'dark:bg-zinc-700', 'hover:bg-gray-300', 'dark:hover:bg-zinc-600', 'text-gray-700', 'dark:text-gray-200');
        });

        // Add active state to clicked button
        event.target.classList.add('bg-[var(--color-accent)]', 'text-white');
        event.target.classList.remove('bg-gray-200', 'dark:bg-zinc-700', 'hover:bg-gray-300', 'dark:hover:bg-zinc-600', 'text-gray-700', 'dark:text-gray-200');

        // Apply appearance immediately
        if (mode === 'light') {
            document.documentElement.classList.remove('dark');
            document.documentElement.setAttribute('data-flux-theme', 'light');
        } else if (mode === 'dark') {
            document.documentElement.classList.add('dark');
            document.documentElement.setAttribute('data-flux-theme', 'dark');
        } else if (mode === 'system') {
            const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            if (systemPrefersDark) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
            document.documentElement.setAttribute('data-flux-theme', 'system');
        }

        // Save to localStorage
        localStorage.setItem('flux_appearance', mode);

        // Submit to server via fetch
        var formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('flux_appearance', mode);

        fetch('{{ route("settings.theme.switch") }}', {
            method: 'POST',
            body: formData
        }).then(function(response) {
            if (!response.ok) {
                console.error('Error saving appearance');
            }
        }).catch(function(error) {
            console.error('Error saving appearance:', error);
        });
    }

    // Initialize base theme on page load
    (function() {
        // Check localStorage first, then cookie
        var savedBaseTheme = localStorage.getItem('base_theme');

        // If not in localStorage, check cookie
        if (!savedBaseTheme) {
            var cookieMatch = document.cookie.match(/base_theme=([^;]+)/);
            if (cookieMatch) {
                savedBaseTheme = cookieMatch[1];
            }
        }

        // Fallback to default
        if (!savedBaseTheme) {
            savedBaseTheme = 'default-dark';
        }

        // Apply the theme
        document.documentElement.setAttribute('data-base-theme', savedBaseTheme);

        // Set active button state
        document.querySelectorAll('.base-theme-btn').forEach(function(btn) {
            var btnTitle = btn.title.toLowerCase().replace(' ', '-');
            if (btnTitle === savedBaseTheme || (btnTitle === 'light' && savedBaseTheme === 'default-light') || (btnTitle === 'dark' && savedBaseTheme === 'default-dark')) {
                btn.classList.add('ring-2', 'ring-offset-2', 'ring-[var(--color-accent)]', 'border-zinc-500', 'dark:border-zinc-400');
                btn.classList.remove('border-zinc-300', 'dark:border-zinc-600');
            }
        });
    })();
</script>
