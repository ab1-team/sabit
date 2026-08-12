<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LocaleController extends Controller
{
    public function switch(Request $request)
    {
        $locale = $request->input('locale', 'id');
        $locales = config('app.available_locales', ['id', 'en', 'ar']);

        if (!in_array($locale, $locales, true)) {
            $locale = config('app.locale', 'id');
        }

        $request->session()->put('tenant_locale', $locale);
        app()->setLocale($locale);

        return redirect()->back(fallback: route('tenant.dashboard'));
    }
}
