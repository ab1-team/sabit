<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

/**
 * Menegakkan hak_akses di level route, bukan hanya menyembunyikan menu di sidebar.
 *
 * hak_akses pada tabel users tenant berisi array ID menu (integer eksplisit,
 * tidak ada wildcard '*'). Middleware ini memetakan nama group menu (mis.
 * "landing") ke ID menu-nya, lalu memastikan user punya salah satu ID
 * tersebut lewat array_intersect.
 *
 * Untuk "superadmin" (admin tenant), gunakan saja ID seluruh menu aktif di
 * hak_akses saat pembuatannya — middleware secara otomatis lolos karena
 * array_intersect akan selalu non-kosong untuk group manapun.
 */
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
            // Backward-compat: tenant lama mungkin masih pakai wildcard.
            return $next($request);
        }

        $allowedIds = collect((array) $hakAkses)
            ->map(fn ($v) => (int) $v)
            ->filter()
            ->all();

        if (empty($allowedIds)) {
            abort(403, 'Anda tidak memiliki hak akses untuk halaman ini.');
        }

        $groupMenuIds = DB::table('menu')
            ->where('group', $group)
            ->where('status', 'aktif')
            ->pluck('id')
            ->map(fn ($v) => (int) $v)
            ->all();

        if (empty($groupMenuIds)) {
            abort(403, 'Menu untuk grup ini belum tersedia.');
        }

        if (empty(array_intersect($allowedIds, $groupMenuIds))) {
            abort(403, 'Anda tidak memiliki hak akses untuk halaman ini.');
        }

        return $next($request);
    }
}
