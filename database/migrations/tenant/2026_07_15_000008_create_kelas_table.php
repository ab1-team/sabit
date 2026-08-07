<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('kelas')) {
            Schema::create('kelas', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('kode_kelas');
                $table->string('nama_kelas');
                $table->string('tingkat');
                $table->string('kode_kurikulum');
                $table->timestamps();
            });

            DB::statement('ALTER TABLE `kelas` AUTO_INCREMENT = 26');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('kelas');
    }
};
