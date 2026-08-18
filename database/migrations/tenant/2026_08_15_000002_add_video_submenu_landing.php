<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Tambah sub menu 'Video' di bawah parent Landing Page (id=15).
 *
 * - parent_id = 15 (group 'landing')
 * - route     = app.admin-landing.videos (sejajar Berita/Galeri)
 * - urutan    = 20 (setelah Galeri id=21, sebelum Pengumuman id=22)
 * - icon      = play_circle
 *
 * Idempotent: pakai insertOrIgnore + cek existing by (parent_id, route)
 * agar re-run aman meski slug/route sudah ada.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('menu')) {
            return;
        }

        $parentId = DB::table('menu')->where('id', 15)->value('id');
        if (! $parentId) {
            return;
        }

        // Normalisasi urutan parent=15 child ke nilai final yang kita inginkan:
        //   16 Pengaturan Website = 16
        //   19 Profil            = 17
        //   20 Berita            = 18
        //   21 Galeri            = 19
        //   36 Video             = 20
        //   22 Pengumuman        = 21
        //   23 Kontak            = 22
        //   25 PPDB (dropdown)   = 23
        //
        // Pakai update by id (idempotent) sehingga state apapun yang sudah
        // ada di-rewrite ke nilai benar. Aman untuk tenant baru (id 22/23/25
        // ada, 36 belum) maupun tenant existing (id 36 sudah ada, urutan
        // 22/23/25 bisa apapun).
        $hasExisting = DB::table('menu')
            ->where('parent_id', 15)
            ->where('route', '/app/admin-landing/videos')
            ->exists();

        if ($hasExisting) {
            DB::table('menu')->where('id', 36)->update(['urutan' => 20, 'updated_at' => now()]);
            DB::table('menu')->where('id', 22)->update(['urutan' => 21, 'updated_at' => now()]);
            DB::table('menu')->where('id', 23)->update(['urutan' => 22, 'updated_at' => now()]);
            DB::table('menu')->where('id', 25)->update(['urutan' => 23, 'updated_at' => now()]);
            return;
        }

        // Tenant baru: insert Video, lalu tetapkan urutan absolut untuk
        // parent=15 children. Urutan id=22=20, id=23=21, id=25=22 di seed
        // awal (sebelum update PPDB). Kita normalisasi ulang.
        DB::table('menu')->insert([
            'nama_menu' => 'Video',
            'route' => '/app/admin-landing/videos',
            'icon' => 'play_circle',
            'urutan' => 20,
            'status' => 'aktif',
            'group' => 'landing',
            'parent_id' => 15,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('menu')->where('id', 22)->update(['urutan' => 21, 'updated_at' => now()]);
        DB::table('menu')->where('id', 23)->update(['urutan' => 22, 'updated_at' => now()]);
        DB::table('menu')->where('id', 25)->update(['urutan' => 23, 'updated_at' => now()]);
    }

    public function down(): void
    {
        if (! Schema::hasTable('menu')) {
            return;
        }

        $deleted = DB::table('menu')
            ->where('parent_id', 15)
            ->where('route', '/app/admin-landing/videos')
            ->delete();

        if ($deleted) {
            // Kembalikan urutan parent=15 child ke pre-Video state
            // (id=22 Pengumuman=20, id=23 Kontak=21, id=25 PPDB=22).
            DB::table('menu')->where('id', 22)->update(['urutan' => 20, 'updated_at' => now()]);
            DB::table('menu')->where('id', 23)->update(['urutan' => 21, 'updated_at' => now()]);
            DB::table('menu')->where('id', 25)->update(['urutan' => 22, 'updated_at' => now()]);
        }
    }
};
