<?php

use App\Models\UserSetting;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

new class extends Component {
    public string $theme = 'light';
    public string $locale = 'en';
    public string $accent_color = 'blue';

    // Dark mode settings
    public bool $dark_mode = false;
    public bool $system_theme_detection = true;

    // Notification settings
    public bool $notification_sound = true;

    // Accessibility settings
    public bool $high_contrast = false;
    public bool $large_text = false;
    public bool $dyslexia_font = false;
    public bool $reduced_motion = false;
    public bool $grayscale = false;
    public bool $strong_focus_outline = false;
    public float $line_spacing = 1.5;

    // Font settings
    public string $font_face = 'sans-serif';
    public int $font_size = 16;

    // Background gradient settings
    public string $light_gradient = 'indigo';
    public string $dark_gradient = 'indigo';

    // Available accent colors (FluxUI.dev inspired)
    protected array $accentColors = [
        'blue' => '#3b82f6',
        'cyan' => '#06b6d4',
        'emerald' => '#10b981',
        'green' => '#22c55e',
        'indigo' => '#6366f1',
        'lime' => '#84cc16',
        'orange' => '#f97316',
        'pink' => '#ec4899',
        'purple' => '#a855f7',
        'red' => '#ef4444',
        'rose' => '#f43f5e',
        'sky' => '#0ea5e9',
        'slate' => '#64748b',
        'stone' => '#78716c',
        'teal' => '#14b8a6',
        'violet' => '#8b5cf6',
        'yellow' => '#eab308',
        'zinc' => '#71717a',
    ];

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $user = Auth::user();

        // Load or create settings
        if (!$user->settings) {
            $user->settings()->create([]);
            $user->refresh();
        }

        $settings = $user->settings;

        $this->theme = $settings->theme ?? 'light';
        $this->locale = $settings->locale ?? 'en';
        $this->accent_color = $this->extractAccentColor($settings->background_color ?? '#3b82f6');

        // Dark mode settings
        $this->dark_mode = $settings->dark_mode ?? false;
        $this->system_theme_detection = $settings->system_theme_detection ?? true;

        // Notification settings
        $this->notification_sound = $settings->notification_sound ?? true;

        // Accessibility
        $this->high_contrast = $settings->high_contrast ?? false;
        $this->large_text = $settings->large_text ?? false;
        $this->dyslexia_font = $settings->dyslexia_font ?? false;
        $this->reduced_motion = $settings->reduced_motion ?? false;
        $this->grayscale = $settings->grayscale ?? false;
        $this->strong_focus_outline = $settings->strong_focus_outline ?? false;
        $this->line_spacing = $settings->line_spacing ?? 1.5;

        // Font
        $this->font_face = $settings->font_face ?? 'sans-serif';
        $this->font_size = $settings->font_size ?? 16;

        // Gradients
        $this->light_gradient = $settings->light_gradient ?? 'indigo';
        $this->dark_gradient = $settings->dark_gradient ?? 'indigo';
    }

    /**
     * Extract accent color from background color.
     */
    protected function extractAccentColor(string $color): string
    {
        $color = strtolower($color);
        foreach ($this->accentColors as $name => $hex) {
            if (strtolower($hex) === $color) {
                return $name;
            }
        }
        return 'blue';
    }

    /**
     * Get accent colors for display.
     */
    public function getAccentColors(): array
    {
        return $this->accentColors;
    }

    /**
     * Get available gradients.
     */
    public function getGradients(): array
    {
        return [
            'indigo' => ['from-indigo-50 via-white to-purple-50', '#6366f1', '#8b5cf6'],
            'blue' => ['from-blue-50 via-white to-cyan-50', '#3b82f6', '#0ea5e9'],
            'purple' => ['from-purple-50 via-pink-50 to-rose-50', '#a855f7', '#d946ef'],
            'pink' => ['from-pink-50 via-rose-50 to-orange-50', '#ec4899', '#f472b6'],
            'rose' => ['from-rose-50 via-red-50 to-orange-50', '#f43f5e', '#fb7185'],
            'orange' => ['from-orange-50 via-amber-50 to-yellow-50', '#f97316', '#fb923c'],
            'emerald' => ['from-emerald-50 via-teal-50 to-cyan-50', '#10b981', '#34d399'],
            'teal' => ['from-teal-50 via-green-50 to-emerald-50', '#14b8a6', '#2dd4bf'],
            'cyan' => ['from-cyan-50 via-sky-50 to-blue-50', '#06b6d4', '#22d3ee'],
            'slate' => ['from-slate-50 via-gray-50 to-zinc-50', '#64748b', '#94a3b8'],
            'neutral' => ['from-neutral-50 via-white to-neutral-50', '#737373', '#a3a3a3'],
            'stone' => ['from-stone-50 via-orange-50 to-stone-50', '#78716c', '#a8a29e'],
            // Custom gradients
            'red_black' => ['from-[#ff2e63] via-[#ff2e63]/50 to-black', '#ff2e63', '#000000'],
            'lightblue_blue' => ['from-[#c7d2fe] via-[#3ab0fe]/50 to-[#3ab0fe]', '#c7d2fe', '#3ab0fe'],
            'gray_orange' => ['from-[#1f2937] via-[#ffa77f]/50 to-[#ffa77f]', '#1f2937', '#ffa77f'],
            'green_black' => ['from-[#00ff88] via-[#00ff88]/50 to-black', '#00ff88', '#0b0b0b'],
            'purple_black' => ['from-[#5c2cf4] via-[#5c2cf4]/50 to-[#040405]', '#5c2cf4', '#040405'],
            'purple_yellow' => ['from-[#6a00ff] via-[#f7ff00]/50 to-[#f7ff00]', '#6a00ff', '#f7ff00'],
            'teal_white' => ['from-[#007f5f] via-[#faf9f6]/50 to-[#faf9f6]', '#007f5f', '#faf9f6'],
            'red_white' => ['from-[#d90429] via-white to-white', '#d90429', '#ffffff'],
        ];
    }

    /**
     * Get dark gradients.
     */
    public function getDarkGradients(): array
    {
        return [
            'indigo' => ['from-indigo-950 via-purple-950/30 to-zinc-900', '#6366f1', '#8b5cf6'],
            'blue' => ['from-blue-950 via-cyan-950/30 to-zinc-900', '#3b82f6', '#0ea5e9'],
            'purple' => ['from-purple-950 via-pink-950/30 to-zinc-900', '#a855f7', '#d946ef'],
            'pink' => ['from-pink-950 via-rose-950/30 to-zinc-900', '#ec4899', '#f472b6'],
            'rose' => ['from-rose-950 via-red-950/30 to-zinc-900', '#f43f5e', '#fb7185'],
            'orange' => ['from-orange-950 via-amber-950/30 to-zinc-900', '#f97316', '#fb923c'],
            'emerald' => ['from-emerald-950 via-teal-950/30 to-zinc-900', '#10b981', '#34d399'],
            'teal' => ['from-teal-950 via-green-950/30 to-zinc-900', '#14b8a6', '#2dd4bf'],
            'cyan' => ['from-cyan-950 via-sky-950/30 to-zinc-900', '#06b6d4', '#22d3ee'],
            'slate' => ['from-slate-950 via-gray-950/30 to-zinc-900', '#64748b', '#94a3b8'],
            'neutral' => ['from-neutral-950 via-neutral-900/30 to-zinc-900', '#737373', '#a3a3a3'],
            'stone' => ['from-stone-950 via-orange-950/30 to-zinc-900', '#78716c', '#a8a29e'],
            // Custom dark gradients
            'red_black' => ['from-[#ff2e63] via-black/80 to-black', '#ff2e63', '#000000'],
            'lightblue_blue' => ['from-[#3ab0fe] via-[#3ab0fe]/80 to-[#3ab0fe]', '#3ab0fe', '#3ab0fe'],
            'gray_orange' => ['from-[#1f2937] via-[#ffa77f]/80 to-[#ffa77f]', '#1f2937', '#ffa77f'],
            'green_black' => ['from-[#00ff88] via-black/80 to-black', '#00ff88', '#0b0b0b'],
            'purple_black' => ['from-[#5c2cf4] via-[#040405]/80 to-[#040405]', '#5c2cf4', '#040405'],
            'purple_yellow' => ['from-[#6a00ff] via-[#f7ff00]/80 to-[#f7ff00]', '#6a00ff', '#f7ff00'],
            'teal_white' => ['from-[#007f5f] via-[#faf9f6]/80 to-[#faf9f6]', '#007f5f', '#faf9f6'],
            'red_white' => ['from-[#d90429] via-white/80 to-white', '#d90429', '#ffffff'],
        ];
    }

    /**
     * Toggle dark mode.
     */
    public function toggleDarkMode(): void
    {
        $this->dark_mode = !$this->dark_mode;
        $this->saveAppearance();

        // Dispatch event for JavaScript to update the UI
        $this->dispatch('dark-mode-toggled', dark_mode: $this->dark_mode);
    }

    /**
     * Save appearance settings.
     */
    public function saveAppearance(): void
    {
        $user = Auth::user();

        $user->settings()->update([
            'theme' => $this->theme,
            'locale' => $this->locale,
            'dark_mode' => $this->dark_mode,
            'system_theme_detection' => $this->system_theme_detection,
            'notification_sound' => $this->notification_sound,
            'high_contrast' => $this->high_contrast,
            'large_text' => $this->large_text,
            'dyslexia_font' => $this->dyslexia_font,
            'reduced_motion' => $this->reduced_motion,
            'grayscale' => $this->grayscale,
            'strong_focus_outline' => $this->strong_focus_outline,
            'line_spacing' => $this->line_spacing,
            'font_face' => $this->font_face,
            'font_size' => $this->font_size,
            'background_color' => $this->accentColors[$this->accent_color] ?? '#3b82f6',
            'light_gradient' => $this->light_gradient,
            'dark_gradient' => $this->dark_gradient,
        ]);

        $this->dispatch('appearance-updated', [
            'high_contrast' => $this->high_contrast,
            'large_text' => $this->large_text,
            'dyslexia_font' => $this->dyslexia_font,
            'reduced_motion' => $this->reduced_motion,
            'grayscale' => $this->grayscale,
            'strong_focus_outline' => $this->strong_focus_outline,
            'line_spacing' => $this->line_spacing,
            'font_face' => $this->font_face,
            'font_size' => $this->font_size,
        ]);
    }
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <flux:heading class="sr-only">{{ __('Appearance Settings') }}</flux:heading>

    <x-pages::settings.layout :heading="__('Appearance')" :subheading="__('Customize your visual experience with themes and accessibility options')">
        <form wire:submit="saveAppearance" class="space-y-8">
            <!-- Dark Mode Toggle -->
            <div class="space-y-4 p-4 rounded-xl bg-gradient-to-r from-indigo-50 to-purple-50 dark:from-indigo-950/30 dark:to-purple-950/30 border border-indigo-100 dark:border-indigo-900">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-indigo-100 dark:bg-indigo-900/50 flex items-center justify-center">
                            <flux:icon.moon class="w-5 h-5 text-indigo-600 dark:text-indigo-400" />
                        </div>
                        <div>
                            <flux:heading level="3" size="sm">{{ __('Dark Mode') }}</flux:heading>
                            <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400">{{ __('Toggle dark theme for better visibility in low light') }}</flux:text>
                        </div>
                    </div>
                    <flux:switch
                        wire:model="dark_mode"
                        wire:change="toggleDarkMode"
                        :label="__('Dark Mode')"
                        aria-label="{{ __('Toggle dark mode') }}"
                    />
                </div>

                <!-- System Preference Detection -->
                <div class="flex items-center justify-between pt-3 border-t border-indigo-100 dark:border-indigo-900">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-purple-100 dark:bg-purple-900/50 flex items-center justify-center">
                            <flux:icon.computer-desktop class="w-5 h-5 text-purple-600 dark:text-purple-400" />
                        </div>
                        <div>
                            <flux:heading level="3" size="sm">{{ __('System Preference') }}</flux:heading>
                            <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400">{{ __('Automatically follow system dark mode setting') }}</flux:text>
                        </div>
                    </div>
                    <flux:switch
                        wire:model="system_theme_detection"
                        :label="__('System Preference Detection')"
                        aria-label="{{ __('Toggle system preference detection') }}"
                    />
                </div>
            </div>

            <!-- Theme Selection -->
            <div class="space-y-4">
                <flux:heading level="3" size="sm">{{ __('Theme') }}</flux:heading>
                <flux:radio.group x-data variant="segmented" x-model="$flux.appearance">
                    <flux:radio value="light" icon="sun">{{ __('Light') }}</flux:radio>
                    <flux:radio value="dark" icon="moon">{{ __('Dark') }}</flux:radio>
                    <flux:radio value="system" icon="computer-desktop">{{ __('System') }}</flux:radio>
                </flux:radio.group>
            </div>

            <!-- Accent Color Selection -->
            <div class="space-y-4">
                <flux:heading level="3" size="sm">{{ __('Accent Color') }}</flux:heading>
                <div class="flex flex-wrap gap-3">
                    @foreach($this->getAccentColors() as $name => $hex)
                        <button
                            type="button"
                            wire:click="$set('accent_color', '{{ $name }}')"
                            class="w-8 h-8 rounded-full border-2 transition-all duration-200 hover:scale-110 focus:outline-none focus:ring-2 focus:ring-offset-2"
                            :class="($wire.accent_color === '{{ $name }}') ? 'border-stone-900 dark:border-white ring-2 ring-offset-2' : 'border-transparent'"
                            style="background-color: {{ $hex }};"
                            title="{{ ucfirst($name) }}"
                            aria-label="{{ __('Select accent color: ') . ucfirst($name) }}"
                        >
                            @if($accent_color === $name)
                                <flux:icon.check class="w-4 h-4 mx-auto text-white drop-shadow-md" />
                            @endif
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Light Theme Gradient -->
            <div class="space-y-4">
                <flux:heading level="3" size="sm">{{ __('Light Theme Background') }}</flux:heading>
                <div class="flex flex-wrap gap-2">
                    @foreach($this->getGradients() as $key => $colors)
                        <button
                            type="button"
                            wire:click="$set('light_gradient', '{{ $key }}')"
                            class="w-10 h-10 rounded-lg border-2 transition-all duration-200 hover:scale-110 focus:outline-none focus:ring-2 focus:ring-offset-2"
                            :class="($wire.light_gradient === '{{ $key }}') ? 'border-stone-900 dark:border-white ring-2 ring-offset-2' : 'border-stone-200'"
                            style="background: linear-gradient(135deg, {{ $colors[1] }} 0%, {{ $colors[2] }} 100%)"
                            title="{{ ucfirst($key) }}"
                            aria-label="{{ __('Select light gradient: ') . ucfirst($key) }}"
                        >
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Dark Theme Gradient -->
            <div class="space-y-4">
                <flux:heading level="3" size="sm">{{ __('Dark Theme Background') }}</flux:heading>
                <div class="flex flex-wrap gap-2">
                    @foreach($this->getDarkGradients() as $key => $colors)
                        <button
                            type="button"
                            wire:click="$set('dark_gradient', '{{ $key }}')"
                            class="w-10 h-10 rounded-lg border-2 transition-all duration-200 hover:scale-110 focus:outline-none focus:ring-2 focus:ring-offset-2"
                            :class="($wire.dark_gradient === '{{ $key }}') ? 'border-stone-900 dark:border-white ring-2 ring-offset-2' : 'border-stone-700'"
                            style="background: linear-gradient(135deg, {{ $colors[1] }} 0%, {{ $colors[2] }} 100%)"
                            title="{{ ucfirst($key) }}"
                            aria-label="{{ __('Select dark gradient: ') . ucfirst($key) }}"
                        >
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Language Selection -->
            <div class="space-y-4">
                <flux:heading level="3" size="sm">{{ __('Language') }}</flux:heading>
                <flux:select wire:model="locale" class="w-full">
                    <flux:select.option value="en">English</flux:select.option>
                    <flux:select.option value="ar">العربية (Arabic)</flux:select.option>
                </flux:select>
            </div>

            <!-- Notification Settings -->
            <div class="space-y-4 p-4 rounded-xl bg-gradient-to-r from-blue-50 to-cyan-50 dark:from-blue-950/30 dark:to-cyan-950/30 border border-blue-100 dark:border-blue-900">
                <flux:heading level="3" size="sm">{{ __('Notification Settings') }}</flux:heading>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900/50 flex items-center justify-center">
                            <flux:icon.bell class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                        </div>
                        <div>
                            <flux:heading level="4" size="sm">{{ __('Notification Sounds') }}</flux:heading>
                            <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400">{{ __('Play a sound when you receive a new notification') }}</flux:text>
                        </div>
                    </div>
                    <flux:switch
                        wire:model="notification_sound"
                        :label="__('Notification Sounds')"
                        aria-label="{{ __('Toggle notification sounds') }}"
                    />
                </div>
            </div>

            <!-- Accessibility Options -->
            <div class="space-y-4">
                <flux:heading level="3" size="sm">{{ __('Accessibility') }}</flux:heading>

                <div class="space-y-3">
                    <flux:switch wire:model="high_contrast" wire:change="saveAppearance" :label="__('High Contrast Mode')" description="{{ __('Increase contrast for better visibility') }}" />

                    <flux:switch wire:model="large_text" wire:change="saveAppearance" :label="__('Large Text')" description="{{ __('Increase default text size') }}" />

                    <flux:switch wire:model="dyslexia_font" wire:change="saveAppearance" :label="__('Dyslexia-Friendly Font')" description="{{ __('Use OpenDyslexic font for easier reading') }}" />

                    <flux:switch wire:model="reduced_motion" wire:change="saveAppearance" :label="__('Reduced Motion')" description="{{ __('Minimize animations and transitions') }}" />

                    <flux:switch wire:model="grayscale" wire:change="saveAppearance" :label="__('Grayscale Mode')" description="{{ __('Remove colors for simpler visual experience') }}" />

                    <flux:switch wire:model="strong_focus_outline" wire:change="saveAppearance" :label="__('Strong Focus Outline')" description="{{ __('Enhanced focus indicators for keyboard navigation') }}" />
                </div>
            </div>

            <!-- Font Settings -->
            <div class="space-y-4">
                <flux:heading level="3" size="sm">{{ __('Font Settings') }}</flux:heading>

                <flux:select wire:model="font_face" wire:change="saveAppearance" :label="__('Font Family')" class="w-full">
                    <flux:select.option value="sans-serif">{{ __('Sans Serif (Default)') }}</flux:select.option>
                    <flux:select.option value="serif">{{ __('Serif') }}</flux:select.option>
                    <flux:select.option value="monospace">{{ __('Monospace') }}</flux:select.option>
                    <flux:select.option value="dyslexic">{{ __('Dyslexic (OpenDyslexic)') }}</flux:select.option>
                </flux:select>

                <flux:select wire:model="font_size" wire:change="saveAppearance" :label="__('Font Size')" class="w-full">
                    <flux:select.option value="12">{{ __('Small (12px)') }}</flux:select.option>
                    <flux:select.option value="14">{{ __('Medium (14px)') }}</flux:select.option>
                    <flux:select.option value="16">{{ __('Normal (16px)') }}</flux:select.option>
                    <flux:select.option value="18">{{ __('Large (18px)') }}</flux:select.option>
                    <flux:select.option value="20">{{ __('Extra Large (20px)') }}</flux:select.option>
                    <flux:select.option value="24">{{ __('Huge (24px)') }}</flux:select.option>
                </flux:select>

                <flux:select wire:model="line_spacing" wire:change="saveAppearance" :label="__('Line Spacing')" class="w-full">
                    <flux:select.option value="1.2">{{ __('Compact (1.2)') }}</flux:select.option>
                    <flux:select.option value="1.5">{{ __('Normal (1.5)') }}</flux:select.option>
                    <flux:select.option value="1.8">{{ __('Relaxed (1.8)') }}</flux:select.option>
                    <flux:select.option value="2.0">{{ __('Spacious (2.0)') }}</flux:select.option>
                </flux:select>
            </div>

            <!-- Save Button -->
            <div class="flex items-center gap-4 pt-4 border-t border-stone-200 dark:border-stone-700">
                <x-button.submit loading-text="Saving Appearance..." variant="primary" class="w-full">
                    Save Appearance
                </x-button.submit>

                <x-action-message class="me-3" on="appearance-updated">
                    {{ __('Saved.') }}
                </x-action-message>
            </div>
        </form>
    </x-pages::settings.layout>
</section>
