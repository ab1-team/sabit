<?php

namespace App\Http\Middleware;

use App\Support\HostContext;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    protected function redirectTo(Request $request): ?string
    {
        if (HostContext::isCentral($request->getHost())) {
            return route('tenant.login');
        }

        if (! $request->expectsJson()) {
            return route('login');
        }

        return null;
    }
}