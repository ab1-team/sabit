<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('domains') && !Schema::hasColumn('domains', 'type')) {
            Schema::table('domains', function (Blueprint $table) {
                $table->string('type', 20)->default('admin')->after('domain')->index();
            });

            DB::table('domains')->whereNull('type')->update(['type' => 'admin']);
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('domains') && Schema::hasColumn('domains', 'type')) {
            Schema::table('domains', function (Blueprint $table) {
                $table->dropIndex(['type']);
                $table->dropColumn('type');
            });
        }
    }
};
