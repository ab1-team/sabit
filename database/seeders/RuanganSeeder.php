<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RuanganSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['G001', 'R001', 'Ruang Kepala Sekolah', '0', '0', 'Lantai I', ''],
            ['G001', 'R002', 'Ruang Tata Usaha', '0', '0', 'Lantai I', ''],
            ['G003', 'R07', 'Kelas II.C', '30', '30', 'Lantai II', ''],
            ['G003', 'R08', 'Kelas II.D', '31', '31', 'Lantai II', ''],
            ['G001', 'R024', 'Lab. Komputer', '20', '20', 'Lantai II', ''],
            ['G001', 'R005', 'Ruang Sekretariat', '0', '0', 'Lantai II', ''],
            ['G002', 'R006', 'Ruang Waka Kurikulum dan Kesiswaan', '0', '0', 'Lantai I', ''],
            ['G002', 'R007', 'UKS', '0', '0', 'Lantai I', ''],
            ['G003', 'R01', 'Kelas I. A', '26', '26', 'Lantai I', ''],
            ['G003', 'R02', 'Kelas I. B', '25', '25', 'Lantai I', ''],
            ['G003', 'R03', 'Kelas I. C', '27', '27', 'Lantai I', ''],
            ['G003', 'R04', 'Kelas I. D', '26', '26', 'Lantai I', ''],
            ['G003', 'R05', 'Kelas II. A', '29', '29', 'Lantai I', ''],
            ['G003', 'R06', 'Kelas II.B', '30', '30', 'Lantai II', ''],
            ['G005', 'R17', 'Kelas V. B', '31', '31', 'Lantai II', ''],
            ['G005', 'R16', 'Kelas V. A', '31', '31', 'Lantai I', ''],
            ['G001', 'R11', 'Kelas III. C', '23', '23', 'Lantai II', ''],
            ['G003', 'R10', 'Kelas III. B', '25', '25', 'Lantai II', ''],
            ['G003', 'R09', 'Kelas III. A', '24', '24', 'Lantai I', ''],
            ['G005', 'R14', 'Kelas IV.B', '27', '27', 'Lantai I', ''],
            ['G005', 'R15', 'Kelas IV. C', '30', '30', 'Lantai I', ''],
            ['G005', 'R13', 'Kelas IV. A', '29', '27', 'Lantai I', ''],
            ['G005', 'R023', 'Perpustakaan', '0', '0', 'Lantai II', ''],
            ['G003', 'R18', 'Kelas VI.A', '28', '28', 'Lantai II', ''],
            ['G003', 'R19', 'Kelas VI.B', '28', '28', 'Lantai II', ''],
            ['G001', 'R12', 'Kelas III. D', '24', '24', 'Lantai II', ''],
            ['G005', 'R20', 'Kelas IV. D', '30', '30', 'Lantai I', ''],
        ];

        $data = [];
        foreach ($rows as $i => $r) {
            $data[] = [
                'id' => $i + 1,
                'kode_gedung' => $r[0],
                'kode_ruangan' => $r[1],
                'nama_ruangan' => $r[2],
                'kapasitas_belajar' => $r[3],
                'kapasitas_ujian' => $r[4],
                'keterangan' => $r[5],
                'status' => 'aktif',
                'created_at' => '2026-07-20 04:51:56',
                'updated_at' => '2026-07-20 04:51:56',
            ];
        }

        DB::table('ruangan')->insertOrIgnore($data);
    }
}
