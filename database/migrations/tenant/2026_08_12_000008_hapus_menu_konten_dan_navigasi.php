<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Hapus entry menu admin landing page yang sudah tidak ditampilkan lagi:
 * - id 18 "Pengaturan Konten" (route /app/landing/pengaturan-lanjutan)
 * - id 24 "Menu Navigasi"     (route /app/landing/menus)
 *
 * Menu ini dihapus dari tile Menu Pengelolaan di halaman index landing
 * admin (resources/views/landing-admin/index.blade.php) sehingga tidak
 * dipasang lagi untuk tenant baru lewat MenuStructureSeeder.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('menu')
            ->whereIn('id', [18, 24])
            ->delete();
    }

    public function down(): void
    {
    }
};
