<!-- =====================================================
     THEME SETTINGS INITIALIZATION
     Used by: Welcome page (welcome.blade.php)
     Description: JavaScript that initializes theme and accessibility
     settings from localStorage on the welcome/landing page
===================================================== -->
<!-- Global Theme and Accessibility Settings Initialization -->
<script>
(function() {
    'use strict';

    // Define accent colors mapping with accessible contrast ratios
    const accentColors = {
        'red': '#ef4444', 'orange': '#f97316', 'amber': '#f59e0b', 'yellow': '#eab308',
        'lime': '#84cc16', 'green': '#22c55e', 'emerald': '#10b981', 'teal': '#14b8a6',
        'cyan': '#06b6d4', 'sky': '#0ea5e9', 'blue': '#3b82f6', 'indigo': '#6366f1',
        'violet': '#8b5cf6', 'purple': '#a855f7', 'fuchsia': '#d946ef', 'pink': '#ec4899',
        'rose': '#f43f5e', 'slate': '#64748b', 'gray': '#6b7280', 'zinc': '#71717a',
        'neutral': '#737373', 'stone': '#78716c'
    };

    // Base themes configuration with enhanced visual properties
    const baseThemes = {
        'default-light': {
            name: 'Classic Light',
            background: '#ffffff',
            surface: '#f5f5f4',
            text: '#1c1917',
            border: '#e7e5e4',
            primary: '#6366f1',
            secondary: '#8b5cf6',
            accent: '#a855f7',
            isDark: false,
            contrastRatio: 'AAA',
            cardBg: '#ffffff',
            cardBorder: '#e7e5e4',
            cardShadow: 'rgba(0, 0, 0, 0.08)',
            navBg: '#ffffff',
            navBorder: '#e7e5e4',
            inputBg: '#ffffff',
            inputBorder: '#e7e5e4',
            buttonBg: '#6366f1',
            buttonText: '#ffffff',
            glow: false,
            gradientStart: '#667eea',
            gradientEnd: '#764ba2'
        },
        'default-dark': {
            name: 'Classic Dark',
            background: '#0c0a09',
            surface: '#1c1917',
            text: '#fafaf9',
            border: '#292524',
            primary: '#818cf8',
            secondary: '#a78bfa',
            accent: '#c084fc',
            isDark: true,
            contrastRatio: 'AAA',
            cardBg: '#292524',
            cardBorder: '#44403c',
            cardShadow: 'rgba(0, 0, 0, 0.3)',
            navBg: '#1c1917',
            navBorder: '#44403c',
            inputBg: '#292524',
            inputBorder: '#44403c',
            buttonBg: '#6366f1',
            buttonText: '#ffffff',
            glow: false,
            gradientStart: '#4f46e5',
            gradientEnd: '#7c3aed'
        },
        'cyberpunk-neon': {
            name: 'Cyber Neon',
            background: '#1a0a2e',
            surface: '#2d1b4e',
            text: '#e0e0e0',
            border: '#ff00ff',
            primary: '#ff00ff',
            secondary: '#00ffff',
            accent: '#ffff00',
            isDark: true,
            glow: true,
            contrastRatio: 'AA',
            cardBg: '#1a0a2e',
            cardBorder: '#ff00ff',
            cardShadow: 'rgba(255, 0, 255, 0.3)',
            navBg: '#1a0a2e',
            navBorder: '#ff00ff',
            inputBg: '#2d1b4e',
            inputBorder: '#ff00ff',
            buttonBg: '#ff00ff',
            buttonText: '#000000',
            gradientStart: '#ff00ff',
            gradientEnd: '#00ffff'
        },
        'cyberpunk-dark': {
            name: 'Cyber Dark',
            background: '#000000',
            surface: '#0a0a0a',
            text: '#00ffff',
            border: '#00ffff',
            primary: '#00ffff',
            secondary: '#ff00ff',
            accent: '#00ff00',
            isDark: true,
            glow: true,
            contrastRatio: 'AA',
            cardBg: '#000000',
            cardBorder: '#00ffff',
            cardShadow: 'rgba(0, 255, 255, 0.2)',
            navBg: '#000000',
            navBorder: '#00ffff',
            inputBg: '#0a0a0a',
            inputBorder: '#00ffff',
            buttonBg: '#00ffff',
            buttonText: '#000000',
            gradientStart: '#000000',
            gradientEnd: '#001a1a'
        },
        'synthwave': {
            name: 'Synthwave',
            background: '#1a0a1a',
            surface: '#2d1b2e',
            text: '#ff71ce',
            border: '#ff71ce',
            primary: '#ff71ce',
            secondary: '#01cdfe',
            accent: '#05ffa1',
            isDark: true,
            contrastRatio: 'AA',
            cardBg: '#1a0a1a',
            cardBorder: '#ff71ce',
            cardShadow: 'rgba(255, 113, 206, 0.25)',
            navBg: '#1a0a1a',
            navBorder: '#ff71ce',
            inputBg: '#2d1b2e',
            inputBorder: '#ff71ce',
            buttonBg: '#ff71ce',
            buttonText: '#1a0a1a',
            gradientStart: '#ff71ce',
            gradientEnd: '#01cdfe'
        },
        'midnight': {
            name: 'Midnight',
            background: '#0f172a',
            surface: '#1e293b',
            text: '#e2e8f0',
            border: '#334155',
            primary: '#60a5fa',
            secondary: '#818cf8',
            accent: '#a78bfa',
            isDark: true,
            contrastRatio: 'AAA',
            cardBg: '#0f172a',
            cardBorder: '#334155',
            cardShadow: 'rgba(0, 0, 0, 0.4)',
            navBg: '#0f172a',
            navBorder: '#334155',
            inputBg: '#1e293b',
            inputBorder: '#334155',
            buttonBg: '#3b82f6',
            buttonText: '#ffffff',
            gradientStart: '#1e3a5f',
            gradientEnd: '#0f172a'
        },
        'dracula': {
            name: 'Dracula',
            background: '#21222c',
            surface: '#282a36',
            text: '#f8f8f2',
            border: '#44475a',
            primary: '#bd93f9',
            secondary: '#ff79c6',
            accent: '#50fa7b',
            isDark: true,
            contrastRatio: 'AAA',
            cardBg: '#21222c',
            cardBorder: '#44475a',
            cardShadow: 'rgba(0, 0, 0, 0.3)',
            navBg: '#21222c',
            navBorder: '#44475a',
            inputBg: '#282a36',
            inputBorder: '#44475a',
            buttonBg: '#bd93f9',
            buttonText: '#282a36',
            gradientStart: '#44475a',
            gradientEnd: '#21222c'
        },
        'nord': {
            name: 'Nord',
            background: '#2e3440',
            surface: '#3b4252',
            text: '#eceff4',
            border: '#4c566a',
            primary: '#88c0d0',
            secondary: '#81a1c1',
            accent: '#b48ead',
            isDark: true,
            contrastRatio: 'AAA',
            cardBg: '#2e3440',
            cardBorder: '#4c566a',
            cardShadow: 'rgba(0, 0, 0, 0.25)',
            navBg: '#2e3440',
            navBorder: '#4c566a',
            inputBg: '#3b4252',
            inputBorder: '#4c566a',
            buttonBg: '#5e81ac',
            buttonText: '#eceff4',
            gradientStart: '#3b4252',
            gradientEnd: '#2e3440'
        },
    };

    const htmlElement = document.documentElement;

    // Helper: Announce changes to screen readers
    function announceToScreenReader(message) {
        const announcement = document.createElement('div');
        announcement.setAttribute('role', 'status');
        announcement.setAttribute('aria-live', 'polite');
        announcement.setAttribute('aria-atomic', 'true');
        announcement.className = 'sr-only';
        announcement.style.cssText = 'position:absolute;left:-9999px;width:1px;height:1px;overflow:hidden;';
        announcement.textContent = message;
        document.body.appendChild(announcement);
        setTimeout(function() { announcement.remove(); }, 1000);
    }

    // Helper: Check system preference for reduced motion
    function prefersReducedMotion() {
        return window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    }

    // Helper: Check system preference for color scheme
    function getSystemColorScheme() {
        if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
            return 'dark';
        }
        return 'light';
    }

    // Helper: Check for high contrast mode
    function prefersHighContrast() {
        return window.matchMedia('(prefers-contrast: more)').matches;
    }

    // Load accent color: localStorage > data-theme attribute > default
    const savedColor = localStorage.getItem('accent_color') || htmlElement.getAttribute('data-theme') || 'zinc';
    if (accentColors[savedColor]) {
        const hexColor = accentColors[savedColor];
        htmlElement.setAttribute('data-theme', savedColor);
        htmlElement.setAttribute('data-accent-color', savedColor);
        htmlElement.style.setProperty('--color-accent', hexColor, 'important');
        htmlElement.style.setProperty('--color-accent-light', hexColor + '80', 'important');
    }

    // Load base theme or detect system preference
    let savedBaseTheme = localStorage.getItem('base_theme');

    // If no saved theme, use system preference
    if (!savedBaseTheme) {
        savedBaseTheme = getSystemColorScheme() === 'dark' ? 'default-dark' : 'default-light';
    }

    if (savedBaseTheme && baseThemes[savedBaseTheme]) {
        htmlElement.setAttribute('data-base-theme', savedBaseTheme);
        const theme = baseThemes[savedBaseTheme];

        if (theme.glow) {
            htmlElement.setAttribute('data-theme-glow', 'true');
        }

        // Apply base theme CSS variables
        htmlElement.style.setProperty('--theme-background', theme.background);
        htmlElement.style.setProperty('--theme-surface', theme.surface);
        htmlElement.style.setProperty('--theme-text', theme.text);
        htmlElement.style.setProperty('--theme-border', theme.border);

        // Apply enhanced theme variables
        htmlElement.style.setProperty('--theme-primary', theme.primary);
        htmlElement.style.setProperty('--theme-secondary', theme.secondary);
        htmlElement.style.setProperty('--theme-accent', theme.accent);
        htmlElement.style.setProperty('--theme-card-bg', theme.cardBg);
        htmlElement.style.setProperty('--theme-card-border', theme.cardBorder);
        htmlElement.style.setProperty('--theme-card-shadow', theme.cardShadow);
        htmlElement.style.setProperty('--theme-nav-bg', theme.navBg);
        htmlElement.style.setProperty('--theme-nav-border', theme.navBorder);
        htmlElement.style.setProperty('--theme-input-bg', theme.inputBg);
        htmlElement.style.setProperty('--theme-input-border', theme.inputBorder);
        htmlElement.style.setProperty('--theme-button-bg', theme.buttonBg);
        htmlElement.style.setProperty('--theme-button-text', theme.buttonText);
        htmlElement.style.setProperty('--theme-gradient-start', theme.gradientStart);
        htmlElement.style.setProperty('--theme-gradient-end', theme.gradientEnd);

        // Apply body background based on theme
        if (theme.isDark) {
            htmlElement.style.background = 'linear-gradient(180deg, ' + theme.background + ' 0%, ' + theme.surface + ' 100%)';
        } else {
            htmlElement.style.background = 'linear-gradient(180deg, ' + theme.surface + ' 0%, ' + theme.background + ' 100%)';
        }

        // Set dark/light class for Tailwind
        if (theme.isDark) {
            htmlElement.classList.add('dark');
            htmlElement.classList.remove('light');
        } else {
            htmlElement.classList.add('light');
            htmlElement.classList.remove('dark');
        }
    }

    // Load accessibility settings
    const accessibilitySettings = ['high_contrast', 'large_text', 'dyslexia_font', 'reduced_motion', 'grayscale', 'line_spacing', 'focus_outline', 'font_size'];

    accessibilitySettings.forEach(function(setting) {
        const savedValue = localStorage.getItem('accessibility_' + setting);
        if (savedValue !== null) {
            if (setting === 'font_size') {
                document.documentElement.setAttribute('data-font-size', savedValue);
            } else if (savedValue === 'true') {
                document.documentElement.setAttribute('data-' + setting.replace('_', '-') + '-mode', 'true');
            }
        }
    });

    // Apply system high contrast preference if not manually set
    if (prefersHighContrast() && localStorage.getItem('accessibility_high_contrast') === null) {
        document.documentElement.setAttribute('data-high-contrast-mode', 'true');
    }

    // Apply system reduced motion preference if not manually set
    if (prefersReducedMotion() && localStorage.getItem('accessibility_reduced_motion') === null) {
        document.documentElement.setAttribute('data-reduced-motion-mode', 'true');
    }

    // Listen for system color scheme changes
    if (window.matchMedia) {
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function(e) {
            if (localStorage.getItem('flux_appearance') === 'system' || !localStorage.getItem('flux_appearance')) {
                const newTheme = e.matches ? 'default-dark' : 'default-light';
                if (baseThemes[newTheme]) {
                    setBaseTheme(newTheme);
                }
            }
        });

        // Listen for reduced motion preference changes
        window.matchMedia('(prefers-reduced-motion: reduce)').addEventListener('change', function(e) {
            if (e.matches) {
                document.documentElement.setAttribute('data-reduced-motion-mode', 'true');
            } else if (localStorage.getItem('accessibility_reduced_motion') === null) {
                document.documentElement.removeAttribute('data-reduced-motion-mode');
            }
        });

        // Listen for high contrast preference changes
        window.matchMedia('(prefers-contrast: more)').addEventListener('change', function(e) {
            if (e.matches) {
                document.documentElement.setAttribute('data-high-contrast-mode', 'true');
            } else if (localStorage.getItem('accessibility_high_contrast') === null) {
                document.documentElement.removeAttribute('data-high-contrast-mode');
            }
        });
    }

    // Expose theme data globally for components
    window.themeManager = {
        accentColors: accentColors,
        baseThemes: baseThemes,
        getCurrentTheme: function() {
            return localStorage.getItem('base_theme') || 'default-light';
        },
        getCurrentAccent: function() {
            return localStorage.getItem('accent_color') || 'zinc';
        },
        isDarkMode: function() {
            const theme = baseThemes[this.getCurrentTheme()];
            return theme ? theme.isDark : false;
        }
    };
})();

