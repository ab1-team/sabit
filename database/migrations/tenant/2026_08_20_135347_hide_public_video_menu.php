<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Rapikan menu publik di lp_menu: gabungkan Video ke Galeri.
 *
 *   1. Menu "Video" (url=/video) di header & footer publik di-nonaktifkan
 *      agar tidak muncul di navbar publik. Halaman /video route & view tetap
 *      ada (kompatibilitas mundur untuk link external), hanya entri menu
 *      yang disembunyikan.
 *   2. Idempoten: aman dijalankan berulang kali.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('lp_menu')) {
            return;
        }

        $now = now();

        // Nonaktifkan menu Video publik (header & footer). Pakai url
        // sehingga robust terhadap id auto-increment yang berbeda per tenant.
        DB::table('lp_menu')
            ->where('url', '/video')
            ->update([
                'is_active'  => 0,
                'updated_at' => $now,
            ]);

        // Flush cache menu publik.
        try {
            \Illuminate\Support\Facades\Cache::forget('lp_menus_active');
        } catch (\Throwable $e) {
            // Abaikan: cache opsional.
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('lp_menu')) {
            return;
        }

        $now = now();

        DB::table('lp_menu')
            ->where('url', '/video')
            ->update([
                'is_active'  => 1,
                'updated_at' => $now,
            ]);

        try {
            \Illuminate\Support\Facades\Cache::forget('lp_menus_active');
        } catch (\Throwable $e) {
            // Abaikan: cache opsional.
        }
    }
};
