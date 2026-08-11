<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('admin_user', function (Blueprint $table) {
            if (!Schema::hasColumn('admin_user', 'tenant_id')) {
                $table->string('tenant_id', 64)->nullable()->after('id')->index();
            }
        });

        Schema::table('admin_invoice', function (Blueprint $table) {
            if (!Schema::hasColumn('admin_invoice', 'tenant_id')) {
                $table->string('tenant_id', 64)->nullable()->after('id')->index();
            }
        });

        if (Schema::hasTable('admin_rekening') && !Schema::hasColumn('admin_rekening', 'tenant_id')) {
            DB::statement("ALTER TABLE `admin_rekening` ADD COLUMN `tenant_id` VARCHAR(64) NULL AFTER `id`");
            DB::statement("ALTER TABLE `admin_rekening` ADD INDEX `admin_rekening_tenant_id_index` (`tenant_id`)");
        }

        if (Schema::hasTable('admin_transaksi') && !Schema::hasColumn('admin_transaksi', 'tenant_id')) {
            DB::statement("ALTER TABLE `admin_transaksi` ADD COLUMN `tenant_id` VARCHAR(64) NULL AFTER `idt`");
            DB::statement("ALTER TABLE `admin_transaksi` ADD INDEX `admin_transaksi_tenant_id_index` (`tenant_id`)");
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('admin_transaksi') && Schema::hasColumn('admin_transaksi', 'tenant_id')) {
            DB::statement("ALTER TABLE `admin_transaksi` DROP COLUMN `tenant_id`");
        }

        if (Schema::hasTable('admin_rekening') && Schema::hasColumn('admin_rekening', 'tenant_id')) {
            DB::statement("ALTER TABLE `admin_rekening` DROP COLUMN `tenant_id`");
        }

        Schema::table('admin_invoice', function (Blueprint $table) {
            if (Schema::hasColumn('admin_invoice', 'tenant_id')) {
                $table->dropColumn('tenant_id');
            }
        });

        Schema::table('admin_user', function (Blueprint $table) {
            if (Schema::hasColumn('admin_user', 'tenant_id')) {
                $table->dropColumn('tenant_id');
            }
        });
    }
};