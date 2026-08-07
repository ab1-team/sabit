<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('menu')) {
            Schema::create('menu', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('nama_menu', 100);
                $table->string('route', 255)->nullable();
                $table->string('icon', 100)->nullable();
                $table->unsignedInteger('urutan')->default(0);
                $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
                $table->string('group', 50)->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('menu');
    }
};