<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class SyncAppUrlFromRequest
{
    /**
     * Sinkronkan `app.url` config & URL generator root dengan host request.
     *
     * Tanpa ini, kalau `APP_URL` di .env production masih `http://sabit.test`
     * (atau nilai basi lain), `url()` helper akan menghasilkan URL absolut
     * ke host basi tersebut meskipun route sudah benar.
     *
     * `route()` helper sudah otomatis host-aware, tapi `asset()` dan
     * `url()` tidak — mereka pakai `app.url` config.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $configured = (string) config('app.url');
        $requestHost = $request->getHost();

        // Kalau APP_URL host tidak cocok dengan host request, override
        // config + URL generator force root URL.
        if ($requestHost && $configured && ! $this->sameHost($configured, $requestHost)) {
            $scheme = $request->isSecure() ? 'https' : 'this scheme';
            $scheme = $request->isSecure() ? 'https' : (parse_url($configured, PHP_URL_SCHEME) ?: 'http');

            $newUrl = $scheme . '://' . $requestHost;

            config(['app.url' => $newUrl]);

            URL::forceRootUrl($newUrl);
            if ($request->isSecure()) {
                URL::forceScheme('https');
            }
        }

        return $next($request);
    }

    private function sameHost(string $url, string $host): bool
    {
        $parsed = parse_url($url, PHP_URL_HOST);
        return $parsed && strtolower($parsed) === strtolower($host);
    }
}