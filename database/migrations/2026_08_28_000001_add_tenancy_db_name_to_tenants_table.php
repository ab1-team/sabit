<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            if (!Schema::hasColumn('tenants', 'tenancy_db_name')) {
                $table->string('tenancy_db_name')->nullable()->after('email')->unique();
            }
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            if (Schema::hasColumn('tenants', 'tenancy_db_name')) {
                $table->dropUnique(['tenancy_db_name']);
                $table->dropColumn('tenancy_db_name');
            }
        });
    }
};
