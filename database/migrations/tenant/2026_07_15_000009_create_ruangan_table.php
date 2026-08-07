<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('ruangan')) {
            Schema::create('ruangan', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('kode_gedung');
                $table->string('kode_ruangan');
                $table->string('nama_ruangan');
                $table->string('kapasitas_belajar');
                $table->string('kapasitas_ujian');
                $table->string('keterangan');
                $table->enum('status', ['aktif', 'non_aktif']);
                $table->timestamps();
            });

            DB::statement('ALTER TABLE `ruangan` AUTO_INCREMENT = 28');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('ruangan');
    }
};
