<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;

class SettingController extends Controller
{
    /**
     * Display system settings dashboard.
     */
    public function index(): View
    {
        $categories = [
            'general' => SystemSetting::where('category', 'general')->where('is_editable', true)->get(),
            'security' => SystemSetting::where('category', 'security')->where('is_editable', true)->get(),
            'email' => SystemSetting::where('category', 'email')->where('is_editable', true)->get(),
            'system' => SystemSetting::where('category', 'system')->where('is_editable', true)->get(),
        ];

        return view('pages.admin.settings.index', compact('categories'));
    }

    /**
     * Update system settings.
     */
    public function updateSettings(Request $request)
    {
        $settings = $request->except(['_token', '_method']);

        $updated = 0;
        $errors = [];

        foreach ($settings as $key => $value) {
            try {
                $setting = SystemSetting::where('key', $key)->where('is_editable', true)->first();

                if ($setting) {
                    // Handle file uploads
                    if ($request->hasFile($key)) {
                        $file = $request->file($key);
                        $path = $file->store('settings', 'public');
                        $value = $path;
                        $setting->type = 'file';
                    }

                    SystemSetting::set($key, $value, ['type' => $setting->type]);
                    $updated++;
                }
            } catch (\Exception $e) {
                $errors[] = "Failed to update {$key}: {$e->getMessage()}";
            }
        }

        $message = $updated > 0
            ? __('Settings updated successfully')." ({$updated} settings)"
            : __('No settings were updated');

        if (! empty($errors)) {
            $message .= '. Errors: '.implode(', ', $errors);
        }

        return back()->with('success', $message);
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
     * Toggle maintenance mode.
     */
    public function toggleMaintenance(Request $request)
    {
        $enabled = $request->boolean('maintenance_mode');
        $message = $request->input('maintenance_message', 'System is under maintenance');

        try {
            if ($enabled) {
                Artisan::call('down', ['--message' => $message]);
                SystemSetting::set('maintenance_mode', true);
            } else {
                Artisan::call('up');
                SystemSetting::set('maintenance_mode', false);
            }

            return back()->with('success', $enabled ? 'Maintenance mode enabled' : 'Maintenance mode disabled');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to toggle maintenance mode: '.$e->getMessage());
        }
    }

    /**
     * Create a database backup.
     */
    public function createBackup()
    {
        try {
            $filename = 'backup-'.date('Y-m-d-H-i-s').'.sql';
            $path = storage_path('app/backups/'.$filename);

            // Ensure backup directory exists
            if (! File::exists(storage_path('app/backups'))) {
                File::makeDirectory(storage_path('app/backups'), 0755, true);
            }

            // Simple backup command (you might want to use a more robust backup solution)
            $command = sprintf(
                'mysqldump -h%s -u%s -p%s %s > %s',
                config('database.connections.mysql.host'),
                config('database.connections.mysql.username'),
                config('database.connections.mysql.password'),
                config('database.connections.mysql.database'),
                $path
            );

            exec($command, $output, $returnCode);

            if ($returnCode === 0) {
                return back()->with('success', 'Database backup created successfully: '.$filename);
            } else {
                return back()->with('error', 'Backup failed with return code: '.$returnCode);
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Backup failed: '.$e->getMessage());
        }
    }

    /**
     * Test email configuration.
     */
    public function testEmail(Request $request)
    {
        $request->validate([
            'test_email' => 'required|email',
        ]);

        try {
            \Mail::raw('This is a test email from Noor Alhuda LMS settings.', function ($message) use ($request) {
                $message->to($request->test_email)
                    ->subject('Test Email from LMS Settings');
            });

            return back()->with('success', 'Test email sent successfully to '.$request->test_email);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to send test email: '.$e->getMessage());
        }
    }

    /**
     * Clear all system caches.
     */
    public function clearAllCaches()
    {
        try {
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('view:clear');
            Artisan::call('route:clear');
            SystemSetting::clearCache();

            return back()->with('success', 'All caches cleared successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to clear caches: '.$e->getMessage());
        }
    }
}
