<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuStructureSeeder extends Seeder
{
    public function run(): void
    {
        // Top-level parent Pengaturan & Transaksi. parent_id NULL aman.
        DB::table('menu')->insertOrIgnore([
            ['id' => 12, 'nama_menu' => 'Pengaturan', 'route' => '#', 'icon' => 'settings', 'urutan' => 1, 'status' => 'aktif', 'group' => 'Pengaturan', 'parent_id' => null, 'created_at' => null, 'updated_at' => null],
            ['id' => 13, 'nama_menu' => 'Transaksi', 'route' => '#', 'icon' => 'paid', 'urutan' => 9, 'status' => 'aktif', 'group' => 'Transaksi', 'parent_id' => null, 'created_at' => null, 'updated_at' => null],
        ]);

        // Insert PARENT dulu sebelum CHILD untuk menghindari FK constraint
        // violation pada menu.parent_id. Parent menu "Landing Page" (id=15)
        // dan "PPDB" (id=25) harus ada sebelum child dengan parent_id=15/25
        // di-insert.
        //
        // Menu Landing Page. group = 'landing' dipakai oleh middleware hak.akses:landing
        // untuk memetakan hak akses user ke route /app/admin-landing/*.
        //
        // Mulai migrasi 2026_08_12_000011_restructure_landing_admin_menus:
        // Sub-menu disusun ulang menjadi 7 item sesuai halaman publik:
        //   16  Pengaturan Website   - identitas, kontak, warna, background, SEO
        //   19  Profil              - section profil, struktur, fasilitas
        //   20  Berita              - lp_artikel
        //   21  Galeri              - lp_galeri
        //   22  Pengumuman          - lp_pengumuman
        //   23  Kontak              - pesan masuk dari form landing
        //
        // PPDB dipecah jadi dropdown sendiri (parent=25 + child 26-30):
        //   25  PPDB                - parent dropdown (route='#')
        //   26  Pengaturan PPDB     - route /app/admin-landing/ppdb-cta
        //   27  Persyaratan         - route /app/admin-landing/ppdb/persyaratan
        //   28  Alur                - route /app/admin-landing/ppdb/tahapan
        //   29  Jadwal              - route /app/admin-landing/ppdb/jadwal
        //   30  FAQ                 - route /app/admin-landing/ppdb/faq
        //
        // Catatan: id=24 sudah dihapus oleh migration
        // 2026_08_12_000008_remove_landing_konten_and_nav_menus.php dan tidak
        // boleh dipakai ulang.
        //
        // id=15 (Landing Page) menjadi parent dropdown inert (route='#')
        // hanya sebagai label visual di sidebar.
        DB::table('menu')->insertOrIgnore([
            ['id' => 15, 'nama_menu' => 'Landing Page', 'route' => '#', 'icon' => 'language', 'urutan' => 15, 'status' => 'aktif', 'group' => 'landing', 'parent_id' => null, 'created_at' => null, 'updated_at' => null],
            ['id' => 25, 'nama_menu' => 'PPDB', 'route' => '#', 'icon' => 'how_to_reg', 'urutan' => 22, 'status' => 'aktif', 'group' => 'landing', 'parent_id' => 15, 'created_at' => null, 'updated_at' => null],
        ]);

        // Child dengan parent_id=15 (Landing Page) atau parent_id=25 (PPDB).
        // Insert terpisah agar child rows tervalidasi setelah parent ada,
        // menghindari FK constraint violation.
        // PPDB child menggunakan id 31-35 untuk menghindari collision dengan
        // id=26 (Beranda Layanan dari MenuSeeder) dan id 27-30 (konten
        // landing lain hasil migrasi). Id konsisten dipakai di MenuStructureSeeder.
        DB::table('menu')->insertOrIgnore([
            ['id' => 16, 'nama_menu' => 'Pengaturan Website', 'route' => '/app/admin-landing/pengaturan', 'icon' => 'tune', 'urutan' => 16, 'status' => 'aktif', 'group' => 'landing', 'parent_id' => 15, 'created_at' => null, 'updated_at' => null],
            ['id' => 19, 'nama_menu' => 'Profil', 'route' => '/app/admin-landing/bagian-profil', 'icon' => 'account_balance', 'urutan' => 17, 'status' => 'aktif', 'group' => 'landing', 'parent_id' => 15, 'created_at' => null, 'updated_at' => null],
            ['id' => 20, 'nama_menu' => 'Berita', 'route' => '/app/admin-landing/artikel', 'icon' => 'article', 'urutan' => 18, 'status' => 'aktif', 'group' => 'landing', 'parent_id' => 15, 'created_at' => null, 'updated_at' => null],
            ['id' => 21, 'nama_menu' => 'Galeri', 'route' => '/app/admin-landing/galeri', 'icon' => 'photo_library', 'urutan' => 19, 'status' => 'aktif', 'group' => 'landing', 'parent_id' => 15, 'created_at' => null, 'updated_at' => null],
            ['id' => 22, 'nama_menu' => 'Pengumuman', 'route' => '/app/admin-landing/pengumuman', 'icon' => 'campaign', 'urutan' => 20, 'status' => 'aktif', 'group' => 'landing', 'parent_id' => 15, 'created_at' => null, 'updated_at' => null],
            ['id' => 23, 'nama_menu' => 'Kontak', 'route' => '/app/admin-landing/pesan-kontak', 'icon' => 'contact_mail', 'urutan' => 21, 'status' => 'aktif', 'group' => 'landing', 'parent_id' => 15, 'created_at' => null, 'updated_at' => null],
            ['id' => 31, 'nama_menu' => 'Pengaturan PPDB', 'route' => '/app/admin-landing/ppdb-cta', 'icon' => 'tune', 'urutan' => 1, 'status' => 'aktif', 'group' => 'landing', 'parent_id' => 25, 'created_at' => null, 'updated_at' => null],
            ['id' => 32, 'nama_menu' => 'Persyaratan', 'route' => '/app/admin-landing/ppdb/persyaratan', 'icon' => 'fact_check', 'urutan' => 2, 'status' => 'aktif', 'group' => 'landing', 'parent_id' => 25, 'created_at' => null, 'updated_at' => null],
            ['id' => 33, 'nama_menu' => 'Alur', 'route' => '/app/admin-landing/ppdb/tahapan', 'icon' => 'timeline', 'urutan' => 3, 'status' => 'aktif', 'group' => 'landing', 'parent_id' => 25, 'created_at' => null, 'updated_at' => null],
            ['id' => 34, 'nama_menu' => 'Jadwal', 'route' => '/app/admin-landing/ppdb/jadwal', 'icon' => 'event', 'urutan' => 4, 'status' => 'aktif', 'group' => 'landing', 'parent_id' => 25, 'created_at' => null, 'updated_at' => null],
            ['id' => 35, 'nama_menu' => 'FAQ', 'route' => '/app/admin-landing/ppdb/faq', 'icon' => 'quiz', 'urutan' => 5, 'status' => 'aktif', 'group' => 'landing', 'parent_id' => 25, 'created_at' => null, 'updated_at' => null],
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