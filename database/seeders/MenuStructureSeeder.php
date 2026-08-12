<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuStructureSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('menu')->insertOrIgnore([
            ['id' => 12, 'nama_menu' => 'Pengaturan', 'route' => '#', 'icon' => 'settings', 'urutan' => 1, 'status' => 'aktif', 'group' => 'Pengaturan', 'parent_id' => null, 'created_at' => null, 'updated_at' => null],
            ['id' => 13, 'nama_menu' => 'Transaksi', 'route' => '#', 'icon' => 'paid', 'urutan' => 9, 'status' => 'aktif', 'group' => 'Transaksi', 'parent_id' => null, 'created_at' => null, 'updated_at' => null],
        ]);

        // Menu Landing Page. group = 'landing' dipakai oleh middleware hak.akses:landing
        // untuk memetakan hak akses user ke route /app/landing/*.
        //
        // Mulai migrasi 2026_08_12_000011_restructure_landing_admin_menus:
        // Sub-menu disusun ulang menjadi 7 item sesuai halaman publik:
        //   16  Pengaturan Website   - identitas, kontak, warna, background, SEO
        //   19  Profil              - section profil, struktur, fasilitas
        //   20  Berita              - lp_posts
        //   21  Galeri              - lp_galleries
        //   22  Pengumuman          - lp_announcements
        //   23  Kontak              - pesan masuk dari form landing
        //   24  PPDB                - halaman /ppdb (CTA + settings + FAQ)
        //
        // id=15 (Landing Page) menjadi parent dropdown inert (route='#')
        // hanya sebagai label visual di sidebar.
        DB::table('menu')->insertOrIgnore([
            ['id' => 15, 'nama_menu' => 'Landing Page', 'route' => '#', 'icon' => 'language', 'urutan' => 15, 'status' => 'aktif', 'group' => 'landing', 'parent_id' => null, 'created_at' => null, 'updated_at' => null],
            ['id' => 16, 'nama_menu' => 'Pengaturan Website', 'route' => '/app/landing/pengaturan', 'icon' => 'tune', 'urutan' => 16, 'status' => 'aktif', 'group' => 'landing', 'parent_id' => 15, 'created_at' => null, 'updated_at' => null],
            ['id' => 19, 'nama_menu' => 'Profil', 'route' => '/app/landing/profile-sections', 'icon' => 'account_balance', 'urutan' => 17, 'status' => 'aktif', 'group' => 'landing', 'parent_id' => 15, 'created_at' => null, 'updated_at' => null],
            ['id' => 20, 'nama_menu' => 'Berita', 'route' => '/app/landing/posts', 'icon' => 'article', 'urutan' => 18, 'status' => 'aktif', 'group' => 'landing', 'parent_id' => 15, 'created_at' => null, 'updated_at' => null],
            ['id' => 21, 'nama_menu' => 'Galeri', 'route' => '/app/landing/galleries', 'icon' => 'photo_library', 'urutan' => 19, 'status' => 'aktif', 'group' => 'landing', 'parent_id' => 15, 'created_at' => null, 'updated_at' => null],
            ['id' => 22, 'nama_menu' => 'Pengumuman', 'route' => '/app/landing/announcements', 'icon' => 'campaign', 'urutan' => 20, 'status' => 'aktif', 'group' => 'landing', 'parent_id' => 15, 'created_at' => null, 'updated_at' => null],
            ['id' => 23, 'nama_menu' => 'Kontak', 'route' => '/app/landing/contact-messages', 'icon' => 'contact_mail', 'urutan' => 21, 'status' => 'aktif', 'group' => 'landing', 'parent_id' => 15, 'created_at' => null, 'updated_at' => null],
            ['id' => 24, 'nama_menu' => 'PPDB', 'route' => '/app/landing/ppdb-cta', 'icon' => 'how_to_reg', 'urutan' => 22, 'status' => 'aktif', 'group' => 'landing', 'parent_id' => 15, 'created_at' => null, 'updated_at' => null],
        ]);

        DB::table('menu')->where('id', 2)->update(['parent_id' => 12]);
        DB::table('menu')->where('id', 3)->update(['parent_id' => 12]);
        DB::table('menu')->where('id', 4)->update(['parent_id' => 12]);
        DB::table('menu')->where('id', 5)->update(['parent_id' => 12]);
        DB::table('menu')->where('id', 6)->update(['parent_id' => 12]);

        DB::table('menu')->where('id', 9)->update(['parent_id' => 13]);
        DB::table('menu')->where('id', 10)->update(['parent_id' => 13]);

        DB::table('menu')->where('id', 2)->update(['group' => null]);
        DB::table('menu')->where('id', 3)->update(['group' => null]);
        DB::table('menu')->where('id', 4)->update(['group' => null]);
        DB::table('menu')->where('id', 5)->update(['group' => null]);
        DB::table('menu')->where('id', 6)->update(['group' => null]);
        DB::table('menu')->where('id', 9)->update(['group' => null]);
        DB::table('menu')->where('id', 10)->update(['group' => null]);
    }
}