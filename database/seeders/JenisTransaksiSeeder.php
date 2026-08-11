<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JenisTransaksiSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['Aset Masuk'],
            ['Aset Keluar'],
            ['Pemindahan Aset & Saldo'],
        ];

        $data = [];
        foreach ($rows as $r) {
            $data[] = [
                'nama'       => $r[0],
                'created_at' => '2026-07-22 14:30:35',
                'updated_at' => '2026-07-22 14:30:35',
            ];
        }

        DB::table('jenis_transaksi')->insert($data);
    }
}
