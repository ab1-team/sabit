<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class BenchmarkQueries extends Command
{
    protected $signature = 'audit:benchmark {--db=sabit_demo} {--iter=3}';
    protected $description = 'Benchmark query SQL kritis pada DB tenant';

    public function handle(): int
    {
        $db = $this->option('db');
        $iter = (int) $this->option('iter');

        Config::set('database.connections.bench', [
            'driver' => 'mysql',
            'host' => config('database.connections.central.host'),
            'port' => config('database.connections.central.port'),
            'database' => $db,
            'username' => config('database.connections.central.username'),
            'password' => config('database.connections.central.password'),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'strict' => true,
        ]);
        DB::purge('bench');
        $c = DB::connection('bench');

        $tests = [
            'rekening_full_load' => fn () => $c->table('rekening')->whereNull('tgl_nonaktif')->get(['kode_akun', 'nama_akun']),
            'rekening_by_lev1' => fn () => $c->table('rekening')->where('lev1', '1')->get(['kode_akun', 'nama_akun']),
            'menu_by_group_status' => fn () => $c->table('menu')->where('status', 'aktif')->where('group', 'admin')->orderBy('urutan')->get(),
            'saldo_per_account' => fn () => $c->table('saldo')->where('tahun', '2026')->where('bulan', 8)->get(),
            'anggota_kelas_by_kelas_status' => fn () => $c->table('anggota_kelas')->where('kode_kelas', 'X-1')->where('status', 'aktif')->get(),
            'spp_by_anggota_kelas' => fn () => $c->table('spp')->where('anggota_kelas', 1)->where('status', 'B')->get(),
            'transaksi_by_rek_debit' => fn () => $c->table('transaksi')->where('rekening_debit', '1.1.01')->whereNull('deleted_at')->limit(100)->get(['id', 'tanggal_transaksi', 'jumlah']),
            'transaksi_by_rek_kredit' => fn () => $c->table('transaksi')->where('rekening_kredit', '1.1.01')->whereNull('deleted_at')->limit(100)->get(['id', 'tanggal_transaksi', 'jumlah']),
            'transaksi_jurnal_umum' => fn () => $c->table('transaksi')->where('kode_spp', '0')->where('siswa_id', 0)->whereNull('deleted_at')->orderByDesc('tanggal_transaksi')->limit(50)->get(),
            'aggregate_rek_debit_year' => fn () => $c->table('transaksi')->where('rekening_debit', '1.1.01')->whereNull('deleted_at')->whereYear('tanggal_transaksi', '2026')->selectRaw('SUM(jumlah) as total')->value('total'),
            'siswa_by_status_tahun_kelas' => fn () => $c->table('siswa')->where('status_siswa', 'aktif')->where('tahun_akademik', '2025/2026')->where('kode_kelas', 'X-1')->limit(20)->get(['id', 'nama']),
        ];

        foreach ($tests as $name => $fn) {
            $this->line("");
            $this->info("=== $name ===");

            try {
                $explain = $c->select("EXPLAIN " . $this->getExplainSql($name, $c));
                $this->line("  EXPLAIN:");
                foreach ($explain as $row) {
                    $cols = [];
                    foreach ((array) $row as $k => $v) {
                        if (in_array($k, ['select_type','table','type','possible_keys','key','key_len','ref','rows','Extra','id','partitions'])) {
                            $cols[] = "$k=$v";
                        }
                    }
                    $this->line("    " . implode(' ', $cols));
                }
            } catch (\Throwable $e) {
                $this->line("  EXPLAIN err: " . $e->getMessage());
            }

            $times = [];
            for ($i = 0; $i < $iter; $i++) {
                $start = microtime(true);
                $fn();
                $times[] = (microtime(true) - $start) * 1000;
            }
            $avg = array_sum($times) / count($times);
            $min = min($times);
            $max = max($times);
            $this->line(sprintf("  Time: avg=%.2fms min=%.2fms max=%.2fms (n=%d)", $avg, $min, $max, $iter));
        }

        return 0;
    }

    private function getExplainSql(string $name, $c): string
    {
        switch ($name) {
            case 'rekening_full_load':
                return "SELECT kode_akun, nama_akun FROM rekening WHERE tgl_nonaktif IS NULL";
            case 'rekening_by_lev1':
                return "SELECT kode_akun, nama_akun FROM rekening WHERE lev1 = '1'";
            case 'menu_by_group_status':
                return "SELECT * FROM menu WHERE status = 'aktif' AND `group` = 'admin' ORDER BY urutan";
            case 'saldo_per_account':
                return "SELECT * FROM saldo WHERE tahun = '2026' AND bulan = 8";
            case 'anggota_kelas_by_kelas_status':
                return "SELECT * FROM anggota_kelas WHERE kode_kelas = 'X-1' AND status = 'aktif'";
            case 'spp_by_anggota_kelas':
                return "SELECT * FROM spp WHERE anggota_kelas = 1 AND status = 'B'";
            case 'transaksi_by_rek_debit':
                return "SELECT id, tanggal_transaksi, jumlah FROM transaksi WHERE rekening_debit = '1.1.01' AND deleted_at IS NULL LIMIT 100";
            case 'transaksi_by_rek_kredit':
                return "SELECT id, tanggal_transaksi, jumlah FROM transaksi WHERE rekening_kredit = '1.1.01' AND deleted_at IS NULL LIMIT 100";
            case 'transaksi_jurnal_umum':
                return "SELECT * FROM transaksi WHERE kode_spp = '0' AND siswa_id = 0 AND deleted_at IS NULL ORDER BY tanggal_transaksi DESC LIMIT 50";
            case 'aggregate_rek_debit_year':
                return "SELECT SUM(jumlah) as total FROM transaksi WHERE rekening_debit = '1.1.01' AND deleted_at IS NULL AND YEAR(tanggal_transaksi) = '2026'";
            case 'siswa_by_status_tahun_kelas':
                return "SELECT id, nama FROM siswa WHERE status_siswa = 'aktif' AND tahun_akademik = '2025/2026' AND kode_kelas = 'X-1' LIMIT 20";
        }
        return "SELECT 1";
    }
}
