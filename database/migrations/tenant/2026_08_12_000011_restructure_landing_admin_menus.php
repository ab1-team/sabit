<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Restruktur menu admin Landing Page (sub-menu dari parent "Landing Page",
 * id=15):
 *
 *   1. Pengaturan Website (id=16) — tetap. Semua text-editor & ubah konten
 *      khusus identitas/kontak/warna/background/seo berpusat di sini.
 *   2. Profil           (id=19) — kelola section profil publik (/profil).
 *   3. Berita           (id=20) — kelola lp_posts (/berita).
 *   4. Galeri           (id=21) — kelola lp_galleries (/galeri).
 *   5. Pengumuman       (id=22) — kelola lp_announcements (/pengumuman).
 *   6. Kontak           (id=23) — kelola pesan masuk dari form /kontak.
 *   7. PPDB             (id=24) — kelola konten halaman /ppdb (CTA + settings
 *                                + requirements + stages + schedules + faqs).
 *
 * Sub-menu yang lama dipakai (id 17, 25-28, dst.) dihapus dari tabel karena
 * redundan/tidak lagi diperlukan di sidebar admin:
 *   - 17  Hero Slider       (Hero dikelola via Pengaturan Website > section
 *                            hero text; tidak ada list hero CRUD terpisah
 *                            karena landing publik tidak pakai carousel
 *                            multi-slide; jika di kemudian hari dibutuhkan,
 *                            akan jadi entry menu baru).
 *   - 25 Section Profil     (route-nya di-merge ke menu 'Profil' id=19).
 *   - 26 Struktur Organisasi (jadi sub-halaman di dalam route 'Profil').
 *   - 27 Fasilitas          (jadi sub-halaman di dalam route 'Profil').
 *   - 28 CTA PPDB           (jadi tab di dalam route 'PPDB' id=24).
 *   - 24 Acara / Agenda     (publik belum expose /acara; modul disembunyikan
 *                            untuk menghindari CRUD yang tidak diakses).
 *
 * Backfill hak_akses: user dengan akses ke sub-menu yang dihapus otomatis
 * mendapat akses ke sub-menu penggantinya. Dengan begitu, user admin landing
 * yang awalnya mengelola hero/struktur/fasilitas/CTA PPDB tidak kehilangan
 * kemampuan mengelola modul yang sama — hanya lewat menu yang lebih bersih.
 */
