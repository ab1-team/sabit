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

        if (Schema::hasColumn('transaksi', 'spp_id')) {
            Schema::table('transaksi', function (Blueprint $table) {
                $table->dropColumn('spp_id');
            });
        }

        if (Schema::hasColumn('transaksi', 'kode_spp')) {
            DB::statement("UPDATE transaksi SET kode_spp = '' WHERE kode_spp IS NULL");

            $zeroDate = collect(DB::select("SHOW COLUMNS FROM transaksi WHERE Field = 'tanggal_transaksi'"))->first();
            if ($zeroDate && str_contains($zeroDate->Type, 'date')) {
                $sqlMode = collect(DB::select("SELECT @@sql_mode AS mode"))->first()->mode ?? '';
                $noZeroDate = str_contains($sqlMode, 'NO_ZERO_DATE') || str_contains($sqlMode, 'STRICT_TRANS_TABLES') || str_contains($sqlMode, 'STRICT_ALL_TABLES');

                if (!$noZeroDate) {
                    $cnt = collect(DB::select("SELECT COUNT(*) AS c FROM transaksi WHERE tanggal_transaksi = '0000-00-00'"))->first()->c;
                    if ($cnt > 0) {
                        DB::statement("ALTER TABLE transaksi MODIFY tanggal_transaksi VARCHAR(10) NOT NULL DEFAULT ''");
                        DB::statement("UPDATE transaksi SET tanggal_transaksi = '1970-01-01' WHERE tanggal_transaksi = '0000-00-00'");
                        DB::statement("ALTER TABLE transaksi MODIFY tanggal_transaksi DATE NOT NULL");
                    }
                }

                if (Schema::hasColumn('transaksi', 'tgl_lunas')) {
                    $cntTgl = collect(DB::select("SELECT COUNT(*) AS c FROM transaksi WHERE tgl_lunas = '0000-00-00'"))->first()->c;
                    if ($cntTgl > 0) {
                        DB::statement("ALTER TABLE transaksi MODIFY tgl_lunas VARCHAR(10) NULL");
                        DB::statement("UPDATE transaksi SET tgl_lunas = '1970-01-01' WHERE tgl_lunas = '0000-00-00'");
                        DB::statement("ALTER TABLE transaksi MODIFY tgl_lunas DATE NULL");
                    }
                }
            }

            DB::statement("ALTER TABLE transaksi MODIFY kode_spp VARCHAR(255) NOT NULL");

            $sppIdx = collect(DB::select("SHOW INDEX FROM spp WHERE Column_name = 'kode'"));
            if ($sppIdx->isEmpty()) {
                DB::statement("CREATE INDEX spp_kode_index ON spp (kode)");
            }

            $fkExists = collect(DB::select("
                SELECT CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS
                WHERE TABLE_SCHEMA = DATABASE()
                AND TABLE_NAME = 'transaksi'
                AND CONSTRAINT_TYPE = 'FOREIGN KEY'
                AND CONSTRAINT_NAME = 'transaksi_kode_spp_foreign'
            "))->isNotEmpty();

            if (!$fkExists) {
                $orphanZero = (int) collect(DB::select("SELECT COUNT(*) AS c FROM transaksi WHERE kode_spp = '0'"))->first()->c;
                $sppZeroExists = collect(DB::select("SELECT id FROM spp WHERE kode = '0'"))->isNotEmpty();

                if ($orphanZero > 0 && !$sppZeroExists) {
                    DB::statement("INSERT INTO spp (kode, tanggal, anggota_kelas, nominal, status, tgl_lunas, created_at, updated_at) VALUES ('0', '1970-01-01', '0', '0', 'B', '1970-01-01', NULL, NULL)");
                    $sppZeroExists = true;
                }

                if ($orphanZero === 0) {
                    Schema::table('transaksi', function (Blueprint $table) {
                        $table->foreign('kode_spp', 'transaksi_kode_spp_foreign')
                            ->references('kode')->on('spp');
                    });
                }
            }
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('transaksi')) {
            return;
        }

        if (Schema::hasColumn('transaksi', 'kode_spp')) {
            Schema::table('transaksi', function (Blueprint $table) {
                $table->dropForeign('transaksi_kode_spp_foreign');
            });

            Schema::table('transaksi', function (Blueprint $table) {
                $table->dropColumn('kode_spp');
            });

            Schema::table('transaksi', function (Blueprint $table) {
                $table->integer('spp_id')->nullable()->after('siswa_id');
            });
        }
    }
};