<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('menu')) {
            return;
        }

        // Parent id=15 (Landing Page) mungkin sudah ada dari migrasi
        // sebelumnya atau MenuStructureSeeder. Insert ulang dengan
        // insertOrIgnore agar aman di re-run dan tidak muncul FK violation
        // bila migrasi 000002 belum sempat membuat parent-nya.
        DB::table('menu')->insertOrIgnore([
            'id'         => 15,
            'nama_menu'  => 'Landing Page',
            'route'      => '#',
            'icon'       => 'language',
            'urutan'     => 15,
            'status'     => 'aktif',
            'group'      => 'landing',
            'parent_id'  => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $rows = [
            ['nama_menu' => 'Acara / Agenda',        'route' => '/app/landing/events',         'icon' => 'event',      'urutan' => 24, 'status' => 'aktif', 'group' => 'landing', 'parent_id' => 15],
            ['nama_menu' => 'Section Profil',        'route' => '/app/landing/profile-sections', 'icon' => 'article',  'urutan' => 25, 'status' => 'aktif', 'group' => 'landing', 'parent_id' => 15],
            ['nama_menu' => 'Struktur Organisasi',   'route' => '/app/landing/struktur',      'icon' => 'groups',     'urutan' => 26, 'status' => 'aktif', 'group' => 'landing', 'parent_id' => 15],
            ['nama_menu' => 'Fasilitas',             'route' => '/app/landing/fasilitas',     'icon' => 'apartment',  'urutan' => 27, 'status' => 'aktif', 'group' => 'landing', 'parent_id' => 15],
            ['nama_menu' => 'CTA PPDB',              'route' => '/app/landing/ppdb-cta',      'icon' => 'megaphone',  'urutan' => 28, 'status' => 'aktif', 'group' => 'landing', 'parent_id' => 15],
        ];

        $now = now();

        foreach ($rows as $row) {
            $exists = DB::table('menu')->where('route', $row['route'])->exists();
            if (!$exists) {
                DB::table('menu')->insert(array_merge($row, [
                    'created_at' => $now,
                    'updated_at' => $now,
                ]));
            }
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('menu')) {
            return;
        }

        DB::table('menu')->whereIn('route', [
            '/app/landing/events',
            '/app/landing/profile-sections',
            '/app/landing/struktur',
            '/app/landing/fasilitas',
            '/app/landing/ppdb-cta',
        ])->delete();
    }
};

