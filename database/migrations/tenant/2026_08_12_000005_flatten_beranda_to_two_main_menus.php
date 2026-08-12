<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Restruktur menu Beranda jadi 2 menu utama (flat, tanpa dropdown):
 *   - Beranda          (id=1)  -> /app/dashboard  (Dashboard Pembayaran)
 *   - Beranda Layanan  (id=26) -> /app/landing    (Dashboard Landing Page)
 *
 * Sebelum migrasi ini: Beranda (id=1) adalah parent dropdown dengan anak
 * Beranda 1 (id=25, /app/dashboard) dan Beranda 2 (id=26, /app/landing).
 *
 * Sesudah migrasi ini:
 *   - Beranda (id=1) kembali ke kondisi flat: route=/app/dashboard, parent_id=NULL
 *   - Beranda 1 (id=25) DIHAPUS
 *   - Beranda 2 (id=26) di-rename jadi 'Beranda Layanan', route tetap /app/landing,
 *     parent_id=NULL, group tetap 'landing' agar middleware hak.akses:landing
 *     tetap melewatkan user yang memegang ID 26.
 *
 * Backfill hak_akses:
 *   - User dengan hak_akses mengandung 25 -> 25 diganti 1 (alias Beranda 1 = Beranda)
 *   - User tanpa 1 tapi punya 25 -> tambahkan 1
 *   - User dengan 26 tetap (sekarang disebut Beranda Layanan)
 */
return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        // Kembalikan Beranda (id=1) ke kondisi flat: parent dropdown, route=/app/dashboard.
        DB::table('menu')->where('id', 1)->update([
            'parent_id'  => null,
            'route'      => '/app/dashboard',
            'updated_at' => $now,
        ]);

        // Rename Beranda 2 (id=26) -> Beranda Layanan, pastikan parent_id null.
        DB::table('menu')->where('id', 26)->update([
            'parent_id'  => null,
            'nama_menu'  => 'Beranda Layanan',
            'route'      => '/app/landing',
            'group'      => 'landing',
            'updated_at' => $now,
        ]);

        // Hapus Beranda 1 (id=25) — sudah digantikan Beranda (id=1).
        DB::table('menu')->where('id', 25)->delete();

        $this->backfillHakAkses();
    }

    public function down(): void
    {
        $now = now();

        // Kembalikan Beranda 1 (id=25) sebagai child dari Beranda.
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

        // Kembalikan Beranda 2 (id=26) sebagai child dari Beranda.
        DB::table('menu')->where('id', 26)->update([
            'parent_id'  => 1,
            'nama_menu'  => 'Beranda 2',
            'route'      => '/app/landing',
            'group'      => 'landing',
            'updated_at' => $now,
        ]);

        // Beranda jadi parent dropdown.
        DB::table('menu')->where('id', 1)->update([
            'route'      => '#',
            'updated_at' => $now,
        ]);

        $this->reverseBackfillHakAkses();
    }

    /**
     * Setelah migrasi: hak_akses user yang punya 25 (Beranda 1) -> dapat 1 (Beranda).
     * User yang punya 26 (Beranda 2) tetap punya 26 (sekarang Beranda Layanan).
     * Bersihkan juga referensi 25 yang sudah orphaned (id menu dihapus).
     */
    private function backfillHakAkses(): void
    {
        $users = DB::table('users')->get(['id', 'hak_akses']);
        foreach ($users as $u) {
            $ids = $this->decode($u->hak_akses);
            if (empty($ids)) {
                continue;
            }

            $changed = false;

            // Tambahkan 1 (Beranda) untuk user yang punya 25 tapi belum punya 1.
            if (in_array(25, $ids, true) && !in_array(1, $ids, true)) {
                $ids[] = 1;
                $changed = true;
            }

            // Buang 25 orphaned references (menu Beranda 1 sudah dihapus).
            if (in_array(25, $ids, true)) {
                $ids = array_values(array_diff($ids, [25]));
                $changed = true;
            }

            if ($changed) {
                $ids = array_values(array_unique(array_map('intval', $ids)));
                DB::table('users')->where('id', $u->id)->update([
                    'hak_akses' => json_encode($ids),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Rollback: jika user punya 1 (Beranda) tapi tidak punya 25 (Beranda 1),
     * jangan auto-add 25. Cukup diamkan saja — 25 sudah dihapus, dan 1 tetap valid.
     */
    private function reverseBackfillHakAkses(): void
    {
        // Tidak ada perubahan karena user dengan 1 (Beranda) sudah benar
        // untuk versi parent-dropdown juga (parent ditampilkan di sidebar).
    }

    private function decode($raw): array
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
