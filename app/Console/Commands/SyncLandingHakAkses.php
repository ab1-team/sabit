<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SyncLandingHakAkses extends Command
{
    protected $signature = 'landing:sync-hak-akses {--dry-run : Hanya tampilkan perubahan tanpa simpan}';

    protected $description = 'Sync hak akses user dengan menu landing yang aktif';

    public function handle(): int
    {
        $tenant = tenant();

        if (!$tenant) {
            $this->error('Perintah ini harus dijalankan dalam konteks tenant.');
            return self::FAILURE;
        }

        $landingMenuIds = Cache::remember('menu:group:landing:' . $tenant->id, 7200, function () {
            return DB::table('menu')
                ->where('group', 'landing')
                ->where('status', 'aktif')
                ->pluck('id')
                ->map(fn ($v) => (int) $v)
                ->all();
        });

        if (empty($landingMenuIds)) {
            $this->warn('Tidak ada menu landing aktif di tenant ini.');
            return self::SUCCESS;
        }

        $dryRun = (bool) $this->option('dry-run');
        $updated = 0;
        $skipped = 0;

        User::query()
            ->select(['id', 'username', 'hak_akses'])
            ->chunkById(300, function ($users) use ($landingMenuIds, $dryRun, &$updated, &$skipped) {
                foreach ($users as $user) {
                    $current = collect((array) ($user->hak_akses ?? []))
                        ->map(fn ($v) => (int) $v)
                        ->filter()
                        ->all();

                    $hasAllAccess = in_array('*', $current, true);
                    $hasLanding = (bool) array_intersect($current, [15, 16, 17]);

                    $isLandingAdmin = in_array(strtolower((string) $user->username), ['admin', 'landing'], true)
                        || $hasAllAccess
                        || $hasLanding;

                    if (!$isLandingAdmin) {
                        $skipped++;
                        continue;
                    }

                    $merged = collect($current)
                        ->merge($landingMenuIds)
                        ->unique()
                        ->map(fn ($v) => (int) $v)
                        ->values()
                        ->all();

                    if ($merged === $current) {
                        continue;
                    }

                    if ($dryRun) {
                        $this->line(sprintf(
                            '[dry-run] %s: %s → %s',
                            $user->username,
                            implode(',', $current),
                            implode(',', $merged)
                        ));
                    } else {
                        DB::table('users')
                            ->where('id', $user->id)
                            ->update(['hak_akses' => json_encode($merged)]);
                    }

                    $updated++;
                }
            });

        $this->info(sprintf(
            '%s%d user di-update, %d dilewati (tanpa hak akses landing).',
            $dryRun ? '[dry-run] ' : '',
            $updated,
            $skipped
        ));

        return self::SUCCESS;
    }
}
