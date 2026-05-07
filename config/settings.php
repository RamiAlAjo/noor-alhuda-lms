<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Application Theme Settings
    |--------------------------------------------------------------------------
    |
    | These settings control the visual appearance of the application including
    | the default theme (light/dark/system) and accent color.
    |
    */

    'default_theme' => env('DEFAULT_THEME', 'light'),
    'accent_color' => env('ACCENT_COLOR', 'blue'),

    // Branding
    'app_name' => env('APP_NAME', 'Noor Alhuda LMS'),
    'app_name_ar' => env('APP_NAME_AR', 'نور الهدى نظام إدارة التعلم'),
    'app_description' => env('APP_DESCRIPTION', 'A modern, bilingual Learning Management System'),
    'app_email' => env('APP_EMAIL', 'info@noorlms.com'),
    'app_phone' => env('APP_PHONE', '+962 6 000 0000'),
    'app_address' => env('APP_ADDRESS', 'Amman, Jordan'),

    // Mobile App Settings
    'mobile_app_enabled' => env('MOBILE_APP_ENABLED', false),
    'mobile_app_download_url' => env('MOBILE_APP_DOWNLOAD_URL', ''),
    'mobile_app_ios_url' => env('MOBILE_APP_IOS_URL', ''),
    'mobile_app_android_url' => env('MOBILE_APP_ANDROID_URL', ''),
    'mobile_app_version' => env('MOBILE_APP_VERSION', '1.0.0'),
];
