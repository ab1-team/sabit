<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('jenis_pembayaran')) {
            Schema::create('jenis_pembayaran', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('nama');
                $table->string('kode_akun');
                $table->string('jumlah');
                $table->timestamps();
            });

            DB::statement('ALTER TABLE `jenis_pembayaran` AUTO_INCREMENT = 6');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('jenis_pembayaran');
    }
};
