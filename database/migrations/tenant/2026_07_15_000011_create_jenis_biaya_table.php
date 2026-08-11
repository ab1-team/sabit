<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('jenis_biaya')) {
            Schema::create('jenis_biaya', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('id_jp');
                $table->string('angkatan');
                $table->string('total_beban');
                $table->timestamps();

                $table->index('id_jp', 'jenis_biaya_id_jp_foreign');
                $table->foreign('id_jp', 'jenis_biaya_id_jp_foreign')
                    ->references('id')->on('jenis_pembayaran')
                    ->onDelete('cascade');
            });

            DB::statement('ALTER TABLE `jenis_biaya` AUTO_INCREMENT = 6');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('jenis_biaya');
    }
};
