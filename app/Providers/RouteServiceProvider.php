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
     * Daftarkan route publik & admin tenant.
     *
     * PENTING — perubahan penting dari versi sebelumnya:
     *
     *   Versi sebelumnya mendaftarkan file route di dalam loop
     *   `foreach ($domain as $d) { Route::domain($d)->group(...) }`.
     *   Akibatnya, setiap tenant punya salinan route dengan nama yang SAMA
     *   (mis. 'halaman-publik.beranda'), dan helper `route('halaman-publik.beranda')`
     *   di view Blade TIDAK memfilter berdasarkan current host — Laravel
     *   hanya mengambil route pertama dengan nama itu. Hasilnya: link di
     *   landing.sabit.test (tenant demo) merujuk ke URL absolut sma1.sabit.test
     *   (tenant sma), sehingga klik "Lihat Semua" / menu navigasi melompat
     *   ke domain tenant lain.
     *
     *   Solusi: daftarkan SETIAP route file SATU KALI tanpa `Route::domain()`,
     *   dan biarkan middleware `InitializeTenancyByDomain` + `EnsureDomainType`
     *   (yang sudah jalan duluan) yang memvalidasi host. Middleware akan
     *   abort(404) untuk host yang tidak valid.
     *
     *   - routes/tenant-halaman-publik.php => route publik (galeri, berita, dll)
     *   - routes/tenant-admin.php           => route admin sekolah (/login, /app/*)
     *
     *   Middleware yang membatasi host ada di group 'web' (lihat Kernel.php)
     *   sehingga tidak ada route publik yang bocor ke host central.
     */
    protected function mapTenantRoutes(): void
    {
        $base = [
            'web',
            Middleware\PreventAccessFromCentralDomains::class,
            Middleware\InitializeTenancyByDomain::class,
        ];

        $domains = $this->tenantDomains();

        // 1. Route admin publik PER-DOMAIN admin: 'GET /' (tenant.home),
        //    'GET /login', 'POST /auth/login', 'GET /link'. Didaftarkan
        //    DENGAN Route::domain() supaya match duluan dan tidak ter-match
        //    oleh catch-all {slug} di step 2.
        foreach ($domains['admin'] as $domain) {
            Route::domain($domain)
                ->middleware(array_merge($base, ['domain.type:admin']))
                ->group(function () {
                    Route::get('/', function () {
                        return redirect(url('/login'));
                    })->name('tenant.home');

                    Route::get('/login', [\App\Http\Controllers\AuthController::class, 'index'])
                        ->name('login');

                    Route::post('/auth/login', [\App\Http\Controllers\AuthController::class, 'login'])
                        ->name('auth.login');

                    Route::get('/link', function () {
                        $target = base_path('storage/app/public');
                        $shortcut = public_path('storage');
                        $alreadyLinked = is_link($shortcut)
                            || (DIRECTORY_SEPARATOR === '\\' && file_exists($shortcut) && strtolower(readlink($shortcut) ?: '') !== '');
                        if ($alreadyLinked) {
                            return response()->json(['status' => 'ok', 'message' => 'Symlink already exists.']);
                        }
                        try {
                            if (DIRECTORY_SEPARATOR === '\\') {
                                $cmd = sprintf('mklink /J %s %s', escapeshellarg($shortcut), escapeshellarg($target));
                                exec($cmd, $out, $rc);
                                if ($rc !== 0) {
                                    return response()->json(['status' => 'error', 'message' => 'Failed: ' . implode("\n", $out)], 500);
                                }
                            } else {
                                symlink($target, $shortcut);
                            }
                            return response()->json(['status' => 'ok', 'message' => 'Symlink created.']);
                        } catch (\Throwable $e) {
                            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
                        }
                    });
                });
        }

        // 2. Daftarkan route publik landing TANPA filter domain, supaya
        //    URI 'GET /', 'GET /berita', 'GET /galeri' dll. terdaftar satu kali.
        //    Host scoping oleh middleware 'domain.type:landing'.
        if (file_exists(base_path('routes/tenant-halaman-publik.php'))) {
            Route::middleware(array_merge($base, ['domain.type:landing']))
                ->group(base_path('routes/tenant-halaman-publik.php'));
        }

        // 3. Daftarkan route admin tenant (app/*) — semua di bawah group
        //    auth. Host scoping lewat middleware 'domain.type:admin'.
        if (file_exists(base_path('routes/tenant-admin.php'))) {
            Route::middleware(array_merge($base, ['domain.type:admin']))
                ->group(base_path('routes/tenant-admin.php'));
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
