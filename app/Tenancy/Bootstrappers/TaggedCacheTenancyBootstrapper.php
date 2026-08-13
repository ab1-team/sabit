<?php

declare(strict_types=1);

namespace App\Tenancy\Bootstrappers;

use App\Cache\TaggedDatabaseStore;
use Illuminate\Cache\CacheManager;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Cache;
use Stancl\Tenancy\Contracts\TenancyBootstrapper;
use Stancl\Tenancy\Contracts\Tenant;

/**
 * Drop-in pengganti Stancl\Tenancy\Bootstrappers\CacheTenancyBootstrapper.
 *
 * Yang dilakukan:
 *   1. Meng-install Cache::extend('database', ...) ke CacheManager aktif,
 *      sehingga driver 'database' resolve ke TaggedDatabaseStore (yang
 *      mengimplementasikan ->tags() dan supaya
 *      Illuminate\Cache\Repository::tags() tidak melempar
 *      "This cache store does not support tagging.").
 *   2. Mem-flush resolved cache instances (Repository) di Cache facade
 *      agar tidak ada store yang sudah ter-resolve dengan DatabaseStore
 *      bawaan. Repository baru akan resolve melalui creator custom di
 *      atas pada pemakaian berikutnya.
 */
class TaggedCacheTenancyBootstrapper implements TenancyBootstrapper
{
    public function __construct(protected Application $app)
    {
    }

    public function bootstrap(Tenant $tenant): void
    {
        // Lupakan CacheManager lama agar fresh instance di-resolve, lalu
        // pasang extender yang akan menambahkan creator 'database' ke
        // setiap CacheManager baru yang dibuat.
        Cache::clearResolvedInstances();
        $this->app->forgetInstance('cache');
        $this->app->forgetInstance(CacheManager::class);
        $this->app->forgetInstance(\Illuminate\Contracts\Cache\Factory::class);

        $this->app->extend('cache', function ($manager, $app) {
            $this->installCreator($manager);
            return $manager;
        });
    }

    public function revert(): void
    {
        Cache::clearResolvedInstances();
        $this->app->forgetInstance('cache');
        $this->app->forgetInstance(CacheManager::class);
        $this->app->forgetInstance(\Illuminate\Contracts\Cache\Factory::class);

        $this->app->forgetExtenders('cache');
        $this->app->forgetExtenders(CacheManager::class);
    }

    /**
     * Tambah custom creator 'database' ke CacheManager sehingga driver
     * 'database' akan di-resolve ke TaggedDatabaseStore (yang punya
     * ->tags()). Laravel's Cache::tags() di facade butuh
     * CacheManager::store()->supportsTags() return true, sedangkan
     * DatabaseStore bawaan tidak punya method tags().
     */
    private function installCreator(CacheManager $manager): void
    {
        $manager->extend('database', function ($app) use ($manager) {
            $config = config('cache.stores.database', []);
            $connection = $app['db']->connection($config['connection'] ?? null);
            $lockConnection = $app['db']->connection($config['lock_connection'] ?? $config['connection'] ?? null);

            $store = (new TaggedDatabaseStore(
                $connection,
                $config['table'] ?? 'cache',
                config('cache.prefix'),
                $config['lock_table'] ?? 'cache_locks',
                $config['lock_lottery'] ?? [2, 100],
                $config['lock_timeout'] ?? 86400,
            ))->setLockConnection($lockConnection);

            return $manager->repository($store);
        });
    }
}
