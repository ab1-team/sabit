<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('lp_settings') && !Schema::hasColumn('lp_settings', 'hero_background')) {
            Schema::table('lp_settings', function (Blueprint $table) {
                $table->string('hero_background', 255)->nullable()->after('meta_keywords');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('lp_settings') && Schema::hasColumn('lp_settings', 'hero_background')) {
            Schema::table('lp_settings', function (Blueprint $table) {
                $table->dropColumn('hero_background');
            });
        }
    }
};
