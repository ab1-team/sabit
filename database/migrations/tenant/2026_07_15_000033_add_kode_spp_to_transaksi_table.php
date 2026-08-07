<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('transaksi') && !Schema::hasColumn('transaksi', 'kode_spp')) {
            Schema::table('transaksi', function (Blueprint $table) {
                $table->string('kode_spp', 255)->nullable()->after('spp_id');
            });

            DB::statement("
                UPDATE transaksi t
                JOIN spp s ON s.id = t.spp_id
                SET t.kode_spp = s.kode
            ");
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('transaksi') && Schema::hasColumn('transaksi', 'kode_spp')) {
            Schema::table('transaksi', function (Blueprint $table) {
                $table->dropColumn('kode_spp');
            });
        }
    }
};