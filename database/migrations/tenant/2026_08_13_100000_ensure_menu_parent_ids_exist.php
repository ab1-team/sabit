<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Fix FK constraint pada menu.parent_id untuk tenant baru.
 *
 * Kronologi bug:
 *   - MenuStructureSeeder (dipanggil dari TenantDatabaseSeeder) insert parent
 *     menu id=15 (Landing Page) dengan parent_id NULL.
 *   - Seeder jalan SETELAH semua migrasi tenant selesai.
 *   - Tapi migrasi 000002, 000010, dan 000011 di folder database/migrations/tenant
 *     insert CHILD rows dengan parent_id=15 SEBELUM seeder jalan → FK violation.
 *
 * Perbaikan:
 *   - Tiga migrasi di atas sudah di-update untuk insert parent id=15 secara
 *     idempotent di awal up(). Lihat commit log.
 *
 * Migrasi ini menambahkan safety net bagi tenant yang tabel 'menu'-nya sudah
 * created dengan parent_id=15 missing (mis. database sabit_demo yang gagal
 * setengah jadi). Ia:
 *   1. Insert parent id=15 jika belum ada.
 *   2. Pastikan parent dropdown Landing Page ada sebelum anak-anaknya.
 *
 * Tidak menghapus data existing. Idempotent (aman di-run ulang).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('menu')) {
            return;
        }

        $now = now();

        // 1) Pastikan parent Landing Page (id=15) ada.
        DB::table('menu')->insertOrIgnore([
            'id'         => 15,
            'parent_id'  => null,
            'nama_menu'  => 'Landing Page',
            'route'      => '#',
            'icon'       => 'language',
            'urutan'     => 15,
            'status'     => 'aktif',
            'group'      => 'landing',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // 2) Pastikan parent PPDB (id=25) juga ada, karena MenuStructureSeeder
        //    membuat child dengan parent_id=25 (id 26-30). Migrasi tenant
        //    tidak insert id=25, jadi amankan juga.
        DB::table('menu')->insertOrIgnore([
            'id'         => 25,
            'parent_id'  => null,
            'nama_menu'  => 'PPDB',
            'route'      => '#',
            'icon'       => 'how_to_reg',
            'urutan'     => 22,
            'status'     => 'aktif',
            'group'      => 'landing',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    public function down(): void
    {
        if (!Schema::hasTable('menu')) {
            return;
        }

        // Jangan hapus parent saat rollback — biarkan data existing
        // (MenuStructureSeeder akan insert ulang dengan id yang sama).
    }
};
