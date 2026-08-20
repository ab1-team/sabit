<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->fixRuangan();
        $this->fixTahunAkademik();
        $this->fixJurusan();
    }

    public function down(): void
    {
    }

    private function fixRuangan(): void
    {
        if (!Schema::hasTable('ruangan') || !Schema::hasColumn('ruangan', 'status')) {
            return;
        }

        DB::statement("UPDATE ruangan SET status = 'aktif' WHERE status IS NULL OR status = '' OR status NOT IN ('aktif','non_aktif')");

        DB::statement("ALTER TABLE ruangan MODIFY status ENUM('aktif','non_aktif') NOT NULL DEFAULT 'aktif'");
    }

    private function fixTahunAkademik(): void
    {
        if (!Schema::hasTable('tahun_akademik') || !Schema::hasColumn('tahun_akademik', 'status')) {
            return;
        }

        DB::statement("UPDATE tahun_akademik SET status = 'aktif' WHERE status IS NULL OR status = ''");

        Schema::table('tahun_akademik', function (Blueprint $table) {
            $table->string('status', 20)->nullable(false)->default('aktif')->change();
        });
    }

    private function fixJurusan(): void
    {
        if (!Schema::hasTable('jurusan') || !Schema::hasColumn('jurusan', 'status')) {
            return;
        }

        DB::statement("UPDATE jurusan SET status = 'aktif' WHERE status IS NULL OR status = ''");

        Schema::table('jurusan', function (Blueprint $table) {
            $table->string('status', 20)->nullable(false)->default('aktif')->change();
        });
    }
};
