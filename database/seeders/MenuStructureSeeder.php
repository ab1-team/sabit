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
        DB::table('menu')->insertOrIgnore([
            ['id' => 15, 'nama_menu' => 'Landing Page', 'route' => '#', 'icon' => 'language', 'urutan' => 15, 'status' => 'aktif', 'group' => 'landing', 'parent_id' => null, 'created_at' => null, 'updated_at' => null],
            ['id' => 16, 'nama_menu' => 'Pengaturan Website', 'route' => '/app/landing/pengaturan', 'icon' => 'tune', 'urutan' => 16, 'status' => 'aktif', 'group' => 'landing', 'parent_id' => 15, 'created_at' => null, 'updated_at' => null],
            ['id' => 17, 'nama_menu' => 'Hero Slider', 'route' => '/app/landing/hero', 'icon' => 'photo_library', 'urutan' => 17, 'status' => 'aktif', 'group' => 'landing', 'parent_id' => 15, 'created_at' => null, 'updated_at' => null],
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