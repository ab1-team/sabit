<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    protected function redirectTo(Request $request): ?string
    {
        if ($request->routeIs('tenant.*') || $request->getHost() === config('tenancy.central_domains')[0] ?? false) {
            return route('tenant.login');
        }

        if (! $request->expectsJson()) {
            return route('login');
        }

        return null;
    }
}
