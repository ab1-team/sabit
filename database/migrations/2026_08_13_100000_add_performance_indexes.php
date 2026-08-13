<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    private function addIndex(string $table, string $index, string|array $cols, bool $unique = false): void
    {
        if (!Schema::hasTable($table)) return;

        $exists = collect(DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = ?", [$index]))->isNotEmpty();
        if ($exists) return;

        $colList = is_array($cols) ? implode(', ', array_map(fn ($c) => "`{$c}`", $cols)) : "`{$cols}`";
        $type = $unique ? 'UNIQUE INDEX' : 'INDEX';
        $sql = "ALTER TABLE `{$table}` ADD {$type} `{$index}` ({$colList})";
        DB::statement($sql);
    }

    private function dropIndex(string $table, string $index): void
    {
        if (!Schema::hasTable($table)) return;
        $exists = collect(DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = ?", [$index]))->isNotEmpty();
        if (!$exists) return;
        DB::statement("ALTER TABLE `{$table}` DROP INDEX `{$index}`");
    }

    public function up(): void
    {
        $idx = [
            ['transaksi', 'transaksi_rek_debit_tgl_index', ['rekening_debit', 'tanggal_transaksi']],
            ['transaksi', 'transaksi_rek_kredit_tgl_index', ['rekening_kredit', 'tanggal_transaksi']],
            ['transaksi', 'transaksi_siswa_id_tgl_index', ['siswa_id', 'tanggal_transaksi']],
            ['transaksi', 'transaksi_idtp_index', ['idtp']],
            ['transaksi', 'transaksi_kode_spp_index', ['kode_spp']],
            ['transaksi', 'transaksi_kode_spp_deleted_at_index', ['kode_spp', 'deleted_at']],
            ['transaksi', 'transaksi_user_id_index', ['user_id']],
            ['transaksi', 'transaksi_deleted_at_tgl_index', ['deleted_at', 'tanggal_transaksi']],
            ['transaksi', 'transaksi_jurnal_umum_index', ['kode_spp', 'siswa_id', 'deleted_at', 'tanggal_transaksi']],
            ['transaksi', 'transaksi_rek_kredit_rek_debit_tgl_index', ['rekening_kredit', 'rekening_debit', 'tanggal_transaksi']],
            ['transaksi', 'transaksi_tgl_rek_debit_index', ['tanggal_transaksi', 'rekening_debit']],
            ['transaksi', 'transaksi_tgl_rek_kredit_index', ['tanggal_transaksi', 'rekening_kredit']],

            ['spp', 'spp_anggota_kelas_index', ['anggota_kelas']],
            ['spp', 'spp_status_index', ['status']],
            ['spp', 'spp_tanggal_index', ['tanggal']],
            ['spp', 'spp_tgl_lunas_index', ['tgl_lunas']],
            ['spp', 'spp_anggota_kelas_tgl_index', ['anggota_kelas', 'tanggal']],
            ['spp', 'spp_anggota_kelas_status_tgl_index', ['anggota_kelas', 'status', 'tanggal']],
            ['spp', 'spp_status_tgl_index', ['status', 'tanggal']],

            ['anggota_kelas', 'ak_id_siswa_index', ['id_siswa']],
            ['anggota_kelas', 'ak_status_index', ['status']],
            ['anggota_kelas', 'ak_tahun_akademik_index', ['tahun_akademik']],
            ['anggota_kelas', 'ak_kode_kelas_index', ['kode_kelas']],
            ['anggota_kelas', 'ak_id_siswa_status_index', ['id_siswa', 'status']],
            ['anggota_kelas', 'ak_kode_kelas_tahun_status_index', ['kode_kelas', 'tahun_akademik', 'status']],
            ['anggota_kelas', 'ak_status_kode_kelas_index', ['status', 'kode_kelas']],

            ['siswa', 'siswa_nisn_index', ['nisn']],
            ['siswa', 'siswa_status_siswa_index', ['status_siswa']],
            ['siswa', 'siswa_tahun_akademik_index', ['tahun_akademik']],
            ['siswa', 'siswa_kode_kelas_index', ['kode_kelas']],
            ['siswa', 'siswa_status_tahun_kelas_index', ['status_siswa', 'tahun_akademik', 'kode_kelas']],
            ['siswa', 'siswa_tahun_status_nama_index', ['tahun_akademik', 'status_siswa', 'nama']],

            ['jenis_biaya', 'jb_angkatan_index', ['angkatan']],
            ['jenis_biaya', 'jb_id_jp_angkatan_index', ['id_jp', 'angkatan']],

            ['users', 'users_username_index', ['username']],
            ['users', 'users_email_index', ['email']],

            ['tahun_akademik', 'ta_status_index', ['status']],

            ['menu', 'menu_status_group_urutan_index', ['status', 'group', 'urutan']],
            ['menu', 'menu_parent_id_index', ['parent_id']],

            ['rekening', 'rekening_parent_id_index', ['parent_id']],
            ['rekening', 'rekening_lev1_index', ['lev1']],
            ['rekening', 'rekening_lev2_index', ['lev2']],
            ['rekening', 'rekening_tgl_nonaktif_index', ['tgl_nonaktif']],

            ['saldo', 'saldo_kode_akun_tahun_bulan_index', ['kode_akun', 'tahun', 'bulan']],

            ['inventaris', 'inventaris_jenis_kategori_index', ['jenis', 'kategori']],
            ['inventaris', 'inventaris_tanggal_beli_index', ['tanggal_beli']],

            ['jurusan', 'jurusan_kode_jurusan_index', ['kode_jurusan']],
            ['kelas', 'kelas_kode_kurikulum_index', ['kode_kurikulum']],
            ['ruangan', 'ruangan_status_index', ['status']],

            ['lp_menu', 'lp_menu_position_active_sort_index', ['position', 'is_active', 'sort_order']],
            ['lp_menu', 'lp_menu_parent_id_index', ['parent_id']],
            ['lp_artikel', 'lp_artikel_published_featured_index', ['is_published', 'is_featured', 'published_at']],
            ['lp_galeri', 'lp_galeri_album_published_index', ['album', 'is_published']],
            ['lp_pengumuman', 'lp_pengumuman_published_at_index', ['published_at', 'is_published']],
            ['lp_pesan_kontak', 'lp_pesan_kontak_is_read_index', ['is_read', 'created_at']],

            ['master_arus_kas', 'mak_parent_id_index', ['parent_id']],
        ];

        foreach ($idx as [$table, $name, $cols]) {
            $this->addIndex($table, $name, $cols);
        }

        if (Schema::hasTable('domains')) {
            $this->addIndex('domains', 'domains_domain_type_index', ['domain', 'type']);
            $this->addIndex('domains', 'domains_type_index', ['type']);
        }
    }

    public function down(): void
    {
        $indexes = [
            'transaksi' => [
                'transaksi_rek_debit_tgl_index',
                'transaksi_rek_kredit_tgl_index',
                'transaksi_siswa_id_tgl_index',
                'transaksi_idtp_index',
                'transaksi_kode_spp_index',
                'transaksi_kode_spp_deleted_at_index',
                'transaksi_user_id_index',
                'transaksi_deleted_at_tgl_index',
                'transaksi_jurnal_umum_index',
                'transaksi_rek_kredit_rek_debit_tgl_index',
                'transaksi_tgl_rek_debit_index',
                'transaksi_tgl_rek_kredit_index',
            ],
            'spp' => [
                'spp_anggota_kelas_index',
                'spp_status_index',
                'spp_tanggal_index',
                'spp_tgl_lunas_index',
                'spp_anggota_kelas_tgl_index',
                'spp_anggota_kelas_status_tgl_index',
                'spp_status_tgl_index',
            ],
            'anggota_kelas' => [
                'ak_id_siswa_index',
                'ak_status_index',
                'ak_tahun_akademik_index',
                'ak_kode_kelas_index',
                'ak_id_siswa_status_index',
                'ak_kode_kelas_tahun_status_index',
                'ak_status_kode_kelas_index',
            ],
            'siswa' => [
                'siswa_nisn_index',
                'siswa_status_siswa_index',
                'siswa_tahun_akademik_index',
                'siswa_kode_kelas_index',
                'siswa_status_tahun_kelas_index',
                'siswa_tahun_status_nama_index',
            ],
            'jenis_biaya' => ['jb_angkatan_index', 'jb_id_jp_angkatan_index'],
            'users' => ['users_username_index', 'users_email_index'],
            'tahun_akademik' => ['ta_status_index'],
            'menu' => ['menu_status_group_urutan_index', 'menu_parent_id_index'],
            'rekening' => ['rekening_parent_id_index', 'rekening_lev1_index', 'rekening_lev2_index', 'rekening_tgl_nonaktif_index'],
            'saldo' => ['saldo_kode_akun_tahun_bulan_index'],
            'inventaris' => ['inventaris_jenis_kategori_index', 'inventaris_tanggal_beli_index'],
            'jurusan' => ['jurusan_kode_jurusan_index'],
            'kelas' => ['kelas_kode_kurikulum_index'],
            'ruangan' => ['ruangan_status_index'],
            'lp_menu' => ['lp_menu_position_active_sort_index', 'lp_menu_parent_id_index'],
            'lp_artikel' => ['lp_artikel_published_featured_index'],
            'lp_galeri' => ['lp_galeri_album_published_index'],
            'lp_pengumuman' => ['lp_pengumuman_published_at_index'],
            'lp_pesan_kontak' => ['lp_pesan_kontak_is_read_index'],
            'master_arus_kas' => ['mak_parent_id_index'],
            'domains' => ['domains_domain_type_index', 'domains_type_index'],
        ];

        foreach ($indexes as $table => $list) {
            foreach ($list as $idx) {
                $this->dropIndex($table, $idx);
            }
        }
    }
};
