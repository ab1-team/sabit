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
            return config('tenancy.database.prefix');
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
