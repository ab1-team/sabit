<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\Domain;
use App\Support\HostContext;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class EnsureDomainType
{
    public function handle(Request $request, Closure $next, string $type): Response
    {
        $host = $request->getHost();

        if (HostContext::isCentral($host)) {
            abort(404);
        }

        $cacheKey = "domain:type:{$host}";

        // Pakai cache::remember supaya tidak query DB tiap request.
        // Key SELALU berisi data dari central DB (tabel `domains`) sehingga
        // semua tenant baca entry yang sama. flushHostCache() dipanggil
        // setiap kali tabel domains berubah.
        $typeActual = Cache::remember($cacheKey, 7200, function () use ($host) {
            return Domain::query()->where('domain', $host)->value('type');
        });

        if (!$typeActual) {
            abort(404);
        }

        if ($typeActual !== $type) {
            abort(404);
        }

        $request->attributes->set('domain_type', $typeActual);

        return $next($request);
    }

    public static function flushHostCache(string $host): void
    {
        Cache::forget("domain:type:{$host}");
    }
}
