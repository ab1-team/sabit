<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class EnsureHakAkses
{
    public function handle(Request $request, Closure $next, string $group): Response
    {
        $user = Auth::user();

        if (!$user) {
            abort(403);
        }

        $hakAkses = $user->hak_akses ?? [];

        if (in_array('*', (array) $hakAkses, true)) {
            return $next($request);
        }

        $allowedIds = collect((array) $hakAkses)
            ->map(fn ($v) => (int) $v)
            ->filter()
            ->all();

        if (empty($allowedIds)) {
            abort(403, 'Anda tidak memiliki hak akses untuk halaman ini.');
        }

        $groupMenuIds = $this->groupMenuIds($group);

        if (empty($groupMenuIds)) {
            abort(403, 'Menu untuk grup ini belum tersedia.');
        }

        if (empty(array_intersect($allowedIds, $groupMenuIds))) {
            abort(403, 'Anda tidak memiliki hak akses untuk halaman ini.');
        }

        return $next($request);
    }

    private function groupMenuIds(string $group): array
    {
        $cacheKey = self::groupCacheKey($group);

        return Cache::remember($cacheKey, 7200, function () use ($group) {
            return DB::table('menu')
                ->where('group', $group)
                ->where('status', 'aktif')
                ->pluck('id')
                ->map(fn ($v) => (int) $v)
                ->all();
        });
    }

    private static function groupCacheKey(string $group): string
    {
        return "menu:group:{$group}:" . (tenant('id') ?? 'central');
    }

    public static function flushGroupCache(string $group): void
    {
        Cache::forget(self::groupCacheKey($group));
    }

    public static function flushAllGroupCache(): void
    {
        $groups = DB::table('menu')->distinct()->pluck('group')->all();
        foreach ($groups as $g) {
            if ($g !== null) {
                Cache::forget(self::groupCacheKey($g));
            }
        }
    }
}