// Theme Color Setter Function (global)
function setThemeColor(name, hex) {
    const htmlElement = document.documentElement;
    htmlElement.setAttribute('data-theme', name);
    htmlElement.setAttribute('data-accent-color', name);
    htmlElement.style.setProperty('--color-accent', hex, 'important');
    htmlElement.style.setProperty('--color-accent-light', hex + '80', 'important');
    localStorage.setItem('accent_color', name);

    // Dispatch custom event for components to react
    window.dispatchEvent(new CustomEvent('themechange', {
        detail: { type: 'accent', name: name, hex: hex }
    }));

    // Announce to screen readers
    if (typeof announceToScreenReader === 'function') {
        announceToScreenReader('Accent color changed to ' + name);
    }

    console.log('Theme color applied: ' + name + ' (' + hex + ')');
}

// Base Theme Setter Function (global)
function setBaseTheme(themeName) {
    const baseThemes = {
        'default-light': {
            name: 'Classic Light',
            background: '#ffffff',
            surface: '#f5f5f4',
            text: '#1c1917',
            border: '#e7e5e4',
            primary: '#6366f1',
            secondary: '#8b5cf6',
            accent: '#a855f7',
            isDark: false,
            cardBg: '#ffffff',
            cardBorder: '#e7e5e4',
            cardShadow: 'rgba(0, 0, 0, 0.08)',
            navBg: '#ffffff',
            navBorder: '#e7e5e4',
            inputBg: '#ffffff',
            inputBorder: '#e7e5e4',
            buttonBg: '#6366f1',
            buttonText: '#ffffff',
            gradientStart: '#667eea',
            gradientEnd: '#764ba2'
        },
        'default-dark': {
            name: 'Classic Dark',
            background: '#0c0a09',
            surface: '#1c1917',
            text: '#fafaf9',
            border: '#292524',
            primary: '#818cf8',
            secondary: '#a78bfa',
            accent: '#c084fc',
            isDark: true,
            cardBg: '#292524',
            cardBorder: '#44403c',
            cardShadow: 'rgba(0, 0, 0, 0.3)',
            navBg: '#1c1917',
            navBorder: '#44403c',
            inputBg: '#292524',
            inputBorder: '#44403c',
            buttonBg: '#6366f1',
            buttonText: '#ffffff',
            gradientStart: '#4f46e5',
            gradientEnd: '#7c3aed'
        },
        'cyberpunk-neon': {
            name: 'Cyber Neon',
            background: '#1a0a2e',
            surface: '#2d1b4e',
            text: '#e0e0e0',
            border: '#ff00ff',
            primary: '#ff00ff',
            secondary: '#00ffff',
            accent: '#ffff00',
            glow: true,
            isDark: true,
            cardBg: '#1a0a2e',
            cardBorder: '#ff00ff',
            cardShadow: 'rgba(255, 0, 255, 0.3)',
            navBg: '#1a0a2e',
            navBorder: '#ff00ff',
            inputBg: '#2d1b4e',
            inputBorder: '#ff00ff',
            buttonBg: '#ff00ff',
            buttonText: '#000000',
            gradientStart: '#ff00ff',
            gradientEnd: '#00ffff'
        },
        'cyberpunk-dark': {
            name: 'Cyber Dark',
            background: '#000000',
            surface: '#0a0a0a',
            text: '#00ffff',
            border: '#00ffff',
            primary: '#00ffff',
            secondary: '#ff00ff',
            accent: '#00ff00',
            glow: true,
            isDark: true,
            cardBg: '#000000',
            cardBorder: '#00ffff',
            cardShadow: 'rgba(0, 255, 255, 0.2)',
            navBg: '#000000',
            navBorder: '#00ffff',
            inputBg: '#0a0a0a',
            inputBorder: '#00ffff',
            buttonBg: '#00ffff',
            buttonText: '#000000',
            gradientStart: '#000000',
            gradientEnd: '#001a1a'
        },
        'synthwave': {
            name: 'Synthwave',
            background: '#1a0a1a',
            surface: '#2d1b2e',
            text: '#ff71ce',
            border: '#ff71ce',
            primary: '#ff71ce',
            secondary: '#01cdfe',
            accent: '#05ffa1',
            isDark: true,
            cardBg: '#1a0a1a',
            cardBorder: '#ff71ce',
            cardShadow: 'rgba(255, 113, 206, 0.25)',
            navBg: '#1a0a1a',
            navBorder: '#ff71ce',
            inputBg: '#2d1b2e',
            inputBorder: '#ff71ce',
            buttonBg: '#ff71ce',
            buttonText: '#1a0a1a',
            gradientStart: '#ff71ce',
            gradientEnd: '#01cdfe'
        },
        'midnight': {
            name: 'Midnight',
            background: '#0f172a',
            surface: '#1e293b',
            text: '#e2e8f0',
            border: '#334155',
            primary: '#60a5fa',
            secondary: '#818cf8',
            accent: '#a78bfa',
            isDark: true,
            cardBg: '#0f172a',
            cardBorder: '#334155',
            cardShadow: 'rgba(0, 0, 0, 0.4)',
            navBg: '#0f172a',
            navBorder: '#334155',
            inputBg: '#1e293b',
            inputBorder: '#334155',
            buttonBg: '#3b82f6',
            buttonText: '#ffffff',
            gradientStart: '#1e3a5f',
            gradientEnd: '#0f172a'
        },
        'dracula': {
            name: 'Dracula',
            background: '#21222c',
            surface: '#282a36',
            text: '#f8f8f2',
            border: '#44475a',
            primary: '#bd93f9',
            secondary: '#ff79c6',
            accent: '#50fa7b',
            isDark: true,
            cardBg: '#21222c',
            cardBorder: '#44475a',
            cardShadow: 'rgba(0, 0, 0, 0.3)',
            navBg: '#21222c',
            navBorder: '#44475a',
            inputBg: '#282a36',
            inputBorder: '#44475a',
            buttonBg: '#bd93f9',
            buttonText: '#282a36',
            gradientStart: '#44475a',
            gradientEnd: '#21222c'
        },
        'nord': {
            name: 'Nord',
            background: '#2e3440',
            surface: '#3b4252',
            text: '#eceff4',
            border: '#4c566a',
            primary: '#88c0d0',
            secondary: '#81a1c1',
            accent: '#b48ead',
            isDark: true,
            cardBg: '#2e3440',
            cardBorder: '#4c566a',
            cardShadow: 'rgba(0, 0, 0, 0.25)',
            navBg: '#2e3440',
            navBorder: '#4c566a',
            inputBg: '#3b4252',
            inputBorder: '#4c566a',
            buttonBg: '#5e81ac',
            buttonText: '#eceff4',
            gradientStart: '#3b4252',
            gradientEnd: '#2e3440'
        },
    };

    const theme = baseThemes[themeName];
    if (!theme) return;

    const htmlElement = document.documentElement;
    htmlElement.style.setProperty('--theme-background', theme.background);
    htmlElement.style.setProperty('--theme-surface', theme.surface);
    htmlElement.style.setProperty('--theme-text', theme.text);
    htmlElement.style.setProperty('--theme-border', theme.border);

    // Apply enhanced theme variables
    htmlElement.style.setProperty('--theme-primary', theme.primary);
    htmlElement.style.setProperty('--theme-secondary', theme.secondary);
    htmlElement.style.setProperty('--theme-accent', theme.accent);
    htmlElement.style.setProperty('--theme-card-bg', theme.cardBg);
    htmlElement.style.setProperty('--theme-card-border', theme.cardBorder);
    htmlElement.style.setProperty('--theme-card-shadow', theme.cardShadow);
    htmlElement.style.setProperty('--theme-nav-bg', theme.navBg);
    htmlElement.style.setProperty('--theme-nav-border', theme.navBorder);
    htmlElement.style.setProperty('--theme-input-bg', theme.inputBg);
    htmlElement.style.setProperty('--theme-input-border', theme.inputBorder);
    htmlElement.style.setProperty('--theme-button-bg', theme.buttonBg);
    htmlElement.style.setProperty('--theme-button-text', theme.buttonText);
    htmlElement.style.setProperty('--theme-gradient-start', theme.gradientStart);
    htmlElement.style.setProperty('--theme-gradient-end', theme.gradientEnd);

    // Apply body background based on theme
    if (theme.isDark) {
        htmlElement.style.background = 'linear-gradient(180deg, ' + theme.background + ' 0%, ' + theme.surface + ' 100%)';
    } else {
        htmlElement.style.background = 'linear-gradient(180deg, ' + theme.surface + ' 0%, ' + theme.background + ' 100%)';
    }

    htmlElement.setAttribute('data-base-theme', themeName);

    if (theme.glow) {
        htmlElement.setAttribute('data-theme-glow', 'true');
    } else {
        htmlElement.removeAttribute('data-theme-glow');
    }

    // Update dark/light classes for Tailwind
    if (theme.isDark) {
        htmlElement.classList.add('dark');
        htmlElement.classList.remove('light');
    } else {
        htmlElement.classList.add('light');
        htmlElement.classList.remove('dark');
    }

    localStorage.setItem('base_theme', themeName);

    // Dispatch custom event for components to react
    window.dispatchEvent(new CustomEvent('themechange', {
        detail: { type: 'base', name: themeName, theme: theme }
    }));

    // Announce to screen readers
    const announcement = document.createElement('div');
    announcement.setAttribute('role', 'status');
    announcement.setAttribute('aria-live', 'polite');
    announcement.setAttribute('aria-atomic', 'true');
    announcement.className = 'sr-only';
    announcement.style.cssText = 'position:absolute;left:-9999px;width:1px;height:1px;overflow:hidden;';
    announcement.textContent = 'Theme changed to ' + theme.name;
    document.body.appendChild(announcement);
    setTimeout(function() { announcement.remove(); }, 1000);

    console.log('Base theme applied: ' + theme.name);
}

