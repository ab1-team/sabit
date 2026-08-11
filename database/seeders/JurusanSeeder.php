<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JurusanSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['IPA',   'Ilmu Pengetahuan Alam',     'aktif'],
            ['IPS',   'Ilmu Pengetahuan Sosial',    'aktif'],
            ['BAHASA','Bahasa',                    'aktif'],
        ];

        $data = [];
        foreach ($rows as $i => $r) {
            $data[] = [
                'id'           => $i + 1,
                'kode_jurusan' => $r[0],
                'nama'         => $r[1],
                'status'       => $r[2],
                'created_at'   => '2026-07-15 18:12:27',
                'updated_at'   => '2026-07-15 18:12:27',
            ];
        }

        DB::table('jurusan')->insertOrIgnore($data);
    }
}
