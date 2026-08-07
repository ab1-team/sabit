<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('transaksi')) {
            return;
        }

        if (!Schema::hasColumn('transaksi', 'invoice')) {
            DB::statement("ALTER TABLE transaksi ADD COLUMN invoice BIGINT(20) NOT NULL DEFAULT 0 AFTER id");
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('transaksi') && Schema::hasColumn('transaksi', 'invoice')) {
            Schema::table('transaksi', function (Blueprint $table) {
                $table->dropColumn('invoice');
            });
        }
    }
};