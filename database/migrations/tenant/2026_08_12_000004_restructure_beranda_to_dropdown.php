<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Restruktur menu "Beranda" menjadi parent dropdown dengan 2 anak:
 *   - Beranda 1 -> /app/dashboard  (Dashboard Pembayaran)
 *   - Beranda 2 -> /app/landing    (Dashboard Landing)
 *
 * Sebelum migrasi: menu id=1 "Beranda" adalah flat menu (route=/app/dashboard).
 * Setelah migrasi: menu id=1 menjadi parent (route='#', parent_id=NULL),
 * dan ditambahkan 2 anak baru (id=25 dan id=26) yang parent_id=1.
 *
 * Backfill hak_akses user existing:
 *   - User dengan id=1 (parent Beranda) di hak_akses -> tambahkan 25 dan 26.
 *   - User tanpa id=1 tapi memiliki id=15/16/.../24 (landing group) ->
 *     tambahkan 26 (Beranda 2) agar konsisten dengan hak akses landing.
 *   - User lain (mis. bendahara/admin tanpa landing) -> tambahkan 25 saja.
 */
return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        // Ubah Beranda (id=1) menjadi parent dropdown.
        DB::table('menu')->where('id', 1)->update([
            'route'      => '#',
            'updated_at' => $now,
        ]);

        // Tambah Beranda 1 -> Dashboard Pembayaran.
        DB::table('menu')->insertOrIgnore([
            'id'         => 25,
            'parent_id'  => 1,
            'nama_menu'  => 'Beranda 1',
            'route'      => '/app/dashboard',
            'icon'       => 'payments',
            'urutan'     => 1,
            'status'     => 'aktif',
            'group'      => null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Tambah Beranda 2 -> Dashboard Landing. group='landing' agar user
        // yang memiliki ID 26 otomatis lolos middleware hak.akses:landing
        // (route /app/landing diproteksi middleware group 'landing').
        DB::table('menu')->insertOrIgnore([
            'id'         => 26,
            'parent_id'  => 1,
            'nama_menu'  => 'Beranda 2',
            'route'      => '/app/landing',
            'icon'       => 'language',
            'urutan'     => 2,
            'status'     => 'aktif',
            'group'      => 'landing',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $this->backfillHakAkses();
    }

    public function down(): void
    {
        $this->removeBerandaFromHakAkses();

        // Kembalikan Beranda ke kondisi flat awal.
        DB::table('menu')->whereIn('id', [25, 26])->delete();
        DB::table('menu')->where('id', 1)->update([
            'route'      => '/app/dashboard',
            'updated_at' => now(),
        ]);
    }

    private function backfillHakAkses(): void
    {
        $users = DB::table('users')->get(['id', 'hak_akses']);
        foreach ($users as $u) {
            $ids = $this->decodeHakAkses($u->hak_akses);
            if (empty($ids)) {
                continue;
            }

            $hasBerandaParent = in_array(1, $ids, true);
            $hasLandingChildren = (bool) array_intersect($ids, [15, 16, 17, 18, 19, 20, 21, 22, 23, 24]);

            if ($hasBerandaParent) {
                $ids[] = 25;
                if ($hasLandingChildren) {
                    $ids[] = 26;
                }
            } elseif ($hasLandingChildren) {
                $ids[] = 26;
            }

            $ids = array_values(array_unique(array_map('intval', $ids)));
            DB::table('users')->where('id', $u->id)->update([
                'hak_akses' => json_encode($ids),
                'updated_at' => now(),
            ]);
        }
    }

    private function removeBerandaFromHakAkses(): void
    {
        $users = DB::table('users')->get(['id', 'hak_akses']);
        foreach ($users as $u) {
            $ids = $this->decodeHakAkses($u->hak_akses);
            if (empty($ids)) {
                continue;
            }

            $ids = array_values(array_diff($ids, [25, 26]));
            DB::table('users')->where('id', $u->id)->update([
                'hak_akses' => json_encode($ids),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Decode kolom hak_akses (JSON-string atau array) menjadi array<int>.
     */
    private function decodeHakAkses($raw): array
    {
        if ($raw === null || $raw === '') {
            return [];
        }
        if (is_array($raw)) {
            $arr = $raw;
        } else {
            $arr = json_decode((string) $raw, true);
        }
        $arr = is_array($arr) ? $arr : [];
        return array_values(array_unique(array_map('intval', $arr)));
    }
};
