<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KurikulumSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            [
                'kode_kurikulum' => 'K-MERDEKA',
                'nama_kurikulum' => 'Kurikulum Merdeka',
                'status'         => 'aktif',
            ],
            [
                'kode_kurikulum' => 'K-2013',
                'nama_kurikulum' => 'Kurikulum 2013',
                'status'         => 'nonaktif',
            ],
        ];

        foreach ($rows as $r) {
            DB::table('kurikulum')->updateOrInsert(
                ['kode_kurikulum' => $r['kode_kurikulum']],
                $r + ['updated_at' => now(), 'created_at' => now()]
            );
        }
    }
}
