<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JenisBiayaSeeder extends Seeder
{
    public function run(): void
    {
        $row = DB::table('jenis_pembayaran')->where('nama', 'Pembayaran SPP')->first();
        $idJp = $row ? $row->id : 1;

        $rows = [
            ['id_jp' => $idJp, 'angkatan' => date('Y'), 'total_beban' => '3000000'],
        ];

        $data = [];
        foreach ($rows as $i => $r) {
            $data[] = [
                'id'          => $i + 1,
                'id_jp'       => $r['id_jp'],
                'angkatan'    => $r['angkatan'],
                'total_beban' => $r['total_beban'],
                'created_at'  => '2026-07-15 18:12:27',
                'updated_at'  => '2026-07-15 18:12:27',
            ];
        }

        DB::table('jenis_biaya')->insertOrIgnore($data);
    }
}
