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
                if (!Schema::hasColumn('lp_settings', 'theme_button_color')) {
                    $table->string('theme_button_color', 20)->nullable()->after('hero_background');
                }
                if (!Schema::hasColumn('lp_settings', 'theme_text_color')) {
                    $table->string('theme_text_color', 20)->nullable()->after('theme_button_color');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('lp_settings')) {
            Schema::table('lp_settings', function (Blueprint $table) {
                if (Schema::hasColumn('lp_settings', 'theme_button_color')) {
                    $table->dropColumn('theme_button_color');
                }
                if (Schema::hasColumn('lp_settings', 'theme_text_color')) {
                    $table->dropColumn('theme_text_color');
                }
            });
        }
    }
};
