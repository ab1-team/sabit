<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\Domain;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Memastikan host request cocok dengan tipe domain yang diizinkan route.
 *
 * Tenancy hanya tahu "domain ini milik tenant siapa", bukan "domain ini untuk apa".
 * Middleware ini menutup celah tersebut: tanpa pengecekan ini, /app/dashboard bisa
 * dibuka dari domain landing yang publik.
 */
class EnsureDomainType
{
    public function handle(Request $request, Closure $next, string $type): Response
    {
        $host = $request->getHost();

        $domain = Domain::query()
            ->where('domain', $host)
            ->first();

        if (!$domain) {
            abort(404);
        }

        if ($domain->type !== $type) {
            abort(404);
        }

        $request->attributes->set('domain_type', $domain->type);

        return $next($request);
    }
}
