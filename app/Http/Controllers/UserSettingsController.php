<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class UserSettingsController extends Controller
{
    /**
     * Toggle accessibility settings
     */
    public function toggleAccessibility(Request $request)
    {
        $settings = ['high_contrast', 'large_text', 'dyslexia_font', 'reduced_motion', 'grayscale', 'line_spacing', 'focus_outline', 'font_size'];

        foreach ($settings as $setting) {
            if ($request->has($setting)) {
                $value = $request->input($setting);
                // Handle both '1' and 'true' as boolean true, 'false' as boolean false
                if ($value === '1' || $value === 'true') {
                    session([$setting => true]);
                } elseif ($value === 'false' || $value === '0') {
                    session([$setting => false]);
                } else {
                    session([$setting => $value]);
                }
            }
        }

        return redirect()->back();
    }

    /**
     * Reset all accessibility settings
     */
    public function resetAccessibility()
    {
        $settings = ['high_contrast', 'large_text', 'dyslexia_font', 'reduced_motion', 'grayscale', 'line_spacing', 'focus_outline', 'font_size'];

        foreach ($settings as $setting) {
            session()->forget($setting);
        }

        return redirect()->back();
    }

    /**
     * Switch application language
     */
    public function switchLanguage(Request $request, $lang)
    {
        $allowedLanguages = ['en', 'ar', 'fr', 'tr', 'zh', 'fa', 'id', 'ku', 'hy'];

        if (! in_array($lang, $allowedLanguages)) {
            return back();
        }

        // Store locale in session
        session(['locale' => $lang]);

        // Save to database for authenticated users (persists across all pages and roles)
        if (Auth::check()) {
            $userId = Auth::id();
            \App\Models\UserSetting::updateOrCreate(
                ['user_id' => $userId],
                ['locale' => $lang]
            );
        }

        // Also set a cookie for guest users (persists for 1 year)
        \Illuminate\Support\Facades\Cookie::queue('locale', $lang, 525600);

        // Force the locale to be applied immediately
        app()->setLocale($lang);

        // Clear any cached translations or views
        \Illuminate\Support\Facades\Cache::forget('translations');
        \Illuminate\Support\Facades\Artisan::call('view:clear');

        // Redirect back with cache prevention headers
        return back()->withHeaders([
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }

    /**
     * Switch application theme
     */
    public function switchTheme(Request $request)
    {
        // Handle base theme
        if ($request->has('base_theme')) {
            $baseTheme = $request->input('base_theme');
            $allowedBaseThemes = [
                'default-light', 'default-dark', 'cyberpunk-neon', 'cyberpunk-dark',
                'synthwave', 'midnight', 'dracula', 'nord',
            ];

            if (in_array($baseTheme, $allowedBaseThemes)) {
                session(['base_theme' => $baseTheme]);

                // Save to database for authenticated users
                if (Auth::check()) {
                    \App\Models\UserSetting::updateOrCreate(
                        ['user_id' => Auth::id()],
                        ['base_theme' => $baseTheme]
                    );
                }

                // Also set a cookie for guest users
                \Illuminate\Support\Facades\Cookie::queue('base_theme', $baseTheme, 525600);
            }
        }

        // Handle appearance mode
        if ($request->has('flux_appearance')) {
            $appearance = $request->input('flux_appearance');
            $allowedAppearances = ['light', 'dark', 'system'];

            if (in_array($appearance, $allowedAppearances)) {
                session(['flux_appearance' => $appearance]);

                // Save to database for authenticated users
                if (Auth::check()) {
                    \App\Models\UserSetting::updateOrCreate(
                        ['user_id' => Auth::id()],
                        ['appearance' => $appearance]
                    );
                }

                // Also set a cookie for guest users
                \Illuminate\Support\Facades\Cookie::queue('flux_appearance', $appearance, 525600);
            }
        }

        // Handle accent color (legacy theme parameter)
        $theme = $request->input('theme', 'zinc');
        $allowedThemes = [
            'base', 'red', 'orange', 'amber', 'yellow', 'lime', 'green', 'emerald', 'teal', 'cyan',
            'sky', 'blue', 'indigo', 'violet', 'purple', 'fuchsia', 'pink', 'rose', 'slate', 'gray',
            'zinc', 'neutral', 'stone', 'light', 'dark',
        ];

        if (in_array($theme, $allowedThemes)) {
            // Handle light/dark mode - these affect appearance mode (legacy)
            if ($theme === 'light' || $theme === 'dark') {
                session(['flux_appearance' => $theme]);
            } else {
                // For accent colors, update session
                session(['accent_color' => $theme]);

                // Save to database for authenticated users (persists across sessions)
                if (Auth::check()) {
                    \App\Models\UserSetting::updateOrCreate(
                        ['user_id' => Auth::id()],
                        ['theme' => $theme]
                    );
                }

                // Also set a cookie for guest users (persists for 1 year)
                \Illuminate\Support\Facades\Cookie::queue('accent_color', $theme, 525600);
            }
        }

        // Return appropriate response based on request type
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'theme' => $theme]);
        }

        // Redirect back with cache prevention headers
        return back()->withHeaders([
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }
}
