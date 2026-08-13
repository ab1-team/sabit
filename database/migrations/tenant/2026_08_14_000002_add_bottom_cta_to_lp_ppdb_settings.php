<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('lp_ppdb_pengaturan')) {
            return;
        }

        Schema::table('lp_ppdb_pengaturan', function (Blueprint $table) {
            // CTA strip bawah halaman PPDB (section "Siap mendaftarkan putra/putri Anda?").
            if (!Schema::hasColumn('lp_ppdb_pengaturan', 'bottom_eyebrow')) {
                $table->string('bottom_eyebrow', 100)->nullable()->after('secondary_url');
            }
            if (!Schema::hasColumn('lp_ppdb_pengaturan', 'bottom_title')) {
                $table->string('bottom_title', 200)->nullable()->after('bottom_eyebrow');
            }
            if (!Schema::hasColumn('lp_ppdb_pengaturan', 'bottom_paragraph')) {
                $table->text('bottom_paragraph')->nullable()->after('bottom_title');
            }
            if (!Schema::hasColumn('lp_ppdb_pengaturan', 'bottom_primary_text')) {
                $table->string('bottom_primary_text', 100)->nullable()->after('bottom_paragraph');
            }
            if (!Schema::hasColumn('lp_ppdb_pengaturan', 'bottom_primary_url')) {
                $table->string('bottom_primary_url', 255)->nullable()->after('bottom_primary_text');
            }
            if (!Schema::hasColumn('lp_ppdb_pengaturan', 'bottom_secondary_text')) {
                $table->string('bottom_secondary_text', 100)->nullable()->after('bottom_primary_url');
            }
            if (!Schema::hasColumn('lp_ppdb_pengaturan', 'bottom_secondary_url')) {
                $table->string('bottom_secondary_url', 255)->nullable()->after('bottom_secondary_text');
            }
            if (!Schema::hasColumn('lp_ppdb_pengaturan', 'bottom_meta')) {
                $table->string('bottom_meta', 150)->nullable()->after('bottom_secondary_url');
            }
        });
    }

    public function down(): void
    {
        Schema::table('lp_ppdb_pengaturan', function (Blueprint $table) {
            foreach ([
                'bottom_meta', 'bottom_secondary_url', 'bottom_secondary_text',
                'bottom_primary_url', 'bottom_primary_text', 'bottom_paragraph',
                'bottom_title', 'bottom_eyebrow',
            ] as $col) {
                if (Schema::hasColumn('lp_ppdb_pengaturan', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
