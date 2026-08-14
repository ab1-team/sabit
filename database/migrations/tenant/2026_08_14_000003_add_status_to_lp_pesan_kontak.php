<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('lp_pesan_kontak')) {
            return;
        }
        if (!Schema::hasColumn('lp_pesan_kontak', 'status')) {
            Schema::table('lp_pesan_kontak', function (Blueprint $table) {
                // Workflow status pesan masuk:
                //   baru      = pesan baru masuk, belum dibuka admin
                //   dibaca    = sudah dibuka admin, belum/belum tentu dibalas
                //   selesai   = sudah ditangani / selesai
                $table->string('status', 20)->default('baru')->after('ip_address');
                $table->index(['status', 'created_at'], 'lp_pesan_kontak_status_created_at_index');
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('lp_pesan_kontak')) {
            return;
        }
        if (Schema::hasColumn('lp_pesan_kontak', 'status')) {
            Schema::table('lp_pesan_kontak', function (Blueprint $table) {
                $table->dropIndex('lp_pesan_kontak_status_created_at_index');
                $table->dropColumn('status');
            });
        }
    }
};
