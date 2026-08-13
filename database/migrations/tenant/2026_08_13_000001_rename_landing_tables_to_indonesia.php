<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Rename tabel & kolom landing page ke bahasa Indonesia.
 *
 * Sebelumnya tabel & kolom pakai nama Inggris (lp_settings, lp_pages, dll).
 * Setelah model Indonesia (PengaturanLanding, HalamanLanding, dll) di-update,
 * tabel DB juga disesuaikan agar konsistensi. Mapping lengkap di bawah.
 *
 * Kolom di-rename hanya yang memang berbeda, untuk menjaga data existing
 * tetap terbaca. Kolom JSON cast di model (stats, welcome, dll) namanya
 * sudah bahasa Indonesia (sejak migrasi 2026_08_12_000001), jadi tidak
 * perlu di-rename lagi di sini.
 *
 * Tabel mapping:
 *   lp_settings             -> lp_pengaturan
 *   lp_menus                -> lp_menu
 *   lp_pages                -> lp_halaman
 *   lp_posts                -> lp_artikel
 *   lp_galleries            -> lp_galeri
 *   lp_videos               -> lp_video
 *   lp_events               -> lp_acara
 *   lp_announcements        -> lp_pengumuman
 *   lp_contact_messages     -> lp_pesan_kontak
 *   lp_hero_slides          -> lp_slide_hero
 *   lp_profile_sections     -> lp_bagian_profil
 *   lp_struktur_organisasi  -> lp_struktur_organisasi (sudah ID)
 *   lp_fasilitas            -> lp_fasilitas (sudah ID)
 *   lp_ppdb_settings        -> lp_ppdb_pengaturan
 *   lp_ppdb_requirements    -> lp_ppdb_persyaratan
 *   lp_ppdb_stages          -> lp_ppdb_tahapan
 *   lp_ppdb_schedules       -> lp_ppdb_jadwal
 *   lp_ppdb_faqs            -> lp_ppdb_faq
 *
 * Kolom mapping (hanya yang berbeda):
 *   lp_ppdb_settings.hero_image     -> lp_ppdb_pengaturan.gambar_hero
 *
 * Catatan: migrasi ini idempotent. Jika tabel/kolom sudah dalam nama
 * Indonesia, skip rename (jangan error).
 */
return new class extends Migration
{
    public function up(): void
    {
        $tableMap = [
            'lp_settings' => 'lp_pengaturan',
            'lp_menus' => 'lp_menu',
            'lp_pages' => 'lp_halaman',
            'lp_posts' => 'lp_artikel',
            'lp_galleries' => 'lp_galeri',
            'lp_videos' => 'lp_video',
            'lp_events' => 'lp_acara',
            'lp_announcements' => 'lp_pengumuman',
            'lp_contact_messages' => 'lp_pesan_kontak',
            'lp_hero_slides' => 'lp_slide_hero',
            'lp_profile_sections' => 'lp_bagian_profil',
            'lp_ppdb_settings' => 'lp_ppdb_pengaturan',
            'lp_ppdb_requirements' => 'lp_ppdb_persyaratan',
            'lp_ppdb_stages' => 'lp_ppdb_tahapan',
            'lp_ppdb_schedules' => 'lp_ppdb_jadwal',
            'lp_ppdb_faqs' => 'lp_ppdb_faq',
        ];

        $driver = DB::connection()->getDriverName();

        foreach ($tableMap as $from => $to) {
            if (Schema::hasTable($from) && !Schema::hasTable($to)) {
                if ($driver === 'mysql' || $driver === 'mariadb') {
                    DB::statement("RENAME TABLE `{$from}` TO `{$to}`");
                } else {
                    Schema::rename($from, $to);
                }
            }
        }

        // Rename kolom spesifik.
        if (Schema::hasTable('lp_ppdb_pengaturan') && Schema::hasColumn('lp_ppdb_pengaturan', 'hero_image')) {
            if ($driver === 'mysql' || $driver === 'mariadb') {
                DB::statement('ALTER TABLE `lp_ppdb_pengaturan` CHANGE `hero_image` `gambar_hero` VARCHAR(255) NULL');
            } else {
                Schema::table('lp_ppdb_pengaturan', function ($table) {
                    $table->renameColumn('hero_image', 'gambar_hero');
                });
            }
        }
    }

    public function down(): void
    {
        $tableMap = [
            'lp_pengaturan' => 'lp_settings',
            'lp_menu' => 'lp_menus',
            'lp_halaman' => 'lp_pages',
            'lp_artikel' => 'lp_posts',
            'lp_galeri' => 'lp_galleries',
            'lp_video' => 'lp_videos',
            'lp_acara' => 'lp_events',
            'lp_pengumuman' => 'lp_announcements',
            'lp_pesan_kontak' => 'lp_contact_messages',
            'lp_slide_hero' => 'lp_hero_slides',
            'lp_bagian_profil' => 'lp_profile_sections',
            'lp_ppdb_pengaturan' => 'lp_ppdb_settings',
            'lp_ppdb_persyaratan' => 'lp_ppdb_requirements',
            'lp_ppdb_tahapan' => 'lp_ppdb_stages',
            'lp_ppdb_jadwal' => 'lp_ppdb_schedules',
            'lp_ppdb_faq' => 'lp_ppdb_faqs',
        ];

        $driver = DB::connection()->getDriverName();

        foreach ($tableMap as $from => $to) {
            if (Schema::hasTable($from) && !Schema::hasTable($to)) {
                if ($driver === 'mysql' || $driver === 'mariadb') {
                    DB::statement("RENAME TABLE `{$from}` TO `{$to}`");
                } else {
                    Schema::rename($from, $to);
                }
            }
        }

        if (Schema::hasTable('lp_ppdb_settings') && Schema::hasColumn('lp_ppdb_settings', 'gambar_hero')) {
            if ($driver === 'mysql' || $driver === 'mariadb') {
                DB::statement('ALTER TABLE `lp_ppdb_settings` CHANGE `gambar_hero` `hero_image` VARCHAR(255) NULL');
            } else {
                Schema::table('lp_ppdb_settings', function ($table) {
                    $table->renameColumn('gambar_hero', 'hero_image');
                });
            }
        }
    }
};
