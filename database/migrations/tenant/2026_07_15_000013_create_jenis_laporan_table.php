<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('jenis_laporan')) {
            Schema::create('jenis_laporan', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('nama', 100);
                $table->string('file', 100);
                $table->unsignedInteger('urut')->default(0);
                $table->timestamps();
            });

            DB::statement('ALTER TABLE `jenis_laporan` AUTO_INCREMENT = 14');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('jenis_laporan');
    }
};
