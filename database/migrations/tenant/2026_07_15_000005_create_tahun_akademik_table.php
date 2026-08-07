<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('tahun_akademik')) {
            Schema::create('tahun_akademik', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('nama_tahun');
                $table->string('keterangan');
                $table->string('status');
                $table->timestamps();
            });

            DB::statement('ALTER TABLE `tahun_akademik` AUTO_INCREMENT = 11');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('tahun_akademik');
    }
};
