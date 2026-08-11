<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('menu') && !Schema::hasColumn('menu', 'parent_id')) {
            Schema::table('menu', function (Blueprint $table) {
                $table->unsignedBigInteger('parent_id')->nullable()->after('id');
                $table->foreign('parent_id')->references('id')->on('menu')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('menu') && Schema::hasColumn('menu', 'parent_id')) {
            Schema::table('menu', function (Blueprint $table) {
                $table->dropForeign(['parent_id']);
                $table->dropColumn('parent_id');
            });
        }
    }
};