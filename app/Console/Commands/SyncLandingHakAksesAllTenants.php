<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;

class SyncLandingHakAksesAllTenants extends Command
{
    protected $signature = 'landing:sync-hak-akses-all {--dry-run : Hanya tampilkan perubahan tanpa simpan}';

    protected $description = 'Sync hak akses user landing + admin ke semua tenant';

    public function handle(): int
    {
        $tenants = Tenant::all();

        if ($tenants->isEmpty()) {
            $this->warn('Tidak ada tenant.');
            return self::SUCCESS;
        }

        foreach ($tenants as $tenant) {
            tenancy()->initialize($tenant);
            try {
                $this->line("--- Tenant: {$tenant->id} ({$tenant->nama_sekolah}) ---");
                $exit = $this->call('landing:sync-hak-akses', [
                    '--dry-run' => $this->option('dry-run'),
                ]);
                if ($exit !== self::SUCCESS) {
                    $this->error("Sync gagal untuk tenant {$tenant->id}");
                }
            } catch (\Throwable $e) {
                $this->error("Error tenant {$tenant->id}: " . $e->getMessage());
            } finally {
                tenancy()->end();
            }
        }

        return self::SUCCESS;
    }
}
