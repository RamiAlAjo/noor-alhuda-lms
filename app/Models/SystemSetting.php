<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SystemSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'category',
        'label',
        'description',
        'options',
        'is_public',
        'is_editable',
    ];

    protected $casts = [
        'options' => 'array',
        'is_public' => 'boolean',
        'is_editable' => 'boolean',
    ];

    /**
     * Get a setting value with caching
     */
    public static function get(string $key, $default = null)
    {
        return Cache::remember(
            "system_setting_{$key}",
            3600, // 1 hour
            function () use ($key, $default) {
                $setting = static::where('key', $key)->first();

                return $setting ? static::castValue($setting) : $default;
            }
        );
    }

    /**
     * Set a setting value
     */
    public static function set(string $key, $value, array $attributes = []): bool
    {
        try {
            $setting = static::updateOrCreate(
                ['key' => $key],
                array_merge($attributes, ['value' => static::prepareValue($value, $attributes['type'] ?? 'string')])
            );

            // Clear cache
            Cache::forget("system_setting_{$key}");

            // Log the change
            Log::info("System setting updated: {$key}", [
                'old_value' => $setting->getOriginal('value'),
                'new_value' => $value,
                'user_id' => auth()->id(),
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error("Failed to update system setting {$key}: {$e->getMessage()}");

            return false;
        }
    }

    /**
     * Get all settings by category
     */
    public static function getByCategory(string $category): array
    {
        return Cache::remember(
            "system_settings_category_{$category}",
            3600,
            function () use ($category) {
                return static::where('category', $category)
                    ->get()
                    ->mapWithKeys(function ($setting) {
                        return [$setting->key => static::castValue($setting)];
                    })
                    ->toArray();
            }
        );
    }

    /**
     * Get all public settings
     */
    public static function getPublicSettings(): array
    {
        return Cache::remember(
            'system_settings_public',
            3600,
            function () {
                return static::where('is_public', true)
                    ->get()
                    ->mapWithKeys(function ($setting) {
                        return [$setting->key => static::castValue($setting)];
                    })
                    ->toArray();
            }
        );
    }

    /**
     * Cast value based on type
     */
    protected static function castValue(SystemSetting $setting)
    {
        return match ($setting->type) {
            'boolean' => filter_var($setting->value, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $setting->value,
            'float' => (float) $setting->value,
            'json' => json_decode($setting->value, true),
            'file' => $setting->value, // File paths are stored as strings
            default => $setting->value,
        };
    }

    /**
     * Prepare value for storage based on type
     */
    protected static function prepareValue($value, string $type): string
    {
        return match ($type) {
            'json' => json_encode($value),
            'boolean' => $value ? '1' : '0',
            default => (string) $value,
        };
    }

    /**
     * Clear all settings cache
     */
    public static function clearCache(): void
    {
        $settings = static::all();
        foreach ($settings as $setting) {
            Cache::forget("system_setting_{$setting->key}");
        }

        // Clear category caches
        $categories = static::distinct('category')->pluck('category');
        foreach ($categories as $category) {
            Cache::forget("system_settings_category_{$category}");
        }

        Cache::forget('system_settings_public');
    }

    /**
     * Seed default settings
     */
    public static function seedDefaults(): void
    {
        $defaults = [
            // General Settings
            [
                'key' => 'app_name',
                'value' => 'Noor Alhuda LMS',
                'type' => 'string',
                'category' => 'general',
                'label' => 'Application Name',
                'description' => 'The main name of the application',
                'is_public' => true,
            ],
            [
                'key' => 'app_name_ar',
                'value' => 'نور الهدى نظام إدارة التعلم',
                'type' => 'string',
                'category' => 'general',
                'label' => 'Application Name (Arabic)',
                'description' => 'The Arabic name of the application',
                'is_public' => true,
            ],
            [
                'key' => 'app_description',
                'value' => 'A modern, bilingual Learning Management System',
                'type' => 'string',
                'category' => 'general',
                'label' => 'Application Description',
                'description' => 'Brief description of the application',
                'is_public' => true,
            ],
            [
                'key' => 'app_email',
                'value' => 'info@noorlms.com',
                'type' => 'string',
                'category' => 'general',
                'label' => 'Contact Email',
                'description' => 'Primary contact email address',
            ],
            [
                'key' => 'app_phone',
                'value' => '+962 6 000 0000',
                'type' => 'string',
                'category' => 'general',
                'label' => 'Contact Phone',
                'description' => 'Primary contact phone number',
            ],
            [
                'key' => 'app_address',
                'value' => 'Amman, Jordan',
                'type' => 'string',
                'category' => 'general',
                'label' => 'Address',
                'description' => 'Physical address of the institution',
            ],
            [
                'key' => 'timezone',
                'value' => 'Asia/Amman',
                'type' => 'string',
                'category' => 'general',
                'label' => 'Timezone',
                'description' => 'Default timezone for the application',
                'options' => ['Asia/Amman', 'UTC', 'America/New_York', 'Europe/London'],
            ],

            // Security Settings
            [
                'key' => 'password_min_length',
                'value' => '8',
                'type' => 'integer',
                'category' => 'security',
                'label' => 'Minimum Password Length',
                'description' => 'Minimum number of characters required for passwords',
            ],
            [
                'key' => 'password_require_uppercase',
                'value' => '1',
                'type' => 'boolean',
                'category' => 'security',
                'label' => 'Require Uppercase Letters',
                'description' => 'Passwords must contain at least one uppercase letter',
            ],
            [
                'key' => 'password_require_numbers',
                'value' => '1',
                'type' => 'boolean',
                'category' => 'security',
                'label' => 'Require Numbers',
                'description' => 'Passwords must contain at least one number',
            ],
            [
                'key' => 'password_require_symbols',
                'value' => '0',
                'type' => 'boolean',
                'category' => 'security',
                'label' => 'Require Symbols',
                'description' => 'Passwords must contain at least one special character',
            ],
            [
                'key' => 'session_lifetime',
                'value' => '480', // 8 hours in minutes
                'type' => 'integer',
                'category' => 'security',
                'label' => 'Session Lifetime (minutes)',
                'description' => 'How long before user sessions expire',
            ],
            [
                'key' => 'max_login_attempts',
                'value' => '5',
                'type' => 'integer',
                'category' => 'security',
                'label' => 'Max Login Attempts',
                'description' => 'Number of failed login attempts before lockout',
            ],
            [
                'key' => 'two_factor_required',
                'value' => '0',
                'type' => 'boolean',
                'category' => 'security',
                'label' => 'Require Two-Factor Authentication',
                'description' => 'All users must enable 2FA',
            ],

            // Email Settings
            [
                'key' => 'mail_driver',
                'value' => 'smtp',
                'type' => 'string',
                'category' => 'email',
                'label' => 'Mail Driver',
                'description' => 'Email sending method',
                'options' => ['smtp', 'mail', 'sendmail', 'ses', 'mailgun'],
            ],
            [
                'key' => 'mail_host',
                'value' => 'smtp.gmail.com',
                'type' => 'string',
                'category' => 'email',
                'label' => 'SMTP Host',
                'description' => 'SMTP server hostname',
            ],
            [
                'key' => 'mail_port',
                'value' => '587',
                'type' => 'integer',
                'category' => 'email',
                'label' => 'SMTP Port',
                'description' => 'SMTP server port',
            ],
            [
                'key' => 'mail_username',
                'value' => '',
                'type' => 'string',
                'category' => 'email',
                'label' => 'SMTP Username',
                'description' => 'SMTP authentication username',
            ],
            [
                'key' => 'mail_password',
                'value' => '',
                'type' => 'string',
                'category' => 'email',
                'label' => 'SMTP Password',
                'description' => 'SMTP authentication password',
            ],
            [
                'key' => 'mail_encryption',
                'value' => 'tls',
                'type' => 'string',
                'category' => 'email',
                'label' => 'SMTP Encryption',
                'description' => 'SMTP encryption method',
                'options' => ['tls', 'ssl', 'null'],
            ],
            [
                'key' => 'mail_from_address',
                'value' => 'noreply@noorlms.com',
                'type' => 'string',
                'category' => 'email',
                'label' => 'From Email Address',
                'description' => 'Default sender email address',
            ],
            [
                'key' => 'mail_from_name',
                'value' => 'Noor Alhuda LMS',
                'type' => 'string',
                'category' => 'email',
                'label' => 'From Name',
                'description' => 'Default sender name',
            ],

            // System Settings
            [
                'key' => 'maintenance_mode',
                'value' => '0',
                'type' => 'boolean',
                'category' => 'system',
                'label' => 'Maintenance Mode',
                'description' => 'Put the application in maintenance mode',
            ],
            [
                'key' => 'maintenance_message',
                'value' => 'The system is currently under maintenance. Please try again later.',
                'type' => 'string',
                'category' => 'system',
                'label' => 'Maintenance Message',
                'description' => 'Message to display during maintenance',
            ],
            [
                'key' => 'debug_mode',
                'value' => '0',
                'type' => 'boolean',
                'category' => 'system',
                'label' => 'Debug Mode',
                'description' => 'Enable debug mode for development',
            ],
            [
                'key' => 'log_level',
                'value' => 'error',
                'type' => 'string',
                'category' => 'system',
                'label' => 'Log Level',
                'description' => 'Minimum log level to record',
                'options' => ['emergency', 'alert', 'critical', 'error', 'warning', 'notice', 'info', 'debug'],
            ],
            [
                'key' => 'cache_driver',
                'value' => 'file',
                'type' => 'string',
                'category' => 'system',
                'label' => 'Cache Driver',
                'description' => 'Default cache driver',
                'options' => ['file', 'database', 'redis', 'memcached'],
            ],
            [
                'key' => 'backup_enabled',
                'value' => '1',
                'type' => 'boolean',
                'category' => 'system',
                'label' => 'Automatic Backups',
                'description' => 'Enable automatic database backups',
            ],
            [
                'key' => 'backup_frequency',
                'value' => 'daily',
                'type' => 'string',
                'category' => 'system',
                'label' => 'Backup Frequency',
                'description' => 'How often to create automatic backups',
                'options' => ['hourly', 'daily', 'weekly', 'monthly'],
            ],
        ];

        foreach ($defaults as $setting) {
            static::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