// Appearance Setter Function (global)
function setAppearance(mode) {
    localStorage.setItem('flux_appearance', mode);
    localStorage.setItem('color-theme', mode);

    if (mode === 'dark') {
        document.documentElement.classList.add('dark');
        document.documentElement.classList.remove('light');
    } else if (mode === 'light') {
        document.documentElement.classList.add('light');
        document.documentElement.classList.remove('dark');
    } else {
        // System - detect
        if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
            document.documentElement.classList.add('dark');
            document.documentElement.classList.remove('light');
        } else {
            document.documentElement.classList.add('light');
            document.documentElement.classList.remove('dark');
        }
    }

    // Dispatch custom event for components to react
    window.dispatchEvent(new CustomEvent('themechange', {
        detail: { type: 'appearance', mode: mode }
    }));
}

// Accessibility Settings Setter Function (global)
function setAccessibilitySetting(setting, value) {
    const storageKey = 'accessibility_' + setting;
    localStorage.setItem(storageKey, value);

    if (setting === 'font_size') {
        document.documentElement.setAttribute('data-font-size', value);
    } else if (value === 'true') {
        document.documentElement.setAttribute('data-' + setting.replace('_', '-') + '-mode', 'true');
    } else {
        document.documentElement.removeAttribute('data-' + setting.replace('_', '-') + '-mode');
    }

    // Dispatch custom event for components to react
    window.dispatchEvent(new CustomEvent('accessibilitychange', {
        detail: { setting: setting, value: value }
    }));

    // Initialize saved base theme on page load
    (function() {
        const savedBaseTheme = localStorage.getItem('base_theme') || 'default-light';
        if (savedBaseTheme && savedBaseTheme !== 'default-light') {
            // Apply the saved theme
            const baseThemes = {
                'default-light': { name: 'Classic Light', background: '#ffffff', surface: '#f5f5f4', text: '#1c1917', border: '#e7e5e4' },
                'default-dark': { name: 'Classic Dark', background: '#0c0a09', surface: '#1c1917', text: '#fafaf9', border: '#292524' },
                'cyberpunk-neon': { name: 'Cyber Neon', background: '#1a0a2e', surface: '#2d1b4e', text: '#e0e0e0', border: '#ff00ff', accent: '#ff00ff', glow: true },
                'cyberpunk-dark': { name: 'Cyber Dark', background: '#000000', surface: '#0a0a0a', text: '#00ffff', border: '#00ffff', accent: '#00ffff', glow: true },
                'synthwave': { name: 'Synthwave', background: '#1a0a1a', surface: '#2d1b2e', text: '#ff71ce', border: '#ff71ce', accent: '#ff71ce' },
                'midnight': { name: 'Midnight', background: '#0f172a', surface: '#1e293b', text: '#e2e8f0', border: '#334155' },
                'dracula': { name: 'Dracula', background: '#21222c', surface: '#282a36', text: '#f8f8f2', border: '#44475a', accent: '#bd93f9' },
                'nord': { name: 'Nord', background: '#2e3440', surface: '#3b4252', text: '#eceff4', border: '#4c566a', accent: '#88c0d0' }
            };

            const theme = baseThemes[savedBaseTheme];
            if (theme) {
                document.documentElement.setAttribute('data-base-theme', savedBaseTheme);

                // Apply glow effect if needed
                if (theme.glow) {
                    document.documentElement.setAttribute('data-theme-glow', 'true');
                }
            }
        }
    })();
}
</script>
