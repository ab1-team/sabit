<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AkunLevel2Seeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            [1, 1, '1.1.00.00', 'Aset Lancar', 'debet'],
            [1, 2, '1.2.00.00', 'Aset Tidak Lancar', 'debet'],
            [1, 3, '1.3.00.00', 'Aset Lain-lain', 'debet'],
            [2, 1, '2.1.00.00', 'Utang Jangka Pendek', 'kredit'],
            [2, 2, '2.2.00.00', 'Utang Jangka Panjang', 'kredit'],
            [3, 1, '3.1.00.00', 'Modal Disetor', 'kredit'],
            [3, 2, '3.2.00.00', 'Laba Rugi', 'kredit'],
            [4, 1, '4.1.00.00', 'Pendapatan Usaha', 'kredit'],
            [4, 2, '4.2.00.00', 'Pendapatan Non Usaha', 'kredit'],
            [4, 3, '4.3.00.00', 'Pendapatan Luar Biasa', 'kredit'],
            [5, 1, '5.1.00.00', 'Beban Usaha', 'debet'],
            [5, 2, '5.2.00.00', 'Beban Pemasaran', 'debet'],
            [5, 3, '5.3.00.00', 'Beban Non Usaha', 'debet'],
            [5, 4, '5.4.00.00', 'Beban Pajak', 'debet'],
        ];

        $data = [];
        foreach ($rows as $i => $r) {
            $data[] = [
                'id' => $i + 1,
                'parent_id' => $r[0],
                'lev1' => $r[0],
                'lev2' => $r[1],
                'lev3' => 0,
                'lev4' => 0,
                'kode_akun' => $r[2],
                'nama_akun' => $r[3],
                'jenis_mutasi' => $r[4],
                'created_at' => '2026-07-14 22:29:57',
                'updated_at' => '2026-07-14 22:29:57',
            ];
        }

        DB::table('akun_level2')->insertOrIgnore($data);
    }
}
