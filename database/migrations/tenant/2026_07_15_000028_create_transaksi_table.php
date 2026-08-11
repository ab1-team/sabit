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
            Schema::create('transaksi', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->date('tanggal_transaksi');
                $table->string('rekening_debit');
                $table->string('rekening_kredit');
                $table->string('kode_spp', 255);
                $table->integer('siswa_id');
                $table->string('idtp')->nullable();
                $table->string('keterangan');
                $table->string('jumlah');
                $table->string('urutan');
                $table->string('user_id');
                $table->timestamps();
                $table->softDeletes();

                $table->foreign('kode_spp', 'transaksi_kode_spp_foreign')
                    ->references('kode')->on('spp');
            });

            DB::statement('ALTER TABLE `transaksi` AUTO_INCREMENT = 59775');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi');
    }
};
