<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('saldo')) {
            Schema::create('saldo', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('kode_akun', 10);
                $table->unsignedTinyInteger('bulan');
                $table->unsignedSmallInteger('tahun');
                $table->decimal('debit', 15, 2)->default(0.00);
                $table->decimal('kredit', 15, 2)->default(0.00);
                $table->timestamps();

                $table->unique(['kode_akun', 'bulan', 'tahun'], 'saldo_kode_akun_bulan_tahun_unique');
                $table->index(['tahun', 'bulan'], 'saldo_tahun_bulan_index');
                $table->foreign('kode_akun', 'saldo_kode_akun_foreign')
                    ->references('kode_akun')->on('rekening')
                    ->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('saldo');
    }
};
