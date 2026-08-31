<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Tenant;
use Database\Seeders\KurikulumSeeder;
use Illuminate\Console\Command;

class RunKurikulumSeederAllTenants extends Command
{
    protected $signature = 'kurikulum:seed-all';

    protected $description = 'Panggil KurikulumSeeder untuk setiap tenant yang ada (idempotent — aman untuk tenant baru maupun lama).';

    public function handle(): int
    {
        $tenants = Tenant::all();
        if ($tenants->isEmpty()) {
            $this->warn('Tidak ada tenant.');
            return self::SUCCESS;
        }

        $seeder = new KurikulumSeeder();

        foreach ($tenants as $tenant) {
            try {
                tenancy()->initialize($tenant);
                $before = \DB::table('kurikulum')->count();
                $seeder->setContainer(app())->run();
                $after = \DB::table('kurikulum')->count();
                $added = $after - $before;
                $this->line("[{$tenant->id}] {$tenant->nama_sekolah} — total kurikulum: {$before} → {$after} (+{$added})");
            } catch (\Throwable $e) {
                $this->error("[{$tenant->id}] ERROR: " . $e->getMessage());
            } finally {
                tenancy()->end();
            }
        }

        return self::SUCCESS;
    }
}
