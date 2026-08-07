<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('spp')) {
            Schema::create('spp', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('kode')->unique();
                $table->date('tanggal');
                $table->string('anggota_kelas');
                $table->string('nominal');
                $table->enum('status', ['L', 'B'])->default('B');
                $table->date('tgl_lunas')->nullable();
                $table->timestamps();
            });

            DB::statement('ALTER TABLE `spp` AUTO_INCREMENT = 69577');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('spp');
    }
};