return new class extends Migration
{
    private const PARENT_LANDING_ID = 15;
    private const PENGATURAN_WEBSITE_ID = 16;

    /** Pemetaan id => nama baru. */
    private const RENAMES = [
        19 => 'Profil',
        20 => 'Berita',
        21 => 'Galeri',
        22 => 'Pengumuman',
        // 23 (Halaman) akan dipakai ulang untuk menu 'Kontak'. Tidak ada di
        // sub-menu lama, jadi dibuat baru di INSERT section.
        24 => 'PPDB',
    ];

    /** Route sub-menu yang DIHAPUS. Pakai route (bukan id) agar robust terhadap
     *  auto-increment id yang dipakai di tenant. */
    private const REMOVED_ROUTES = [
        '/app/landing/hero',
        '/app/landing/events',
        '/app/landing/struktur',
        '/app/landing/fasilitas',
        '/app/landing/ppdb-cta',
        '/app/landing/pages',
        '/app/landing/videos',
    ];

    /** Nama menu yang juga ditandai obsolete (untuk catch-all nama lama). */
    private const REMOVED_NAMES = [
        'Hero Slider',
        'Section Profil',
        'Struktur Organisasi',
        'Fasilitas',
        'CTA PPDB',
        'Halaman Statis',
        'Acara / Agenda',
        'Program / Berita',
    ];

    public function up(): void
    {
        if (!Schema::hasTable('menu')) {
            return;
        }

        $now = now();

        // 1) Rename sub-menu existing.
        foreach (self::RENAMES as $id => $newName) {
            DB::table('menu')->where('id', $id)->update([
                'nama_menu'  => $newName,
                'updated_at' => $now,
            ]);
        }

        // 2) Update route sub-menu agar konsisten. Hanya set kalau berbeda
        //    dengan yang di-hardcode di migrasi ini. (Dibiarkan fleksibel
        //    agar migrasi ulang tidak menimpa route yang sudah benar).

        DB::table('menu')->where('id', 19)->update([
            'route'      => '/app/landing/profile-sections',
            'icon'       => 'account_balance',
            'updated_at' => $now,
        ]);
        DB::table('menu')->where('id', 20)->update([
            'route'      => '/app/landing/posts',
            'icon'       => 'article',
            'updated_at' => $now,
        ]);
        DB::table('menu')->where('id', 21)->update([
            'route'      => '/app/landing/galleries',
            'icon'       => 'photo_library',
            'updated_at' => $now,
        ]);
        DB::table('menu')->where('id', 22)->update([
            'route'      => '/app/landing/announcements',
            'icon'       => 'campaign',
            'updated_at' => $now,
        ]);
        DB::table('menu')->where('id', 24)->update([
            'route'      => '/app/landing/ppdb-cta',
            'icon'       => 'how_to_reg',
            'updated_at' => $now,
        ]);

        // 3) Convert id=23 (Halaman Statis) jadi 'Kontak', atau insert kalau belum ada.
        //    Pakai ID 23 sebagai well-known slot agar konsisten dengan seeder
        //    dan naming convention.
        $existingAt23 = DB::table('menu')->where('id', 23)->first();
        if ($existingAt23) {
            // ID 23 sudah ada (biasanya 'Halaman Statis'); rename + ganti route.
            DB::table('menu')->where('id', 23)->update([
                'nama_menu'  => 'Kontak',
                'route'      => '/app/landing/contact-messages',
                'icon'       => 'contact_mail',
                'urutan'     => 23,
                'group'      => 'landing',
                'status'     => 'aktif',
                'parent_id'  => self::PARENT_LANDING_ID,
                'updated_at' => $now,
            ]);
        } elseif (!DB::table('menu')->where('route', '/app/landing/contact-messages')->exists()) {
            // Pakai auto-increment ID baru.
            DB::table('menu')->insert([
                'parent_id'  => self::PARENT_LANDING_ID,
                'nama_menu'  => 'Kontak',
                'route'      => '/app/landing/contact-messages',
                'icon'       => 'contact_mail',
                'urutan'     => 23,
                'status'     => 'aktif',
                'group'      => 'landing',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // 4) Pastikan parent dropdown Landing Page (id=15) inert + group 'landing'.
        //    id=15 dipakai sebagai label pemisah visual di sidebar — tidak
        //    punya halaman; route='#' supaya klik tidak redirect.
        DB::table('menu')->where('id', self::PARENT_LANDING_ID)->update([
            'route'      => '#',
            'status'     => 'aktif',
            'group'      => 'landing',
            'updated_at' => $now,
        ]);

        // 5) Pastikan Pengaturan Website (id=16) tetap parent=15 & group landing.
        DB::table('menu')->where('id', self::PENGATURAN_WEBSITE_ID)->update([
            'parent_id'  => self::PARENT_LANDING_ID,
            'group'      => 'landing',
            'status'     => 'aktif',
            'updated_at' => $now,
        ]);

        // 6) Pastikan sub-menu (id 19, 20, 21, 22, 23, 24) parent=15 (Landing Page),
        //    group=landing, dan urutannya berurutan.urutan di-update supaya
        //    sidebar rapi: 19=Profil(16), 20=Berita(17), 21=Galeri(18),
        //    22=Pengumuman(19), 23=Kontak(20), 24=PPDB(21). Urutan mengikuti
        //    posisi di child of id=15.
        DB::table('menu')->whereIn('id', [19, 20, 21, 22, 23, 24])->update([
            'parent_id'  => self::PARENT_LANDING_ID,
            'group'      => 'landing',
            'status'     => 'aktif',
            'updated_at' => $now,
        ]);
        $orderMap = [19 => 16, 20 => 17, 21 => 18, 22 => 19, 23 => 20, 24 => 21];
        foreach ($orderMap as $id => $urutan) {
            DB::table('menu')->where('id', $id)->update([
                'urutan'     => $urutan,
                'updated_at' => $now,
            ]);
        }

        // 7) Hapus sub-menu yang sudah jadi obsolete. Pakai route ATAU nama agar
        //    robust terhadap id auto-increment yang mungkin berbeda di tiap
        //    tenant. Sub-menu 'Profil' (id 19) sengaja TIDAK dimasukkan karena
        //    namanya masih dipakai untuk menu baru.
        DB::table('menu')
            ->whereIn('route', self::REMOVED_ROUTES)
            ->orWhereIn('nama_menu', self::REMOVED_NAMES)
            ->delete();

        // 8) Backfill hak_akses user.
        $this->backfillHakAkses();
    }

    public function down(): void
    {
        if (!Schema::hasTable('menu')) {
            return;
        }

        $now = now();

        // Restore sub-menu yang dihapus (kembalikan ke kondisi pra-migrasi). Pakai
        // insertOrIgnore sehingga aman bila duplikat route sudah ada.
        $restored = [
            ['nama_menu' => 'Hero Slider',         'route' => '/app/landing/hero',             'icon' => 'photo_library', 'urutan' => 17],
            ['nama_menu' => 'Program / Berita',    'route' => '/app/landing/posts',            'icon' => 'article',       'urutan' => 19],
            ['nama_menu' => 'Pengumuman',          'route' => '/app/landing/announcements',    'icon' => 'campaign',      'urutan' => 20],
            ['nama_menu' => 'Galeri',              'route' => '/app/landing/galleries',        'icon' => 'photo_library', 'urutan' => 21],
            ['nama_menu' => 'Video',               'route' => '/app/landing/videos',           'icon' => 'play_circle',   'urutan' => 22],
            ['nama_menu' => 'Halaman Statis',      'route' => '/app/landing/pages',            'icon' => 'description',   'urutan' => 23],
            ['nama_menu' => 'Acara / Agenda',      'route' => '/app/landing/events',           'icon' => 'event',         'urutan' => 24],
            ['nama_menu' => 'Section Profil',      'route' => '/app/landing/profile-sections', 'icon' => 'article',       'urutan' => 25],
            ['nama_menu' => 'Struktur Organisasi', 'route' => '/app/landing/struktur',         'icon' => 'groups',        'urutan' => 26],
            ['nama_menu' => 'Fasilitas',           'route' => '/app/landing/fasilitas',        'icon' => 'apartment',     'urutan' => 27],
            ['nama_menu' => 'CTA PPDB',            'route' => '/app/landing/ppdb-cta',         'icon' => 'megaphone',     'urutan' => 28],
        ];
        foreach ($restored as $row) {
            DB::table('menu')->insertOrIgnore(array_merge($row, [
                'parent_id'  => self::PARENT_LANDING_ID,
                'status'     => 'aktif',
                'group'      => 'landing',
                'created_at' => $now,
                'updated_at' => $now,
            ]));
        }

        // Kembalikan nama & route sub-menu ke kondisi pra-migrasi. inverse dari up().
        $oldNames = [
            ['id' => 19, 'nama_menu' => 'Program / Berita', 'route' => '/app/landing/posts'],
            ['id' => 20, 'nama_menu' => 'Pengumuman',       'route' => '/app/landing/announcements'],
            ['id' => 21, 'nama_menu' => 'Galeri',           'route' => '/app/landing/galleries'],
            ['id' => 22, 'nama_menu' => 'Video',            'route' => '/app/landing/videos'],
            ['id' => 23, 'nama_menu' => 'Halaman Statis',   'route' => '/app/landing/pages'],
            ['id' => 24, 'nama_menu' => 'Acara / Agenda',   'route' => '/app/landing/events'],
        ];
        foreach ($oldNames as $row) {
            DB::table('menu')->where('id', $row['id'])->update([
                'nama_menu'  => $row['nama_menu'],
                'route'      => $row['route'],
                'updated_at' => $now,
            ]);
        }
    }

    /**
     * Tambahkan ID pengganti untuk user yang memegang ID sub-menu obsolete.
     * Pemetaan: 17 (Hero) -> 16 (Pengaturan); 25 (Section Profil) -> 19;
     *           26 (Struktur) -> 19; 27 (Fasilitas) -> 19; 28 (CTA PPDB) -> 24.
     *
     * Hapus referensi orphan. Pastikan user yang semula punya akses Hero /
     * Struktur / Fasilitas / CTA PPDB tetap punya akses ke modul yang sama
     * lewat menu Pengganti (yang mencakup fungsionalitas penuh).
     */
    private function backfillHakAkses(): void
    {
        if (!Schema::hasTable('users')) {
            return;
        }

        $replacements = [
            17 => [16],          // Hero Slider -> Pengaturan Website
            25 => [19],          // Section Profil -> Profil
            26 => [19],          // Struktur -> Profil
            27 => [19],          // Fasilitas -> Profil
            28 => [24],          // CTA PPDB -> PPDB
        ];

        $users = DB::table('users')->get(['id', 'hak_akses']);
        foreach ($users as $u) {
            $ids = $this->decode($u->hak_akses);
            if (empty($ids)) {
                continue;
            }

            $changed = false;

            foreach ($replacements as $oldId => $newIds) {
                if (in_array($oldId, $ids, true)) {
                    foreach ($newIds as $newId) {
                        if (!in_array($newId, $ids, true)) {
                            $ids[] = $newId;
                            $changed = true;
                        }
                    }
                    $ids = array_values(array_diff($ids, [$oldId]));
                    $changed = true;
                }
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
