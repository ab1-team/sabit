<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TenantApplyIndexes extends Command
{
    protected $signature = 'tenant:apply-indexes {--tenant=}';
    protected $description = 'Apply performance index migration to all tenant databases';

    private array $indexes = [
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

        ['rekening', 'rekening_lev1_index', ['lev1']],
        ['rekening', 'rekening_lev2_index', ['lev2']],
        ['rekening', 'rekening_tgl_nonaktif_index', ['tgl_nonaktif']],

        ['saldo', 'saldo_kode_akun_tahun_bulan_index', ['kode_akun', 'tahun', 'bulan']],

        ['jurusan', 'jurusan_kode_jurusan_index', ['kode_jurusan']],
        ['kelas', 'kelas_kode_kurikulum_index', ['kode_kurikulum']],
        ['ruangan', 'ruangan_status_index', ['status']],

        ['lp_menu', 'lp_menu_position_active_sort_index', ['position', 'is_active', 'sort_order']],
        ['lp_menu', 'lp_menu_parent_id_index', ['parent_id']],
        ['lp_posts', 'lp_posts_published_featured_index', ['is_published', 'is_featured', 'published_at']],
        ['lp_galleries', 'lp_galleries_album_published_index', ['album', 'is_published']],
        ['lp_pengumuman', 'lp_pengumuman_published_at_index', ['published_at', 'is_published']],
        ['lp_pesan_kontak', 'lp_pesan_kontak_is_read_index', ['is_read', 'created_at']],

        ['master_arus_kas', 'mak_parent_id_index', ['parent_id']],
    ];

    public function handle(): int
    {
        $tenants = $this->option('tenant')
            ? Tenant::where('id', $this->option('tenant'))->get()
            : Tenant::all();

        $base = config('database.connections.tenant_template');
        $prefix = config('tenancy.database.prefix', 'sabit_');
        $suffix = config('tenancy.database.suffix', '');

        $totalOk = 0;
        $totalSkip = 0;
        $totalFail = 0;

        foreach ($tenants as $t) {
            $connName = "tenant_idx_{$t->id}";
            $dbName = $prefix . $t->id . $suffix;

            Config::set("database.connections.{$connName}", array_merge($base, [
                'database' => $dbName,
            ]));
            DB::purge($connName);

            $this->line("[{$t->id}] target DB: {$dbName}");

            try {
                DB::connection($connName)->getPdo();
            } catch (\Throwable $e) {
                $this->warn("  [skip] cannot connect: " . $e->getMessage());
                $totalSkip++;
                continue;
            }

            $ok = 0; $skip = 0; $fail = 0;
            foreach ($this->indexes as [$table, $name, $cols]) {
                try {
                    $exists = collect(DB::connection($connName)->select(
                        "SHOW INDEX FROM `{$table}` WHERE Key_name = ?", [$name]
                    ))->isNotEmpty();

                    if ($exists) {
                        $skip++;
                        continue;
                    }

                    $colList = implode(', ', array_map(fn($c) => "`{$c}`", $cols));
                    $sql = "ALTER TABLE `{$table}` ADD INDEX `{$name}` ({$colList})";
                    DB::connection($connName)->statement($sql);
                    $ok++;
                } catch (\Throwable $e) {
                    $msg = $e->getMessage();
                    if (str_contains($msg, "doesn't exist") || str_contains($msg, "1146")) {
                        $skip++;
                    } else {
                        $this->line("    [WARN] {$table}.{$name}: " . $msg);
                        $fail++;
                    }
                }
            }

            $this->info("  OK={$ok} skip={$skip} fail={$fail}");
            $totalOk += $ok; $totalSkip += $skip; $totalFail += $fail;

            DB::purge($connName);
        }

        $this->info("Summary: OK={$totalOk} skip={$totalSkip} fail={$totalFail}");
        return $totalFail === 0 ? 0 : 1;
    }
}
