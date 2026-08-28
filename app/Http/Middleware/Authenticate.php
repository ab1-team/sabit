<?php

namespace App\Http\Middleware;

use App\Support\HostContext;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    protected function redirectTo(Request $request): ?string
    {
        $host = $request->getHost();

        if (HostContext::isCentral($host)) {
            // Host pusat → route login pusat (tenant.login).
            return route('tenant.login');
        }

        if (! $request->expectsJson()) {
            // Host tenant admin → pakai url() bukan route('login') supaya
            // route generator pakai current host (SyncAppUrlFromRequest
            // middleware sudah sync app.url dengan host request).
            // route('login') bisa resolve ke first match by name (yang
            // belum tentu current host bila ada >1 admin domain).
            return url('/login');
        }

        return null;
    }
}
