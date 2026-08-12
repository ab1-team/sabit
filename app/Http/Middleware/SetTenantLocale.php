<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetTenantLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locales = config('app.available_locales', ['id', 'en', 'ar']);
        $session = $request->session()->get('tenant_locale');

        $locale = $session ?: $request->getPreferredLanguage($locales) ?: config('app.locale');

        if (!in_array($locale, $locales, true)) {
            $locale = config('app.locale', 'id');
        }

        app()->setLocale($locale);
        $request->session()->put('tenant_locale', $locale);

        return $next($request);
    }
}
