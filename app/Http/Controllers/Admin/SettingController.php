<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;

class SettingController extends Controller
{
    /**
     * Display general settings.
     */
    public function index(): View
    {
        return view('pages.admin.settings.index');
    }

    /**
     * Update general settings.
     */
    public function updateGeneral(Request $request)
    {
        $request->validate([
            'app_name' => 'required|string|max:255',
            'app_name_ar' => 'nullable|string|max:255',
            'app_description' => 'nullable|string',
            'app_email' => 'nullable|email',
            'app_phone' => 'nullable|string|max:20',
            'app_address' => 'nullable|string',
            'app_logo' => 'nullable|image|max:2048',
        ]);

        // Save to .env or config
        foreach ($request->only(['app_name', 'app_name_ar', 'app_description', 'app_email', 'app_phone', 'app_address']) as $key => $value) {
            if ($value !== null) {
                config(['settings.'.$key => $value]);
            }
        }

        return back()->with('success', __('lms::messages.settings_updated'));
    }

    /**
     * Display accessibility settings.
     */
    public function accessibility(): View
    {
        return view('pages.admin.settings.accessibility');
    }

    /**
     * Display theme settings.
     */
    public function theme(): View
    {
        return view('pages.admin.settings.theme');
    }

    /**
     * Update theme settings.
     */
    public function updateTheme(Request $request)
    {
        $request->validate([
            'default_theme' => 'nullable|in:light,dark,system',
            'accent_color' => 'nullable|string|max:50',
            'app_name' => 'nullable|string|max:255',
            'app_name_ar' => 'nullable|string|max:255',
            'app_description' => 'nullable|string',
        ]);

        // Save theme settings to config cache
        $settings = $request->only(['default_theme', 'accent_color', 'app_name', 'app_name_ar', 'app_description']);

        foreach ($settings as $key => $value) {
            if ($value !== null && $value !== '') {
                config(['settings.'.$key => $value]);
            }
        }

        // Save to .env file for persistence
        $envPath = base_path('.env');
        $envContent = file_get_contents($envPath);

        if ($request->has('default_theme') && $request->default_theme) {
            $envContent = $this->updateEnv($envContent, 'DEFAULT_THEME', $request->default_theme);
        }
        if ($request->has('accent_color') && $request->accent_color) {
            $envContent = $this->updateEnv($envContent, 'ACCENT_COLOR', $request->accent_color);
        }
        if ($request->has('app_name') && $request->app_name) {
            $envContent = $this->updateEnv($envContent, 'APP_NAME', $request->app_name);
        }
        if ($request->has('app_name_ar') && $request->app_name_ar) {
            $envContent = $this->updateEnv($envContent, 'APP_NAME_AR', $request->app_name_ar);
        }

        file_put_contents($envPath, $envContent);

        return back()->with('success', __('lms::messages.settings_updated'));
    }

    /**
     * Helper to update .env file.
     */
    private function updateEnv(string $content, string $key, string $value): string
    {
        $keyUpper = strtoupper($key);
        // Quote values with spaces
        if (str_contains($value, ' ')) {
            $value = '"'.$value.'"';
        }
        if (preg_match('/^'.$keyUpper.'=/m', $content)) {
            $content = preg_replace('/^'.$keyUpper.'=.*/m', $keyUpper.'='.$value, $content);
        } else {
            $content .= "\n".$keyUpper.'='.$value;
        }

        return $content;
    }

    /**
     * Display system logs.
     */
    public function logs(): View
    {
        $logFiles = [];
        $logPath = storage_path('logs');

        if (File::exists($logPath)) {
            $files = File::files($logPath);
            foreach ($files as $file) {
                $logFiles[] = [
                    'name' => $file->getFilename(),
                    'size' => round($file->getSize() / 1024, 2).' KB',
                    'modified' => date('Y-m-d H:i:s', $file->getMTime()),
                ];
            }
        }

        rsort($logFiles);

        return view('pages.admin.settings.logs', compact('logFiles'));
    }

    /**
     * Clear system cache.
     */
    public function clearCache()
    {
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('view:clear');

        return back()->with('success', __('lms::messages.cache_cleared'));
    }

    /**
     * Display system info.
     */
    public function systemInfo(): View
    {
        $phpVersion = PHP_VERSION;
        $laravelVersion = app()->version();
        $serverSoftware = $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown';
        $databaseDriver = config('database.default');

        return view('pages.admin.settings.system-info', compact(
            'phpVersion', 'laravelVersion', 'serverSoftware', 'databaseDriver'
        ));
    }

    /**
     * Display backup settings.
     */
    public function backups(): View
    {
        $backupPath = storage_path('app/backups');
        $backups = [];

        if (File::exists($backupPath)) {
            $files = File::files($backupPath);
            foreach ($files as $file) {
                $backups[] = [
                    'name' => $file->getFilename(),
                    'size' => round($file->getSize() / 1024 / 1024, 2).' MB',
                    'modified' => date('Y-m-d H:i:s', $file->getMTime()),
                ];
            }
        }

        rsort($backups);

        return view('pages.admin.settings.backups', compact('backups'));
    }

    /**
     * Create a database backup.
     */
    public function createBackup()
    {
        try {
            Artisan::call('backup:run');

            return back()->with('success', __('lms::messages.backup_created'));
        } catch (\Exception $e) {
            return back()->with('error', __('lms::messages.backup_failed'));
        }
    }
}
