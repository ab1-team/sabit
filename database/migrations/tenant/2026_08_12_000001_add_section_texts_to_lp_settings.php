<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('lp_settings')) {
            Schema::table('lp_settings', function (Blueprint $table) {
                $columns = [
                    'hero_badges',
                    'stats',
                    'welcome',
                    'jenjang',
                    'keunggulan',
                    'ppdb_cta',
                ];

                foreach ($columns as $col) {
                    if (!Schema::hasColumn('lp_settings', $col)) {
                        $table->json($col)->nullable()->after('meta_keywords');
                    }
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('lp_settings')) {
            Schema::table('lp_settings', function (Blueprint $table) {
                $columns = ['hero_badges', 'stats', 'welcome', 'jenjang', 'keunggulan', 'ppdb_cta'];
                foreach ($columns as $col) {
                    if (Schema::hasColumn('lp_settings', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }
    }
};
