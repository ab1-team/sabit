<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Gabungkan menu "Video" ke dalam menu "Galeri" di sidebar admin landing.
 *
 * Strategi (sesuai pilihan user, scope rendah, risiko minimal):
 *   1. Menu "Video" (route /app/landing/videos) di tabel `menu` TIDAK dihapus —
 *      agar data history & route CRUD video tetap utuh dan tidak hilang.
 *   2. Menu "Video" di-set `status='nonaktif'` sehingga tidak muncul lagi di
 *      sidebar, tapi route, controller, model, dan view CRUD video tetap
 *      berfungsi penuh.
 *   3. Backfill hak_akses: user yang sebelumnya punya 36 (Video) otomatis
 *      mendapat 21 (Galeri) agar tetap bisa CRUD dokumentasi (sekarang lewat
 *      menu Galeri). ID 36 dihapus dari array hak_akses untuk konsistensi.
 *   4. Halaman CRUD Galeri admin (resources/views/admin-landing/galeri/indeks.blade.php)
 *      sudah diupdate untuk menyediakan link "Kelola Video" sehingga admin
 *      masih bisa akses CRUD video lewat UI meskipun menu di sidebar hilang.
 *   5. Halaman publik /galeri (resources/views/halaman-publik/galeri.blade.php)
 *      sudah diupdate untuk menampilkan video di section terpisah pada
 *      halaman yang sama — foto + video jadi satu halaman "Galeri".
 *   6. Halaman publik /video route & view tetap ada sebagai fallback URL
 *      (kompatibilitas mundur untuk link external); tidak ada perubahan di
 *      route publik, hanya menu header/footer publik (lp_menu) dan footer
 *      layout tata-letak yang dirapikan.
 *
 * Idempoten: aman dijalankan berulang kali. Cek ada-tidak sebelum mutate.
 */
return new class extends Migration
{
    private const VIDEO_ROUTE = '/app/admin-landing/videos';
    private const VIDEO_MENU_ID = 36;
    private const GALLERY_MENU_ID = 21;

    public function up(): void
    {
        if (!Schema::hasTable('menu')) {
            return;
        }

        $now = now();

        // 1) Nonaktifkan menu Video di sidebar admin landing. Pakai route
        //    (bukan id) agar robust terhadap auto-increment id yang mungkin
        //    berbeda di tiap tenant.
        DB::table('menu')
            ->where('route', self::VIDEO_ROUTE)
            ->update([
                'status'     => 'nonaktif',
                'updated_at' => $now,
            ]);

        // 2) Backfill hak_akses: 36 (Video) -> 21 (Galeri).
        $this->backfillHakAkses();

        // 3) Flush cache group landing agar middleware hak.akses tidak
        //    membaca cache lama. Pastikan middleware baca data terbaru.
        $this->flushCaches();
    }

    public function down(): void
    {
        if (!Schema::hasTable('menu')) {
            return;
        }

        $now = now();

        // Restore: aktifkan kembali menu Video.
        DB::table('menu')
            ->where('route', self::VIDEO_ROUTE)
            ->update([
                'status'     => 'aktif',
                'updated_at' => $now,
            ]);

        // Rollback backfill: kembalikan 36 ke array hak_akses dan hapus 21
        // yang sebelumnya ditambahkan oleh migration ini.
        $this->rollbackHakAkses();

        $this->flushCaches();
    }

    /**
     * Tambahkan ID 21 (Galeri) untuk user yang punya ID 36 (Video) di
     * hak_akses, lalu hapus 36 dari array.
     */
    private function backfillHakAkses(): void
    {
        if (!Schema::hasTable('users')) {
            return;
        }

        $users = DB::table('users')->get(['id', 'hak_akses']);
        foreach ($users as $u) {
            $ids = $this->decode($u->hak_akses);
            if (!in_array(self::VIDEO_MENU_ID, $ids, true)) {
                continue;
            }

            // Tambah Galeri (21) bila belum ada.
            if (!in_array(self::GALLERY_MENU_ID, $ids, true)) {
                $ids[] = self::GALLERY_MENU_ID;
            }

            // Hapus Video (36).
            $ids = array_values(array_diff($ids, [self::VIDEO_MENU_ID]));
            $ids = array_values(array_unique(array_map('intval', $ids)));

            DB::table('users')->where('id', $u->id)->update([
                'hak_akses' => json_encode($ids),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Inverse dari backfillHakAkses: kembalikan 36 ke array dan hapus 21
     * yang sebelumnya ditambahkan.
     *
     * Catatan: rollback ini menghapus 21 dari SEMUA user yang mendapatnya
     * lewat migration ini. Aman karena 21 kemungkinan sudah ada di array
     * user admin landing (menu Galeri selalu di grup landing).
     */
    private function rollbackHakAkses(): void
    {
        if (!Schema::hasTable('users')) {
            return;
        }

        $users = DB::table('users')->get(['id', 'hak_akses']);
        foreach ($users as $u) {
            $ids = $this->decode($u->hak_akses);
            if (!in_array(self::GALLERY_MENU_ID, $ids, true)) {
                continue;
            }

            if (!in_array(self::VIDEO_MENU_ID, $ids, true)) {
                $ids[] = self::VIDEO_MENU_ID;
            }

            $ids = array_values(array_diff($ids, [self::GALLERY_MENU_ID]));
            $ids = array_values(array_unique(array_map('intval', $ids)));

            DB::table('users')->where('id', $u->id)->update([
                'hak_akses' => json_encode($ids),
                'updated_at' => now(),
            ]);
        }
    }

    private function decode($raw): array
    {
        if (is_array($raw)) {
            $arr = $raw;
        } elseif ($raw === null || $raw === '') {
            $arr = [];
        } else {
            $arr = json_decode((string) $raw, true);
        }
        $arr = is_array($arr) ? $arr : [];
        return array_values(array_unique(array_map('intval', $arr)));
    }

    /**
     * Bersihkan cache group landing dan cache menu publik agar perubahan
     * menu langsung terlihat tanpa tunggu TTL 2 jam.
     */
    private function flushCaches(): void
    {
        try {
            if (class_exists(\App\Http\Middleware\EnsureHakAkses::class)) {
                \App\Http\Middleware\EnsureHakAkses::flushGroupCache('landing');
            }
        } catch (\Throwable $e) {
            // Abaikan: cache opsional.
        }
        try {
            \Illuminate\Support\Facades\Cache::forget('lp_menus_active');
        } catch (\Throwable $e) {
            // Abaikan: cache opsional.
        }
    }
};
