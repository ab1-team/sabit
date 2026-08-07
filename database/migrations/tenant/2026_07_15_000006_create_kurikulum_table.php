<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('kurikulum')) {
            Schema::create('kurikulum', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('kode_kurikulum', 50)->nullable()->unique();
                $table->string('nama_kurikulum');
                $table->string('status')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('kurikulum');
    }
};
