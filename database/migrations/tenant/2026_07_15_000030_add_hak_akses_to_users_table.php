<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'hak_akses')) {
            Schema::table('users', function (Blueprint $table) {
                $table->json('hak_akses')->nullable()->after('password');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'hak_akses')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('hak_akses');
            });
        }
    }
};