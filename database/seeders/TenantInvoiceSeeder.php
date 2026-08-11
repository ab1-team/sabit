<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TenantInvoiceSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('admin_invoice')->insertOrIgnore([
            [
                'id' => 1,
                'jenis_pembayaran' => 'tyrty',
                'tgl_invoice' => '2026-07-24',
                'tgl_lunas' => '2026-07-21',
                'status' => 'paid',
                'jumlah' => 1231321.31,
                'user_id' => 1,
                'created_at' => '2026-07-20 20:58:38',
                'updated_at' => '2026-07-20 22:45:04',
            ],
            [
                'id' => 2,
                'jenis_pembayaran' => 'Biaya Lisensi Instalasi',
                'tgl_invoice' => '2026-07-21',
                'tgl_lunas' => null,
                'status' => 'unpaid',
                'jumlah' => 1000000.00,
                'user_id' => 1,
                'created_at' => '2026-07-20 21:34:33',
                'updated_at' => '2026-07-20 21:34:33',
            ],
        ]);
    }
}