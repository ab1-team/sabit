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

        if (!Schema::hasColumn('transaksi', 'kelas')) {
            Schema::table('transaksi', function (Blueprint $table) {
                $table->string('kelas', 50)->nullable()->after('siswa_id')
                    ->comment('Snapshot kode_kelas siswa saat transaksi dibuat, agar riwayat pembayaran non-SPP (mis. daftar ulang) tetap tercatat kelasnya.');
                $table->index('kelas', 'transaksi_kelas_index');
            });
        }

        if (Schema::hasColumn('transaksi', 'kelas') && Schema::hasTable('anggota_kelas')) {
            DB::statement("
                UPDATE transaksi t
                LEFT JOIN (
                    SELECT id_siswa, kode_kelas
                    FROM anggota_kelas
                    WHERE status = 'aktif'
                    GROUP BY id_siswa
                ) ak ON ak.id_siswa = t.siswa_id
                SET t.kelas = ak.kode_kelas
                WHERE t.kelas IS NULL OR t.kelas = ''
            ");
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('transaksi')) {
            return;
        }

        if (Schema::hasColumn('transaksi', 'kelas')) {
            Schema::table('transaksi', function (Blueprint $table) {
                $table->dropIndex('transaksi_kelas_index');
                $table->dropColumn('kelas');
            });
        }
    }
};
