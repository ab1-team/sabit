<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\Domain;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class EnsureDomainType
{
    public function handle(Request $request, Closure $next, string $type): Response
    {
        $host = $request->getHost();
        $cacheKey = "domain:type:{$host}";

        $typeActual = Cache::remember($cacheKey, 7200, function () use ($host) {
            $d = Domain::query()->where('domain', $host)->select('type')->first();
            return $d?->type;
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
