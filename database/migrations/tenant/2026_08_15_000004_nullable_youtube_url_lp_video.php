<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Buat `youtube_url` di lp_video menjadi nullable.
 *
 * Sebelumnya pada migrasi awal lp_video (2026_08_11_000002) kolom
 * youtube_url dideklarasikan NOT NULL string (500). Hal ini aman selama
 * semua video selalu bersumber dari YouTube.
 *
 * Dengan masuknya dukungan Local Upload (lp_video.source='local'),
 * baris baru bisa sama sekali tidak punya youtube_url. Jadi kita
 * buat nullable; baris YT lama tetap punya nilainya (di-backfill
 * ke bentuk embed URL oleh controller).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('lp_video')) {
            return;
        }

        if (Schema::hasColumn('lp_video', 'youtube_url')) {
            Schema::table('lp_video', function (Blueprint $table) {
                $table->string('youtube_url', 500)->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('lp_video')) {
            return;
        }

        if (Schema::hasColumn('lp_video', 'youtube_url')) {
            Schema::table('lp_video', function (Blueprint $table) {
                $table->string('youtube_url', 500)->nullable(false)->change();
            });
        }
    }
};
