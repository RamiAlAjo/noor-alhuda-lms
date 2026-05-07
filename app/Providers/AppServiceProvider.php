<?php

namespace App\Providers;

use App\Models\User;
use App\Models\UserProfile;
use App\Models\UserSetting;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();
        $this->configureLocale();
        $this->configureUserSettings();
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null
        );
    }

    /**
     * Configure application locale from session, database, or cookie.
     */
    protected function configureLocale(): void
    {
        // Priority: Session -> Database (for auth users) -> Cookie -> Default (en)
        $locale = null;

        // 1. Check session first
        if (session()->has('locale')) {
            $locale = session('locale');
        }
        // 2. Check database for authenticated users
        elseif (Auth::check()) {
            $userId = Auth::id();
            $profile = UserProfile::where('user_id', $userId)->first();
            if ($profile && $profile->locale) {
                $locale = $profile->locale;
            }
        }
        // 3. Check cookie for guest users
        elseif (\Illuminate\Support\Facades\Cookie::get('locale')) {
            $locale = \Illuminate\Support\Facades\Cookie::get('locale');
        }

        // Apply locale if valid
        $allowedLocales = ['en', 'ar', 'fr', 'tr', 'zh', 'fa', 'id', 'ku', 'hy'];
        if ($locale && in_array($locale, $allowedLocales)) {
            app()->setLocale($locale);
        }
    }

    /**
     * Auto-create user settings when a new user is created.
     */
    protected function configureUserSettings(): void
    {
        User::created(function ($user) {
            // Create default user settings for new users
            UserSetting::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'theme' => 'light',
                    'locale' => 'en',
                ]
            );
        });
    }
}
