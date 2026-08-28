<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Stancl\Tenancy\Events\TenancyInitialized;
use Stancl\Tenancy\Events\TenancyEnded;

class TenantStorageServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $apply = function (?string $tenantId): void {
            $root = $tenantId
                ? storage_path('app/public/tenant/' . $tenantId)
                : storage_path('app/public');

            $url = $tenantId
                ? '/storage/tenant/' . $tenantId
                : '/storage';

            $this->app['config']->set('filesystems.disks.public', [
                'driver'     => 'local',
                'root'       => $root,
                'url'        => $url,
                'visibility' => 'public',
                'throw'      => false,
            ]);

            // Flush cache FilesystemManager agar disk 'public' di-resolve ulang
            $this->app->forgetInstance('filesystem');
            $this->app->forgetInstance('filesystem.disk');

            if ($tenantId && !is_dir($root)) {
                @mkdir($root, 0775, true);
                foreach (['logo', 'siswa', 'users', 'tanda-tangan', 'landing', 'posts', 'galleries'] as $sub) {
                    @mkdir($root . DIRECTORY_SEPARATOR . $sub, 0775, true);
                }
            }
        };

        $this->app['events']->listen(TenancyInitialized::class, function (TenancyInitialized $event) use ($apply) {
            $apply((string) $event->tenancy->tenant->getKey());
        });

        $this->app['events']->listen(TenancyEnded::class, function () use ($apply) {
            $apply(null);
        });
    }
}
