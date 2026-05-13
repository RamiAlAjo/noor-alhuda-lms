<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ApplyLocale
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
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
            $setting = \App\Models\UserSetting::where('user_id', $userId)->first();
            if ($setting && $setting->locale) {
                $locale = $setting->locale;
            }
        }
        // 3. Check cookie for guest users
        elseif ($request->cookie('locale')) {
            $locale = $request->cookie('locale');
        }
        // 4. Check browser language for new users (fallback to Arabic for Arabic speakers)
        elseif ($request->header('Accept-Language')) {
            $acceptLang = $request->header('Accept-Language');
            if (str_contains($acceptLang, 'ar')) {
                $locale = 'ar';
            }
        }

        // Apply locale if valid
        $allowedLocales = ['en', 'ar', 'fr', 'tr', 'zh', 'fa', 'id', 'ku', 'hy'];
        if ($locale && in_array($locale, $allowedLocales)) {
            app()->setLocale($locale);
        }

        return $next($request);
    }
}
