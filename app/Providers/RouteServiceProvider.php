<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/dashboard';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));

            // Route tenant didaftarkan di sini (bukan di TenancyServiceProvider)
            // karena $this->routes() menandai routing selesai di-load. Provider
            // yang boot setelahnya tidak akan masuk pipeline request meskipun
            // route-nya tampil pada `php artisan route:list`.
            $this->mapTenantRoutes();
        });
    }

    /**
     * Dua grup route terpisah berdasarkan tipe domain tenant:
     *   - routes/tenant-landing.php => domain landing (sma1.example.test)
     *   - routes/tenant.php         => domain admin   (admin-sma1.example.test)
     *
     * Keduanya mendefinisikan URI '/' sehingga WAJIB dibatasi Route::domain().
     * Tanpa itu, Laravel menimpa route ber-URI+method identik dan grup yang
     * didaftarkan terakhir akan menang tanpa middleware pernah dievaluasi.
     */
    protected function mapTenantRoutes(): void
    {
        $base = [
            'web',
            Middleware\PreventAccessFromCentralDomains::class,
            Middleware\InitializeTenancyByDomain::class,
        ];

        $domains = $this->tenantDomains();

        if (file_exists(base_path('routes/tenant-landing.php'))) {
            foreach ($domains['landing'] as $domain) {
                Route::domain($domain)
                    ->middleware(array_merge($base, ['domain.type:landing']))
                    ->group(base_path('routes/tenant-landing.php'));
            }
        }

        if (file_exists(base_path('routes/tenant.php'))) {
            foreach ($domains['admin'] as $domain) {
                Route::domain($domain)
                    ->middleware(array_merge($base, ['domain.type:admin']))
                    ->group(base_path('routes/tenant.php'));
            }
        }
    }

    /**
     * Daftar domain tenant per tipe, diambil dari tabel domains pada koneksi central.
     *
     * Dibungkus try/catch agar artisan tetap dapat dijalankan saat database
     * belum tersedia (mis. migrate pertama kali atau saat CI build).
     */
    protected function tenantDomains(): array
    {
        $result = ['admin' => [], 'landing' => []];

        try {
            $rows = DB::connection(config('tenancy.database.central_connection'))
                ->table('domains')
                ->select('domain', 'type')
                ->get();

            foreach ($rows as $row) {
                $type = $row->type ?: 'admin';

                if (isset($result[$type])) {
                    $result[$type][] = $row->domain;
                }
            }
        } catch (\Throwable $e) {
            // Database belum siap; route tenant dilewati.
        }

        return $result;
    }
}
