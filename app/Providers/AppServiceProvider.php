<?php

namespace App\Providers;

use App\Models\Profil;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\DatabaseConfig;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Paginator::useBootstrapFive();

        DatabaseConfig::generateDatabaseNamesUsing(function (TenantWithDatabase $tenant) {
            // Nama DB tenant SELALU dibaca dari kolom `tenancy_db_name` di tabel tenants.
            // Kalau kosong, fallback ke prefix + tenant_id.
            $explicit = $tenant->getAttribute('tenancy_db_name');
            if (! empty($explicit)) {
                return $explicit;
            }

            $internal = $tenant->getInternal('db_name');
            if (! empty($internal)) {
                return $internal;
            }

            return config('tenancy.database.prefix')
                . $tenant->getTenantKey()
                . config('tenancy.database.suffix', '');
        });

        // View composer: jalan setiap kali view 'layouts.tenant.base' di-render,
        // sehingga data profil SELALU diambil dengan koneksi tenant yang sedang
        // aktif (bukan koneksi central seperti di boot()).
        View::composer('layouts.tenant.base', function ($view) {
            $profil = Profil::safeFirst();

            $view->with('profil', $profil);
            $view->with('appLogoUrl', Profil::logoUrl());
            $view->with('appName', $profil->nama ?? config('app.name'));
        });
    }
}
