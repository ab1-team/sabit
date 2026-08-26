<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('transaksi')) {
            return;
        }

        $fkExists = collect(DB::select("
            SELECT CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS
            WHERE TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME = 'transaksi'
            AND CONSTRAINT_TYPE = 'FOREIGN KEY'
            AND CONSTRAINT_NAME = 'transaksi_kode_spp_foreign'
        "))->isNotEmpty();

        if ($fkExists) {
            Schema::table('transaksi', function (Blueprint $table) {
                $table->dropForeign('transaksi_kode_spp_foreign');
            });
        }

        if (Schema::hasColumn('transaksi', 'kode_spp')) {
            DB::statement("ALTER TABLE transaksi MODIFY kode_spp VARCHAR(255) NULL");
        }

        DB::statement("UPDATE transaksi SET kode_spp = NULL WHERE kode_spp = '' OR kode_spp = '0'");

        if (Schema::hasColumn('transaksi', 'kelas')) {
            DB::statement("ALTER TABLE transaksi MODIFY kelas VARCHAR(50) NULL");
        }

        DB::statement("UPDATE transaksi SET kelas = NULL WHERE kelas = ''");
    }

    public function down(): void
    {
        if (!Schema::hasTable('transaksi') || !Schema::hasColumn('transaksi', 'kode_spp')) {
            return;
        }

        DB::statement("UPDATE transaksi SET kode_spp = '' WHERE kode_spp IS NULL");

        DB::statement("ALTER TABLE transaksi MODIFY kode_spp VARCHAR(255) NOT NULL");

        $sppIdx = collect(DB::select("SHOW INDEX FROM spp WHERE Column_name = 'kode'"));
        if ($sppIdx->isEmpty()) {
            DB::statement("CREATE INDEX spp_kode_index ON spp (kode)");
        }

        $sppZeroExists = collect(DB::select("SELECT id FROM spp WHERE kode = ''"))->isNotEmpty();
        if (!$sppZeroExists) {
            DB::statement("INSERT INTO spp (kode, tanggal, anggota_kelas, nominal, status, tgl_lunas, created_at, updated_at) VALUES ('', '1970-01-01', '0', '0', 'B', '1970-01-01', NULL, NULL)");
        }

        $fkExists = collect(DB::select("
            SELECT CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS
            WHERE TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME = 'transaksi'
            AND CONSTRAINT_TYPE = 'FOREIGN KEY'
            AND CONSTRAINT_NAME = 'transaksi_kode_spp_foreign'
        "))->isNotEmpty();

        if (!$fkExists) {
            Schema::table('transaksi', function (Blueprint $table) {
                $table->foreign('kode_spp', 'transaksi_kode_spp_foreign')
                    ->references('kode')->on('spp');
            });
        }
    }
};
