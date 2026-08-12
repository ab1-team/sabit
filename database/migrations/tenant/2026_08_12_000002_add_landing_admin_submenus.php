<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Tambah entry menu admin untuk section CRUD landing page baru
 * (Posts, Pengumuman, Galeri, Video, Halaman).
 *
 * Parent menu "Landing Page" (id=15) sudah ada dari MenuStructureSeeder.
 * Sub-menu yang sudah ada: id=16 (Pengaturan Website) dan id=17 (Hero Slider).
 *
 * Sub-menu baru di sini dipasang parent_id=15 dan group='landing' agar
 * middleware hak.akses:landing otomatis mengizinkan akses untuk user
 * yang sebelumnya sudah mendapat hak akses group 'landing' lewat
 * TenantDatabaseSeeder.
 */
return new class extends Migration
{
    public function up(): void
    {
        $rows = [
            ['id' => 19, 'nama_menu' => 'Program / Berita',   'route' => '/app/landing/posts',                'icon' => 'article',       'urutan' => 19, 'status' => 'aktif', 'group' => 'landing', 'parent_id' => 15],
            ['id' => 20, 'nama_menu' => 'Pengumuman',         'route' => '/app/landing/announcements',        'icon' => 'campaign',      'urutan' => 20, 'status' => 'aktif', 'group' => 'landing', 'parent_id' => 15],
            ['id' => 21, 'nama_menu' => 'Galeri',             'route' => '/app/landing/galleries',            'icon' => 'photo_library', 'urutan' => 21, 'status' => 'aktif', 'group' => 'landing', 'parent_id' => 15],
            ['id' => 22, 'nama_menu' => 'Video',              'route' => '/app/landing/videos',               'icon' => 'play_circle',   'urutan' => 22, 'status' => 'aktif', 'group' => 'landing', 'parent_id' => 15],
            ['id' => 23, 'nama_menu' => 'Halaman Statis',     'route' => '/app/landing/pages',                'icon' => 'description',   'urutan' => 23, 'status' => 'aktif', 'group' => 'landing', 'parent_id' => 15],
        ];

        $now = now();

        foreach ($rows as $row) {
            DB::table('menu')->insertOrIgnore(array_merge($row, [
                'created_at' => $now,
                'updated_at' => $now,
            ]));
        }
    }

    public function down(): void
    {
        DB::table('menu')->whereIn('id', [19, 20, 21, 22, 23])->delete();
    }
};
