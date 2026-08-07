<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('anggota_kelas')) {
            Schema::create('anggota_kelas', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->integer('id_siswa');
                $table->string('tahun_akademik');
                $table->string('tingkat');
                $table->string('kode_kelas');
                $table->string('tgl_masuk');
                $table->string('tgl_keluar');
                $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
                $table->timestamps();
            });

            DB::statement('ALTER TABLE `anggota_kelas` AUTO_INCREMENT = 5850');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('anggota_kelas');
    }
};
