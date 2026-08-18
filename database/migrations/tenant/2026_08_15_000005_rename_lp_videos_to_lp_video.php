<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Selaraskan nama tabel video landing page.
 *
 * Latar belakang:
 *  - Migrasi awal `2026_08_11_000002_buat_tabel_landing_page` membuat
 *    tabel dengan nama `lp_videos` (plural, pakai 's').
 *  - Model `App\Models\Landing\VideoLanding` memakai `protected $table = 'lp_video'`
 *    (singular, tanpa 's') — konsisten dengan konvensi penamaan tabel
 *    landing page lain (`lp_artikel`, `lp_galeri`, `lp_pengumuman`, dll).
 *  - Semua controller, view, dan seeder yang terkait (`AdminLandingController`,
 *    `HalamanPublikController`, `LandingPageSeeder`, `LandingPageDummySeeder`)
 *    berpatokan pada `lp_video`. Karena itu query selalu gagal
 *    (tabel `lp_video` tidak ada) sehingga halaman /video kosong dan
 *    upload video lokal tidak pernah berfungsi.
 *
 * Solusi: rename `lp_videos` -> `lp_video`. Aman untuk tenant baru
 * (belum ada tabel `lp_videos` jika migrasi `2026_08_11_000002` versi
 * terbaru dipakai) maupun tenant existing (rename + tambah kolom
 * lokal-support jika migrasi 000003 belum sempat menambahkan karena
 * tabel target salah nama).
 *
 * Idempotent:
 *  - Rename hanya jalan jika `lp_videos` ada dan `lp_video` belum ada.
 *  - Tambah kolom source/file_path/poster/youtube_url nullable dengan
 *    cek Schema::hasColumn, sehingga aman re-run.
 *  - Backfill `source = 'youtube'` untuk baris lama agar accessor model
 *    (`isLocal`, `isYoutube`, `display_thumb`) konsisten.
 */
return new class extends Migration
{
    public function up(): void
    {
        // 1) Rename tabel dari plural ke singular, jika perlu.
        if (Schema::hasTable('lp_videos') && ! Schema::hasTable('lp_video')) {
            Schema::rename('lp_videos', 'lp_video');
        }

        // 2) Pastikan kolom lokal-upload ada. Migrasi
        //    2026_08_15_000003_add_local_video_support_to_lp_video
        //    menambahkan source/file_path/poster ke tabel `lp_video`
        //    — jika tabelnya dulu bernama `lp_videos` (plural),
        //    migrasi 000003 skip sehingga kolom tidak pernah dibuat.
        if (! Schema::hasTable('lp_video')) {
            return;
        }

        if (! Schema::hasColumn('lp_video', 'source')) {
            Schema::table('lp_video', function (Blueprint $table) {
                $table->string('source', 20)->default('youtube')->after('youtube_url');
            });
        }
        if (! Schema::hasColumn('lp_video', 'file_path')) {
            Schema::table('lp_video', function (Blueprint $table) {
                $table->string('file_path', 500)->nullable()->after('source');
            });
        }
        if (! Schema::hasColumn('lp_video', 'poster')) {
            Schema::table('lp_video', function (Blueprint $table) {
                $table->string('poster', 255)->nullable()->after('file_path');
            });
        }

        // 3) youtube_url nullable untuk mendukung source=local.
        if (Schema::hasColumn('lp_video', 'youtube_url')) {
            \Illuminate\Support\Facades\DB::statement("UPDATE lp_video SET source = 'youtube' WHERE source IS NULL OR source = ''");
        }
    }

    public function down(): void
    {
        // Down hanya menghapus kolom lokal-support dan rename kembali.
        // Tidak menghapus data.

        foreach (['poster', 'file_path', 'source'] as $col) {
            if (Schema::hasTable('lp_video') && Schema::hasColumn('lp_video', $col)) {
                Schema::table('lp_video', function (Blueprint $table) use ($col) {
                    $table->dropColumn($col);
                });
            }
        }

        if (Schema::hasTable('lp_video') && ! Schema::hasTable('lp_videos')) {
            Schema::rename('lp_video', 'lp_videos');
        }
    }
};
