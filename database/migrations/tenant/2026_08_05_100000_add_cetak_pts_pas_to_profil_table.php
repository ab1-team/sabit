<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('profil', function (Blueprint $table) {
            $table->unsignedTinyInteger('cetak_pts')->default(3)->after('jatuh_tempo');
            $table->unsignedTinyInteger('cetak_pas')->default(3)->after('cetak_pts');
        });
    }

    public function down(): void
    {
        Schema::table('profil', function (Blueprint $table) {
            $table->dropColumn(['cetak_pts', 'cetak_pas']);
        });
    }
};
