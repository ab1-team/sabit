<?php

namespace Database\Seeders;

use App\Models\AkunLevel1;
use App\Models\AkunLevel2;
use App\Models\AkunLevel3;
use App\Models\Profil;
use App\Models\Rekening;
use App\Models\TahunAkademik;
use App\Models\TandaTangan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TenantDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = tenant();

        // Model Tenant memakai kolom DB nyata (nama_sekolah, email), bukan
        // virtual column 'data', sehingga diakses langsung sebagai atribut.
        $namaSekolah = $tenant?->nama_sekolah ?: 'Sekolah';
        $emailAdmin  = $tenant?->email;

        // Menu default harus di-seed PALING AWAL agar kumpulan ID menu yang
        // dipakai untuk hak_akses user di bawah sudah ter-populasi. Jika
        // dipanggil setelah user dibuat, semua user akan dapat hak_akses = [].
        $this->call([
            MenuSeeder::class,
            MenuStructureSeeder::class,
        ]);

        // Profil sekolah (tenant)
        Profil::firstOrCreate(['nama' => $namaSekolah], [
            'alamat' => null,
            'telpon' => null,
            'email' => $emailAdmin,
            'jatuh_tempo' => 10,
        ]);

        // Kumpulan ID menu tersedia. Dipakai sebagai sumber tunggal agar konsisten
        // untuk ketiga user default di bawah ini.
        $allMenuIds = DB::table('menu')
            ->where('status', 'aktif')
            ->pluck('id')
            ->map(fn ($v) => (int) $v)
            ->all();

        $landingMenuIds = DB::table('menu')
            ->where('group', 'landing')
            ->where('status', 'aktif')
            ->pluck('id')
            ->map(fn ($v) => (int) $v)
            ->all();

        // ID menu bendahara: semua menu aktif KECUALI grup landing.
        $bendaharaMenuIds = array_values(array_diff($allMenuIds, $landingMenuIds));

        // Default operator sekolah (login pakai tabel users tenant)
        // hak_akses disimpan sebagai ID menu eksplisit (array), bukan wildcard.
        User::firstOrCreate(
            ['username' => 'admin'],
            [
                'nama'      => 'Administrator',
                'id_jabatan' => 3,
                'email'     => $emailAdmin ?? 'admin@local.test',
                'password'  => Hash::make('password'),
                'hak_akses' => array_values($allMenuIds),
            ]
        );

        // Bendahara: akses semua menu kecuali grup landing.
        User::firstOrCreate(
            ['username' => 'bendahara'],
            [
                'nama'      => 'Bendahara',
                'id_jabatan' => 2,
                'email'     => 'bendahara@local.test',
                'password'  => Hash::make('password'),
                'hak_akses' => $bendaharaMenuIds,
            ]
        );

        // Administrator khusus landing page: hanya menu grup landing.
        // Login hanya dari domain landing (lihat EnsureDomainType + EnsureHakAkses).
        User::firstOrCreate(
            ['username' => 'landing'],
            [
                'nama'      => 'Administrator Landing',
                'id_jabatan' => 5,
                'email'     => 'landing@local.test',
                'password'  => Hash::make('password'),
                'hak_akses' => array_values($landingMenuIds),
            ]
        );

        // Tahun akademik
        $ta = TahunAkademik::firstOrCreate(['nama_tahun' => date('Y').'/'.(date('Y')+1)], [
            'keterangan' => 'Tahun Pelajaran',
            'status'     => 'aktif',
        ]);

        // Paket data master: ruangan, kelas, jurusan, jenis_transaksi, jenis_biaya,
        // jenis_pembayaran, jenis_laporan, sub_laporan, jabatan.
        $this->call([
            RuanganSeeder::class,
            KelasSeeder::class,
            JurusanSeeder::class,
            JenisTransaksiSeeder::class,
            JenisPembayaranSeeder::class,
            JenisBiayaSeeder::class,
            JenisLaporanSeeder::class,
            SubLaporansSeeder::class,
            JabatanSeeder::class,
        ]);

        // COA template lengkap: L1 (kategori), L2 (kelompok), L3 (subkelompok),
        // dan rekening detail per L3 (Kas Tunai, Bank, Piutang SPP, dll).
        $this->call([
            AkunLevel1Seeder::class,
            AkunLevel2Seeder::class,
            AkunLevel3Seeder::class,
            RekeningSeeder::class,
        ]);

        // Backfill rekening generik untuk setiap akun_level3 yang belum punya
        // rekening sama sekali (parent_id = id L3). Kode rekening sama dengan
        // kode_akun L3 agar mudah di-lookup.
        $l3Rows = AkunLevel3::orderBy('id')->get();
        foreach ($l3Rows as $l3) {
            $hasAny = Rekening::where('parent_id', $l3->id)->exists();
            if ($hasAny) {
                continue;
            }
            Rekening::firstOrCreate(
                ['kode_akun' => $l3->kode_akun],
                [
                    'parent_id'    => $l3->id,
                    'lev1'         => $l3->lev1,
                    'lev2'         => $l3->lev2,
                    'lev3'         => $l3->lev3,
                    'nama_akun'    => $l3->nama_akun,
                    'jenis_mutasi' => $l3->jenis_mutasi,
                    'saldo'        => 0,
                ]
            );
        }

        // Pastikan akun minimum (Kas & SPP) selalu ada, untuk kompatibilitas data lama.
        $l1Kas = AkunLevel1::firstOrCreate(['kode_akun' => '1.0.00.00'], ['nama_akun' => 'Aset', 'lev1' => 1]);
        $l1Pendapatan = AkunLevel1::firstOrCreate(['kode_akun' => '4.0.00.00'], ['nama_akun' => 'Pendapatan', 'lev1' => 4]);
        $l2Kas = AkunLevel2::firstOrCreate(['kode_akun' => '1.1.00.00'], [
            'nama_akun' => 'Kas & Bank', 'parent_id' => $l1Kas->id, 'lev1' => 1, 'lev2' => 1,
        ]);
        $l3Kas = AkunLevel3::firstOrCreate(['kode_akun' => '1.1.01.00'], [
            'nama_akun' => 'Kas', 'parent_id' => $l2Kas->id, 'lev1' => 1, 'lev2' => 1, 'lev3' => 1,
        ]);
        $l2Spp = AkunLevel2::firstOrCreate(['kode_akun' => '4.1.00.00'], [
            'nama_akun' => 'Pendapatan SPP', 'parent_id' => $l1Pendapatan->id, 'lev1' => 4, 'lev2' => 1,
        ]);
        $l3Spp = AkunLevel3::firstOrCreate(['kode_akun' => '4.1.01.00'], [
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

        TandaTangan::firstOrCreate([], [
            'TandaTangan' => '<table class="p0" border="0" width="100%" cellspacing="0" cellpadding="0" style="font-size: 11px;">
<tbody>
<tr>
<td style="width: 33.3333%;">&nbsp;</td>
<td style="width: 33.3333%;">&nbsp;</td>
<td style="width: 33.3333%; text-align: center;">' . $namaSekolah . ', {tanggal}</td>
</tr>
</tbody>
<tbody>
<tr>
<td style="text-align: center;">Diperiksa Oleh</td>
<td style="text-align: center;">Diketahui</td>
<td style="text-align: center;">Dilaporkan</td>
</tr>
<tr>
<td style="text-align: center;">
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
</td>
<td style="text-align: center;">&nbsp;</td>
<td style="text-align: center;">&nbsp;</td>
</tr>
<tr>
<td style="text-align: center;">..........rrr.....rrr.............</td>
<td style="text-align: center;">...............................................</td>
<td style="text-align: center;"><strong>......................................</strong></td>
</tr>
<tr>
<td style="text-align: center;"><strong>Badan Pengawas</strong></td>
<td style="text-align: center;"><strong>Manager DBM</strong></td>
<td style="text-align: center;"><strong>Bendahara</strong></td>
</tr>
<tr>
<td style="text-align: center;">Disetujui Oleh</td>
<td style="text-align: center;" colspan="2">&nbsp;</td>
</tr>
<tr>
<td style="text-align: center;">
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
</td>
<td style="text-align: center;">&nbsp;</td>
<td style="text-align: center;">&nbsp;</td>
</tr>
<tr>
<td style="text-align: center;"><strong>......................................</strong></td>
<td style="text-align: center;" colspan="2">&nbsp;</td>
</tr>
<tr>
<td style="text-align: center;"><strong>Direktur</strong></td>
<td style="text-align: center;" colspan="2">&nbsp;</td>
</tr>
</tbody>
</table>',
        ]);

        // Konten default landing page publik sekolah (lp_* tables) agar tenant
        // baru langsung punya website tampil rapi, bukan halaman kosong.
        $this->call([
            LandingPageSeeder::class,
        ]);
    }
}

