<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TenantAdminUserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('admin_user')->insertOrIgnore([
            [
                'id' => 1,
                'nama_lengkap' => 'Administrator',
                'email' => 'admin@gmail.com',
                'password' => '$2y$12$o7AB2.OF0UWFGnCFP/Xdqu.NECJTLb.VP9uA0qY12fGyyMaCkNpum',
                'akses' => 'master',
                'created_at' => null,
                'updated_at' => null,
            ],
        ]);
    }
}
