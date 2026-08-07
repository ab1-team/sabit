<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('jabatan')) {
            Schema::create('jabatan', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('nama_jabatan', 100);
                $table->string('kode_jabatan', 50)->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('jabatan');
    }
};