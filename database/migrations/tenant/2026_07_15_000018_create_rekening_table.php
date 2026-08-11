<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('rekening')) {
            Schema::create('rekening', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('parent_id')->default(0);
                $table->unsignedTinyInteger('lev1')->default(0);
                $table->unsignedTinyInteger('lev2')->default(0);
                $table->unsignedTinyInteger('lev3')->default(0);
                $table->unsignedTinyInteger('lev4')->default(0);
                $table->string('kode_akun', 10)->unique();
                $table->string('nama_akun', 100);
                $table->string('jenis_mutasi', 6)->default('Debet');
                $table->date('tgl_nonaktif')->nullable();
                $table->decimal('saldo', 15, 2)->default(0.00);
                $table->timestamps();

                $table->index('parent_id', 'rekening_parent_id_index');
            });

            DB::statement('ALTER TABLE `rekening` AUTO_INCREMENT = 105');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('rekening');
    }
};
