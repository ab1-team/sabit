<?php

namespace Database\Seeders;

use App\Models\AkunLevel1;
use App\Models\AkunLevel2;
use App\Models\AkunLevel3;
use App\Models\JenisLaporan;
use App\Models\JenisPembayaran;
use App\Models\Kelas;
use App\Models\Menu;
use App\Models\Profil;
use App\Models\Rekening;
use App\Models\Ruangan;
use App\Models\SubLaporan;
use App\Models\Tahun_Akademik;
use App\Models\Tanda_tangan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TenantDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = tenant();

        $namaSekolah = $tenant?->data['nama'] ?? 'Sekolah';
        $emailAdmin  = $tenant?->data['email'] ?? null;

        // Profil sekolah (tenant)
        Profil::firstOrCreate(['nama' => $namaSekolah], [
            'alamat' => null,
            'telpon' => null,
            'email' => $emailAdmin,
            'jatuh_tempo' => 10,
        ]);

        // Default operator sekolah (login pakai tabel users tenant)
        User::firstOrCreate(
            ['username' => 'admin'],
            [
                'nama'      => 'Administrator',
                'email'     => $emailAdmin ?? 'admin@local.test',
                'password'  => Hash::make('password'),
                'hak_akses' => ['*'],
            ]
        );

        // Tahun akademik
        $ta = Tahun_Akademik::firstOrCreate(['nama_tahun' => date('Y').'/'.(date('Y')+1)], [
            'keterangan' => 'Tahun Pelajaran',
            'status'     => 'aktif',
        ]);

        // Default ruangan & kelas
        $ruang = Ruangan::firstOrCreate(['nama_ruangan' => 'Ruang Kelas'], [
            'kode_gedung'        => 'G1',
            'kode_ruangan'       => 'R1',
            'kapasitas_belajar'  => '36',
            'kapasitas_ujian'    => '36',
            'keterangan'         => 'Ruang Kelas',
            'status'             => 'aktif',
        ]);
        Kelas::firstOrCreate(['nama_kelas' => 'X IPA 1'], [
            'kode_kelas'     => 'X-IPA-1',
            'tingkat'        => '10',
            'kode_kurikulum' => 'K-IPA',
        ]);

        // Jenis pembayaran
        JenisPembayaran::firstOrCreate(['nama' => 'SPP'], [
            'kode_akun' => '4.1.01',
            'jumlah'    => '0',
        ]);
        JenisPembayaran::firstOrCreate(['nama' => 'Ujian'], [
            'kode_akun' => '4.1.02',
            'jumlah'    => '0',
        ]);

        // Jenis laporan + sub laporan
        $jl = JenisLaporan::firstOrCreate(['nama' => 'Bulanan'], [
            'file'  => 'bulanan',
            'urut'  => 1,
        ]);
        SubLaporan::firstOrCreate(['id_lap' => $jl->id, 'nama_laporan' => 'Kas'], [
            'file' => 'kas',
            'urut' => 1,
        ]);

        // COA template
        $l1Kas = AkunLevel1::firstOrCreate(['kode_akun' => '1.0.00.00'], ['nama_akun' => 'Aset', 'lev1' => 1]);
        $l1Pendapatan = AkunLevel1::firstOrCreate(['kode_akun' => '4.0.00.00'], ['nama_akun' => 'Pendapatan', 'lev1' => 4]);
        $l2Kas = AkunLevel2::firstOrCreate(['kode_akun' => '1.1.00.00'], [
            'nama_akun' => 'Kas & Bank', 'parent_id' => $l1Kas->id, 'lev1' => 1, 'lev2' => 1,
        ]);
        $l3Kas = AkunLevel3::firstOrCreate(['kode_akun' => '1.1.01'], [
            'nama_akun' => 'Kas', 'parent_id' => $l2Kas->id, 'lev1' => 1, 'lev2' => 1, 'lev3' => 1,
        ]);
        $l2Spp = AkunLevel2::firstOrCreate(['kode_akun' => '4.1.00.00'], [
            'nama_akun' => 'Pendapatan SPP', 'parent_id' => $l1Pendapatan->id, 'lev1' => 4, 'lev2' => 1,
        ]);
        $l3Spp = AkunLevel3::firstOrCreate(['kode_akun' => '4.1.01'], [
            'nama_akun' => 'SPP', 'parent_id' => $l2Spp->id, 'lev1' => 4, 'lev2' => 1, 'lev3' => 1,
        ]);

        Rekening::firstOrCreate(['kode_akun' => $l3Kas->kode_akun], [
            'parent_id'    => $l3Kas->id,
            'lev1'         => 1, 'lev2' => 1, 'lev3' => 1,
            'nama_akun'    => $l3Kas->nama_akun,
            'jenis_mutasi' => 'Debet',
            'saldo'        => 0,
        ]);
        Rekening::firstOrCreate(['kode_akun' => $l3Spp->kode_akun], [
            'parent_id'    => $l3Spp->id,
            'lev1'         => 4, 'lev2' => 1, 'lev3' => 1,
            'nama_akun'    => $l3Spp->nama_akun,
            'jenis_mutasi' => 'Kredit',
            'saldo'        => 0,
        ]);

        Tanda_tangan::firstOrCreate([], [
            'tanda_tangan' => 'Tanda tangan Kepala Sekolah - ' . $namaSekolah,
        ]);

        // Jabatan default
        DB::table('jabatan')->insertOrIgnore([
            ['nama_jabatan' => 'Kepala Sekolah', 'kode_jabatan' => 'KS', 'created_at' => now(), 'updated_at' => now()],
            ['nama_jabatan' => 'Bendahara',     'kode_jabatan' => 'BD', 'created_at' => now(), 'updated_at' => now()],
            ['nama_jabatan' => 'Operator',      'kode_jabatan' => 'OP', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Menu default
        $menuItems = [
            ['id' => 1, 'nama_menu' => 'Dashboard', 'route' => 'app.dashboard', 'icon' => 'dashboard', 'group' => 'Utama', 'urutan' => 1, 'status' => 'aktif'],
            ['id' => 2, 'nama_menu' => 'Transaksi', 'route' => 'Transaksi.index', 'icon' => 'money', 'group' => 'Utama', 'urutan' => 2, 'status' => 'aktif'],
            ['id' => 3, 'nama_menu' => 'Siswa', 'route' => 'siswa.index', 'icon' => 'students', 'group' => 'Akademik', 'urutan' => 3, 'status' => 'aktif'],
            ['id' => 4, 'nama_menu' => 'Daftar Kelas', 'route' => 'daftar-kelas.index', 'icon' => 'class', 'group' => 'Akademik', 'urutan' => 4, 'status' => 'aktif'],
            ['id' => 5, 'nama_menu' => 'Pengaturan', 'route' => 'pengaturan.index', 'icon' => 'settings', 'group' => 'Sistem', 'urutan' => 5, 'status' => 'aktif'],
            ['id' => 6, 'nama_menu' => 'Laporan Keuangan', 'route' => 'laporan-keuangan.index', 'icon' => 'report', 'group' => 'Laporan', 'urutan' => 6, 'status' => 'aktif'],
            ['id' => 7, 'nama_menu' => 'Hak Akses', 'route' => 'app.hak-akses', 'icon' => 'shield', 'group' => 'Sistem', 'urutan' => 7, 'status' => 'aktif'],
        ];

        foreach ($menuItems as $m) {
            Menu::firstOrCreate(['id' => $m['id']], $m);
        }
    }
}
