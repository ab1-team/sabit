<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JabatanSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('jabatan')->insertOrIgnore([
            ['id' => 1, 'nama_jabatan' => 'Kepala Sekolah', 'kode_jabatan' => 'KEP_SEK', 'created_at' => null, 'updated_at' => null],
            ['id' => 2, 'nama_jabatan' => 'Bendahara', 'kode_jabatan' => 'BEN', 'created_at' => null, 'updated_at' => null],
            ['id' => 3, 'nama_jabatan' => 'Admin', 'kode_jabatan' => 'ADM', 'created_at' => null, 'updated_at' => null],
            ['id' => 4, 'nama_jabatan' => 'Sekretaris', 'kode_jabatan' => 'SEK', 'created_at' => null, 'updated_at' => null],
            ['id' => 5, 'nama_jabatan' => 'Administrator Landing Page', 'kode_jabatan' => 'ADM_LAND', 'created_at' => null, 'updated_at' => null],
        ]);
    }
}