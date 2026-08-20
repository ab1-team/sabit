<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!\Schema::hasTable('spp')) {
            return;
        }

        $exists = collect(DB::select("SELECT id FROM spp WHERE kode = '0'"))->isNotEmpty();
        if ($exists) {
            return;
        }

        DB::statement("INSERT INTO spp (kode, tanggal, anggota_kelas, nominal, status, tgl_lunas, created_at, updated_at) VALUES ('0', '1970-01-01', '0', '0', 'B', '1970-01-01', NULL, NULL)");
    }

    public function down(): void
    {
        if (!\Schema::hasTable('spp')) {
            return;
        }

        DB::statement("DELETE FROM spp WHERE kode = '0'");
    }
};