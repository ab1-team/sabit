<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            [1, 'Beranda', '/app/dashboard', 'dashboard', 1, 'aktif', null],
            [2, 'Personalisasi SOP', '/app/pengaturan/sop', 'settings', 2, 'aktif', 'Pengaturan'],
            [3, 'Bagan Akun (CoA)', '/app/pengaturan/coa', 'account_tree', 3, 'aktif', 'Pengaturan'],
            [4, 'Jenis Pembayaran', '/app/jenis-biaya', 'payments', 4, 'aktif', 'Pengaturan'],
            [5, 'Tanda Tangan Pelaporan', '/app/pengaturan/ttd-pelaporan', 'draw', 5, 'aktif', 'Pengaturan'],
            [6, 'Invoice', '/app/pengaturan/invoice', 'receipt', 6, 'aktif', 'Pengaturan'],
            [7, 'Tambah Siswa', '/app/siswa/create', 'person_add', 7, 'aktif', 'Master Data'],
            [8, 'Data Siswa', '/app/siswa', 'school', 8, 'aktif', 'Master Data'],
            [9, 'Tagihan Siswa', '/app/transaksi/pembayaran-spp', 'request_quote', 9, 'aktif', 'Transaksi'],
            [10, 'Jurnal Umum', '/app/transaksi', 'menu_book', 10, 'aktif', 'Transaksi'],
            [11, 'Laporan Keuangan', '/app/laporan-keuangan', 'receipt_long', 11, 'aktif', 'Pelaporan'],
            [14, 'Daftar Kelas', '/app/daftar-kelas', 'class', 14, 'aktif', 'Master Data'],
        ];

        $data = array_map(function ($r) {
            return [
                'id' => $r[0],
                'nama_menu' => $r[1],
                'route' => $r[2],
                'icon' => $r[3],
                'urutan' => $r[4],
                'status' => $r[5],
                'group' => $r[6],
                'created_at' => null,
                'updated_at' => null,
            ];
        }, $rows);

        DB::table('menu')->insertOrIgnore($data);
    }
}
