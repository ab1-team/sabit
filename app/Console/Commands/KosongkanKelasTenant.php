<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;

class KosongkanKelasTenant extends Command
{
    protected $signature = 'tenant:kosongkan-kelas
                            {--tenant= : Hanya proses 1 tenant dengan ID spesifik}
                            {--dry-run : Tampilkan jumlah baris yang akan dihapus tanpa menulis}';

    protected $description = 'Kosongkan tabel kelas di tenant (atau semua tenant). Mengikuti pipeline tenant:loop.';

    public function handle(): int
    {
        $tenants = $this->option('tenant')
            ? Tenant::where('id', $this->option('tenant'))->get()
            : Tenant::all();

        if ($tenants->isEmpty()) {
            $this->warn('Tenant tidak ditemukan.');
            return self::SUCCESS;
        }

        $dryRun = (bool) $this->option('dry-run');
        $totalAffected = 0;

        foreach ($tenants as $tenant) {
            $tid = $tenant->id;
            try {
                tenancy()->initialize($tenant);
                $count = \DB::table('kelas')->count();

                if ($dryRun) {
                    $this->line(sprintf('[%s] %s — akan menghapus %d baris kelas', $tid, $tenant->nama_sekolah ?? '(?)', $count));
                } else {
                    if ($count === 0) {
                        $this->line(sprintf('[%s] %s — sudah kosong (0 baris), skip', $tid, $tenant->nama_sekolah ?? '(?)'));
                    } else {
                        $deleted = \DB::table('kelas')->delete();
                        $this->line(sprintf('[%s] %s — dihapus %d baris kelas', $tid, $tenant->nama_sekolah ?? '(?)', $deleted));
                        $totalAffected += $deleted;
                    }
                }
            } catch (\Throwable $e) {
                $this->error(sprintf('[%s] ERROR: %s', $tid, $e->getMessage()));
            } finally {
                tenancy()->end();
            }
        }

        $this->newLine();
        if ($dryRun) {
            $this->info('Mode dry-run: tidak ada perubahan data.');
        } else {
            $this->info("Selesai. Total baris dihapus: {$totalAffected}");
        }
        return self::SUCCESS;
    }
}