<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insertOrIgnore([
            [
                'id' => 3,
                'nama' => 'Administrator',
                'nik' => '1',
                'id_jabatan' => 3,
                'jenis_kelamin' => 'L',
                'telepon' => '08123456789',
                'alamat' => 'Sragen',
                'foto' => '1784337372_images.jpg',
                'email' => 'admin@local',
                'username' => 'admin',
                'password' => '$2y$12$o7AB2.OF0UWFGnCFP/Xdqu.NECJTLb.VP9uA0qY12fGyyMaCkNpum',
                'remember_token' => null,
                'created_at' => '2026-07-14 22:29:55',
                'updated_at' => '2026-07-17 18:16:12',
            ],
            [
                'id' => 4,
                'nama' => 'Administrator',
                'nik' => '1',
                'id_jabatan' => 2,
                'jenis_kelamin' => 'L',
                'telepon' => '',
                'alamat' => 'Sragen',
                'foto' => '',
                'email' => 'admin@local',
                'username' => 'Bendahara',
                'password' => '$2y$12$o7AB2.OF0UWFGnCFP/Xdqu.NECJTLb.VP9uA0qY12fGyyMaCkNpum',
                'remember_token' => null,
                'created_at' => '2026-07-14 22:29:55',
                'updated_at' => '2026-07-17 18:16:12',
            ],
        ]);
    }
}
