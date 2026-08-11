<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TenantRekeningSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('admin_rekening')->insertOrIgnore([
            ['id' => '111', 'kd_rekening' => '111.1002', 'nama_rekening' => 'Terima dari Bank SDITAT', 'pasangan' => '121.2001'],
            ['id' => '121', 'kd_rekening' => '121.2001', 'nama_rekening' => 'Terima Kas Tunai', 'pasangan' => '111.1002'],
            ['id' => '111', 'kd_rekening' => '111.1001', 'nama_rekening' => 'Terima dari Lembaga via Kas', 'pasangan' => '411.1001'],
            ['id' => '111', 'kd_rekening' => '111.2010', 'nama_rekening' => 'Biaya Penyelenggaraan Bimtek', 'pasangan' => '511.2009'],
        ]);
    }
}