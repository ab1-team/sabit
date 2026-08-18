<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Seed 1 video default di lp_video agar halaman /video tidak kosong
 * untuk tenant baru. Video yang di-seed adalah clip pendek bebas
 * (Big Buck Bunny - Blender Foundation, lisensi CC-BY 3.0).
 *
 * Idempotent: hanya insert jika tabel lp_video kosong.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('lp_video')) {
            return;
        }

        if (DB::table('lp_video')->exists()) {
            return;
        }

        DB::table('lp_video')->insert([
            'title' => 'Selamat Datang di Website Kami',
            'description' => 'Video perkenalan singkat sekolah. Klik untuk memutar.',
            'youtube_url' => 'https://www.youtube.com/watch?v=aqz-KE-bpKQ',
            'thumbnail' => null,
            'is_published' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        // Tidak menghapus data pada rollback — admin mungkin sudah menambah
        // video lain. Uncomment baris di bawah bila ingin reset penuh.
        // DB::table('lp_video')->truncate();
    }
};
