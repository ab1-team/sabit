<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Sinkronkan hak_akses user di tenant saat ini dengan menu aktif grup
 * 'landing'. User yang sebelumnya sudah punya akses ke salah satu menu
 * landing (id 15..17) akan otomatis mendapat menu landing baru (id 18+)
 * yang ditambahkan lewat migration.
 *
 * Aman untuk dijalankan berulang: idempotent.
 */
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

        $landingMenuIds = DB::table('menu')
            ->where('group', 'landing')
            ->where('status', 'aktif')
            ->pluck('id')
            ->map(fn ($v) => (int) $v)
            ->all();

        if (empty($landingMenuIds)) {
            $this->warn('Tidak ada menu landing aktif di tenant ini.');
            return self::SUCCESS;
        }

        $dryRun = (bool) $this->option('dry-run');
        $updated = 0;
        $skipped = 0;

        User::query()->each(function (User $user) use ($landingMenuIds, $dryRun, &$updated, &$skipped) {
            $current = collect((array) ($user->hak_akses ?? []))
                ->map(fn ($v) => (int) $v)
                ->filter()
                ->all();

            $hasAllAccess = in_array('*', $current, true);
            $hasLanding = (bool) array_intersect($current, [15, 16, 17]);

            // User 'admin' / 'landing' maupun user wildcard dapat semua menu landing.
            // User biasa yang punya salah satu menu landing dianggap admin landing,
            // dapat seluruh menu landing. User tanpa akses landing di-skip.
            $isLandingAdmin = in_array(strtolower((string) $user->username), ['admin', 'landing'], true)
                || $hasAllAccess
                || $hasLanding;

            if (!$isLandingAdmin) {
                $skipped++;
                return;
            }

            $merged = collect($current)
                ->merge($landingMenuIds)
                ->unique()
                ->map(fn ($v) => (int) $v)
                ->values()
                ->all();

            if ($merged === $current) {
                return;
            }

            if ($dryRun) {
                $this->line(sprintf(
                    '[dry-run] %s: %s → %s',
                    $user->username,
                    implode(',', $current),
                    implode(',', $merged)
                ));
            } else {
                $user->hak_akses = $merged;
                $user->save();
            }

            $updated++;
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
