<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Cache;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $supportedLanguages = ['en', 'ar'];
        $locale = null;

        // Priority order for locale detection:
        // 1. Session
        // 2. User preference (if authenticated)
        // 3. Cookie
        // 4. Browser language
        // 5. Default locale from config

        // Check session first
        if (Session::has('locale')) {
            $locale = Session::get('locale');
        }
        
        // Check user preference if authenticated
        elseif (auth()->check() && auth()->user()->locale) {
            $locale = auth()->user()->locale;
            Session::put('locale', $locale);
        }
        
        // Check cookie
        elseif (Cookie::has('locale')) {
            $locale = Cookie::get('locale');
            Session::put('locale', $locale);
        }
        
        // Check browser language
        elseif ($request->hasHeader('Accept-Language')) {
            $browserLanguage = $request->getPreferredLanguage($supportedLanguages);
            if ($browserLanguage) {
                $locale = $browserLanguage;
                Session::put('locale', $locale);
            }
        }

        // Validate locale and set default if invalid
        if (!in_array($locale, $supportedLanguages)) {
            $locale = config('app.locale', 'en');
            Session::put('locale', $locale);
        }

        // Set the locale
        App::setLocale($locale);

        // Set direction for RTL languages
        if ($locale === 'ar') {
            config(['app.direction' => 'rtl']);
        } else {
            config(['app.direction' => 'ltr']);
        }

        // Trigger translation loading for the new locale
        app()->make('translator')->setLocale($locale);

        return $next($request);
    }
}