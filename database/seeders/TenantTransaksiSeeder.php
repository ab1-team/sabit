<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TenantTransaksiSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('admin_transaksi')->insertOrIgnore([
            [
                'idt' => 1,
                'tgl_transaksi' => '2026-07-21',
                'rekening_debit' => '111.2010',
                'rekening_kredit' => '111.2010',
                'idv' => 1,
                'keterangan_transaksi' => '-',
                'jumlah' => '1000000',
                'urutan' => 1,
                'id_user' => 1,
            ],
        ]);
    }
}