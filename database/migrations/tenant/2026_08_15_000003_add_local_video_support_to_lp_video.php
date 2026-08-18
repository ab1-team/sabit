<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tambah dukungan sumber video alternatif (selain YouTube):
 *  - source    : 'youtube' (default) atau 'local' (file upload)
 *  - file_path : path relatif terhadap Storage::disk('public') (tenant root)
 *                untuk video lokal (mp4/webm/mov).
 *  - poster    : opsional — thumbnail kustom untuk video lokal.
 *
 * Aman untuk tenant existing karena kolom baru dibuat nullable dan
 * `source` di-backfill ke 'youtube' agar data lama tetap valid.
 */
return new class extends Migration
{
    public function up(): void
    {
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

        if (Schema::hasColumn('lp_video', 'youtube_url')) {
            \Illuminate\Support\Facades\DB::statement("UPDATE lp_video SET source = 'youtube' WHERE source IS NULL OR source = ''");
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('lp_video')) {
            return;
        }

        foreach (['poster', 'file_path', 'source'] as $col) {
            if (Schema::hasColumn('lp_video', $col)) {
                Schema::table('lp_video', function (Blueprint $table) use ($col) {
                    $table->dropColumn($col);
                });
            }
        }
    }
};
