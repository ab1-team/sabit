<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('jenis_transaksi')) {
            Schema::create('jenis_transaksi', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('nama');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('jenis_transaksi');
    }
};
