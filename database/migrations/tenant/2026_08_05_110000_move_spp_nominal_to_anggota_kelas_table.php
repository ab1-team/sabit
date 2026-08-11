<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('anggota_kelas', 'spp_nominal')) {
            Schema::table('anggota_kelas', function (Blueprint $table) {
                $table->string('spp_nominal')->nullable()->after('kode_kelas');
            });
        }

        if (Schema::hasColumn('siswa', 'spp_nominal')) {
            Schema::table('siswa', function (Blueprint $table) {
                $table->dropColumn('spp_nominal');
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasColumn('siswa', 'spp_nominal')) {
            Schema::table('siswa', function (Blueprint $table) {
                $table->string('spp_nominal')->nullable()->after('ruang');
            });
        }

        if (Schema::hasColumn('anggota_kelas', 'spp_nominal')) {
            Schema::table('anggota_kelas', function (Blueprint $table) {
                $table->dropColumn('spp_nominal');
            });
        }
    }
};
