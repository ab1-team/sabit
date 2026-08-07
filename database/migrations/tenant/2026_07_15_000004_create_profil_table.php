<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('profil')) {
            Schema::create('profil', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('nama', 150)->nullable();
                $table->text('alamat')->nullable();
                $table->string('telpon', 30)->nullable();
                $table->string('penanggung_jawab', 150)->nullable();
                $table->string('email', 150)->nullable();
                $table->string('logo')->nullable();
                $table->unsignedTinyInteger('jatuh_tempo')->default(10);
                $table->timestamps();
            });

            DB::statement('ALTER TABLE `profil` AUTO_INCREMENT = 2');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('profil');
    }
};
