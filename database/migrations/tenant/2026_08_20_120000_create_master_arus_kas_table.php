<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('master_arus_kas')) {
            Schema::create('master_arus_kas', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('nama_akun', 150);
                $table->string('debit', 15)->nullable();
                $table->string('kredit', 15)->nullable();
                $table->integer('parent_id')->default(0);
                $table->timestamps();

                $table->index('parent_id', 'mak_parent_id_index');
                $table->index('debit', 'mak_debit_index');
                $table->index('kredit', 'mak_kredit_index');
            });

            DB::statement('ALTER TABLE `master_arus_kas` AUTO_INCREMENT = 20');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('master_arus_kas');
    }
};