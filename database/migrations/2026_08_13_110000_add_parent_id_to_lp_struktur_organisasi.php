<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lp_struktur_organisasi', function (Blueprint $table) {
            $table->unsignedBigInteger('parent_id')->nullable()->after('id');
            $table->index('parent_id');
            $table->foreign('parent_id')
                ->references('id')->on('lp_struktur_organisasi')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('lp_struktur_organisasi', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropIndex(['parent_id']);
            $table->dropColumn('parent_id');
        });
    }
};
